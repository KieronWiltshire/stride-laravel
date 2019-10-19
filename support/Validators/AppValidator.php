<?php

namespace Support\Validators;

use Illuminate\Contracts\Validation\Validator;
use Support\Exceptions\AppError;
use Support\Exceptions\Http\ValidationError;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use ReflectionClass;

/**
 * Base Validation class. All entity specific validation classes inherit
 * this class and can override any function for respective specific needs
 */
abstract class AppValidator
{

    /**
     * @var ValidationFactory
     */
    protected $validationFactory;

    /**
     * @var AppError
     */
    protected $exception = ValidationError::class;

    /**
     * Create a new app validator instance.
     *
     * @param ValidationFactory $validationFactory
     */
    public function __construct(ValidationFactory $validationFactory)
    {
        $this->validationFactory = $validationFactory;
    }
    
    /**
     * Method called after instantiation.
     *
     * @return void
     */
    public function boot()
    {}

    /**
     * Retrieve the rules set for the validator.
     *
     * @return array
     */
    abstract public function rules();

    /**
     * Validates the specified data against the defined rule set.
     *
     * @param array $data
     * @param array $rules
     * @param array $customErrors
     * @return Validator
     *
     * @throws \ReflectionException
     */
    public function validate(array $data, array $rules = [], array $customErrors = [])
    {
        if (empty($rules)) {
            $rules = $this->rules();
        }

        $validator = $this->validationFactory->make($data, $rules, $customErrors);

        if ($validator->fails()) {
            throw (new ReflectionClass(isset($this->exception) ? $this->exception : ValidationError::class))->newInstance()->setContext($validator->errors()->toArray());
        }

        return $validator;
    }
}
