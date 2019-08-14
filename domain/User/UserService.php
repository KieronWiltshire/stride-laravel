<?php

namespace Domain\User;

use Domain\User\Events\EmailVerificationTokenGeneratedEvent;
use Domain\User\Contracts\Repositories\UserRepository;
use Domain\User\Events\PasswordResetTokenGeneratedEvent;
use Domain\User\Events\UserEmailVerifiedEvent;
use Domain\User\Events\UserPasswordResetEvent;
use Domain\User\Exceptions\CannotCreateUserException;
use Domain\User\Exceptions\CannotUpdateUserException;
use Domain\User\Exceptions\InvalidEmailException;
use Domain\User\Exceptions\InvalidEmailVerificationTokenException;
use Domain\User\Exceptions\InvalidPasswordException;
use Domain\User\Exceptions\InvalidPasswordResetTokenException;
use Domain\User\Exceptions\PasswordResetTokenExpiredException;
use Domain\User\Exceptions\UserNotFoundException;
use Domain\User\Mail\EmailVerificationToken;
use Domain\User\Mail\PasswordResetToken;
use Domain\User\Validators\UserEmailValidator;
use Domain\User\Validators\UserPasswordValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Support\Exceptions\AppError;
use Support\Exceptions\Pagination\InvalidPaginationException;

class UserService
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var UserEmailValidator
     */
    protected $userEmailValidator;

    /**
     * @var UserPasswordValidator
     */
    protected $userPasswordValidator;

    /**
     * Create a new user service instance.
     *
     * @param UserRepository $userRepository
     * @param UserEmailValidator $userEmailValidator
     * @param UserPasswordValidator $userPasswordValidator
     */
    public function __construct(
        UserRepository $userRepository,
        UserEmailValidator $userEmailValidator,
        UserPasswordValidator $userPasswordValidator
    ) {
        $this->userRepository = $userRepository;
        $this->userEmailValidator = $userEmailValidator;
        $this->userPasswordValidator = $userPasswordValidator;
    }

    /**
     * Retrieve all of the users.
     *
     * @return Collection
     */
    public function all()
    {
        return $this->userRepository->with(['roles', 'permissions'])->all();
    }

    /**
     * Create a new user.
     *
     * @param array $attributes
     * @return User
     *
     * @throws CannotCreateUserException
     */
    public function create($attributes)
    {
        if (isset($attributes['email'])) {
            $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
        }

        return $this->userRepository->create($attributes);
    }

    /**
     * Create a user if the specified search parameters could not find one
     * with the matching criteria.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param array $attributes
     * @return User
     */
    public function firstOrCreate($parameter, $search, $regex = true, $attributes = [])
    {
        if (isset($attributes['email'])) {
            $attributes['email_verification_token'] = $this->generateEmailVerificationToken($attributes['email']);
        }

        return $this->userRepository->with(['roles', 'permissions'])->firstOrCreate($parameter, $search, $regex, $attributes);
    }

    /**
     * Find a user by an unknown parameter.
     *
     * @param number|string $parameter
     * @param number|string|array $search
     * @param boolean $regex
     * @return Collection
     */
    public function find($parameter, $search, $regex = true)
    {
        return $this->userRepository->with(['roles', 'permissions'])->find($parameter, $search, $regex);
    }

    /**
     * Find a user by identifier.
     *
     * @param string $id
     * @return User
     */
    public function findById($id)
    {
        return $this->userRepository->with(['roles', 'permissions'])->findById($id);
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return User
     */
    public function findByEmail($email)
    {
        return $this->userRepository->with(['roles', 'permissions'])->findByEmail($email);
    }

    /**
     * Update a user.
     *
     * @param User $user
     * @param array $attributes
     * @return User
     *
     * @throws CannotUpdateUserException
     */
    public function update(User $user, $attributes)
    {
        return $this->userRepository->update($user, $attributes);
    }

    /**
     * Retrieve all of the users.
     *
     * @param integer $limit
     * @param integer $offset
     * @return LengthAwarePaginator
     *
     * @throws InvalidPaginationException
     */
    public function index($limit = null, $offset = 1)
    {
        return $this->userRepository->with(['roles', 'permissions'])->paginate($limit, $offset)->all();
    }

    /**
     * Search for users with the specified search parameters.
     *
     * @param number|string $parameter
     * @param number|string $search
     * @param boolean $regex
     * @param integer $limit
     * @param integer $offset
     * @return LengthAwarePaginator
     *
     * @throws InvalidPaginationException
     */
    public function search($parameter, $search, $regex = true, $limit = null, $offset = 1)
    {
        return $this->userRepository->paginate($limit, $offset)->find($parameter, $search, $regex);
    }

    /**
     * Router a new email verification token be generated with
     * the user's new email address to verify.
     *
     * @param User $user
     * @param string $email
     * @return User
     *
     * @throws CannotUpdateUserException
     * @throws \ReflectionException
     * @throws AppError
     */
    public function requestEmailChange(User $user, $email)
    {
        $this->userEmailValidator->validate([
            'email' => $email
        ]);

        $attributes = [
            'email_verification_token' => $this->generateEmailVerificationToken($email)
        ];

        if ($this->userRepository->update($user, $attributes)) {
            event(new EmailVerificationTokenGeneratedEvent($user));

            return $user;
        }
    }

    /**
     * Verify the user's specified email address and set their
     * email to the new one encoded within the token.
     *
     * @param User $user
     * @param string $emailVerificationToken
     * @return User
     *
     * @throws CannotUpdateUserException
     * @throws InvalidEmailVerificationTokenException
     * @throws \ReflectionException
     * @throws AppError
     */
    public function verifyEmail(User $user, $emailVerificationToken)
    {
        $decodedToken = $this->decodeEmailVerificationToken($emailVerificationToken);

        if ($emailVerificationToken && $decodedToken && $user->email_verification_token == $emailVerificationToken) {
            $this->userEmailValidator->validate([
                'email' => $decodedToken->email
            ]);

            $oldEmail = $user->email;

            $attributes = [
                'email' => $decodedToken->email,
                'email_verified_at' => now(),
                'email_verification_token' => null,
            ];

            if ($this->userRepository->update($user, $attributes)) {
                event(new UserEmailVerifiedEvent($user, $oldEmail));

                return $user;
            }
        } else {
            throw new InvalidEmailVerificationTokenException();
        }
    }

    /**
     * Generate an email verification token for the specified email address.
     *
     * @param string $email
     * @return string
     */
    public function generateEmailVerificationToken($email)
    {
        return base64_encode(json_encode([
            'email' => $email,
            'token' => Str::random(32) // used to append randomness
        ]));
    }

    /**
     * Decode an email verification token.
     *
     * @param string $emailVerificationToken
     * @return Object
     */
    public function decodeEmailVerificationToken($emailVerificationToken)
    {
        return json_decode(base64_decode($emailVerificationToken));
    }

    /**
     * Send the email verification email.
     *
     * @param User $user
     * @return void
     *
     * @throws InvalidEmailVerificationTokenException
     * @throws Exception
     */
    public function sendEmailVerificationToken(User $user)
    {
        if ($user->email_verification_token) {
            $decodedToken = $this->decodeEmailVerificationToken($user->email_verification_token);

            if ($decodedToken) {
                Mail::to($decodedToken->email)->send(new EmailVerificationToken($user->email_verification_token));
            } else {
                throw new Exception();
            }
        } else {
            throw new InvalidEmailVerificationTokenException();
        }
    }

    /**
     * Create's a password reset token for the specified user.
     *
     * @param User $user
     * @return User
     *
     * @throws CannotUpdateUserException
     */
    public function forgotPassword(User $user)
    {
        $attributes = [
            'password_reset_token' => $this->generatePasswordResetToken(),
        ];

        if ($this->userRepository->update($user, $attributes)) {
            event(new PasswordResetTokenGeneratedEvent($user));

            return $user;
        }
    }

    /**
     * Reset a user's password using the password reset token.
     *
     * @param User $user
     * @param string $password
     * @param string $passwordResetToken
     * @return User
     *
     * @throws AppError
     * @throws InvalidPasswordResetTokenException
     * @throws PasswordResetTokenExpiredException
     * @throws \ReflectionException
     * @throws Exception
     */
    public function resetPassword(User $user, $password, $passwordResetToken)
    {
        $decodedToken = $this->decodePasswordResetToken($passwordResetToken);

        if ($passwordResetToken && $decodedToken && $user->password_reset_token == $passwordResetToken) {
            if (Carbon::now()->lessThan(new Carbon($decodedToken->expiry))) {
                $this->userPasswordValidator->validate([
                'password' => $password
            ]);

                $attributes = [
                    'password' => $password,
                    'password_reset_token' => null,
                ];

                if ($this->$this->userRepository->update($user, $attributes)) {
                    event(new UserPasswordResetEvent($user));

                    return $user;
                }
            } else {
                throw new PasswordResetTokenExpiredException();
            }
        } else {
            throw new InvalidPasswordResetTokenException();
        }
    }

    /**
     * Generate a password reset token.
     *
     * @return string
     */
    public function generatePasswordResetToken()
    {
        return base64_encode(json_encode([
            'expiry' => Carbon::now()->addMinutes(config('auth.passwords.users.expire', 60)),
            'token' => Str::random(32) // used to append randomness
        ]));
    }

    /**
     * Decode a password reset token.
     *
     * @param string $passwordResetToken
     * @return Object
     */
    public function decodePasswordResetToken($passwordResetToken)
    {
        return json_decode(base64_decode($passwordResetToken));
    }

    /**
     * Send the user a password reset email.
     *
     * @param User $user
     * @return void
     */
    public function sendPasswordResetToken(User $user)
    {
        if ($user->password_reset_token) {
            Mail::to($user->email)->send(new PasswordResetToken($user->password_reset_token));
        }
    }
}
