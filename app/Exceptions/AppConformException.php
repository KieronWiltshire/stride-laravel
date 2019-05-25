<?php

namespace App\Exceptions;

use Exception;

class AppConformException extends Exception
{
  /**
   * Constructor.
   *
   * @param Exception $thrownException
   */
  public function __construct($thrownException)
  {
    parent::__construct('An exception was unable to conform to the application standard');
    $this->thrownException = $thrownException;
  }

  /**
   * Retrieve the underlying thrown exception that
   * threw this exception.
   * 
   * @return Exception
   */
  public function getThrownException()
  {
    return $this->thrownException;
  }
}
