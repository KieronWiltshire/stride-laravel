<?php

namespace App\Validation;

use App\Exceptions\Http\ValidationError;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use ReflectionClass;

/**
 * Base Validation class. All entity specific validation classes inherit
 * this class and can override any function for respective specific needs
 */
abstract class AppValidator {

  /**
   * @var \Illuminate\Contracts\Validation\Factory
   */
  protected $validationFactory;

  /**
   * @var \App\Exceptions\AppError
   */
  protected $exception = ValidationError::class;

  /**
   * Create a new app validator instance.
   *
   * @param \Illuminate\Contracts\Validation\Factory $validationFactory
   */
  public function __construct(ValidationFactory $validationFactory) {
    $this->validationFactory = $validationFactory;
  }

  /**
   * Retrieve the rules set for the validator.
   *
   * @return array
   */
  public abstract function rules();

  /**
   * Validates the specified data against the defined rule set.
   *
   * @param array $data
   * @param array $rules
   * @param array $customErrors
   * @return \Illuminate\Contracts\Validation\Validator
   *
   * @throws \ReflectionException
   * @throws \App\Exceptions\AppError
   */
  public function validate(array $data, array $rules = [], array $customErrors = []) {
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