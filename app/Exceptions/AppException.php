<?php

namespace App\Exceptions;

use Exception;
use Webpatser\Uuid\Uuid;

abstract class AppException extends Exception
{
  /**
   * @var Uuid
   */
  private $uuid;

  /**
   * @var string
   */
  private $type;

  /**
   * @var integer
   */
  private $httpStatus;

  /**
   * @var Array
   */
  private $context;

  /**
   * Constructor.
   *
   * @param string $message
   * @param integer $httpStatus
   * @param string $type
   */
  public function __construct($message, $httpStatus = 200, $type = null)
  {
    parent::__construct($message);
    $this->uuid = Uuid::generate()->string;
    $this->type = ($type) ? $type : (new \ReflectionClass($this))->getShortName();
    $this->httpStatus = $httpStatus;
    $this->context = [];
  }

  /**
   * Retrieve the unique identifier relating to this exception.
   *
   * @return Webpatser\Uuid\Uuid
   */
  public function getId()
  {
    return $this->uuid;
  }

  /**
   * Retrieve the error type.
   * 
   * @return string
   */
  public function getErrorType()
  {
    return $this->type;
  }

  /**
   * Retrieve the HTTP status of the error.
   *
   * @return integer
   */
  public function getHttpStatus()
  {
    return $this->httpStatus;
  }

  /**
   * Set the context of the error.
   * 
   * @param Array $context
   */
  public function setContext($context)
  {
    $this->context = $context;
  }

  /**
   * Retrieve the context of the error.
   * 
   * @return Array
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * Render the error.
   *
   * @return Array
   */
  public function render()
  {
    return [
      'type' => $this->getErrorType(),
      'message' => $this->message,
      'context' => $this->context,
      'meta' => [
        'id' => $this->uuid,
      ]
    ];
  }
}
