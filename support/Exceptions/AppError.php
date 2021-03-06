<?php

namespace Support\Exceptions;

use Exception;
use Webpatser\Uuid\Uuid;
use PragmaRX\Version\Package\Facade as Version;

abstract class AppError extends Exception
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
     * @var string
     */
    private $cause;

    /**
     * @var integer
     */
    private $httpStatus;

    /**
     * @var array
     */
    private $context;

    /**
     * Constructor.
     *
     * @param string $message
     * @param integer $httpStatus
     * @param string $type
     * @throws \Exception
     */
    public function __construct($message, $httpStatus = 200, $type = null)
    {
        parent::__construct($message, 0);
        $class = (new \ReflectionClass($this));

        $this->uuid = Uuid::generate();
        $this->type = ($type) ? $type : $class->getShortName();
        $this->cause = str_replace('_exception', '', strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class->getShortName())));
        $this->httpStatus = $httpStatus;
        $this->context = [];
    }

    /**
     * Retrieve the unique identifier relating to this exception.
     *
     * @return string
     */
    public function getId()
    {
        return $this->uuid->string;
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
     * Set the cause of the error.
     *
     * @param string $cause
     * @return AppError
     */
    public function setCause($cause)
    {
        $this->cause = $cause;
        return $this;
    }

    /**
     * Retrieve the cause of the error.
     *
     * @return array
     */
    public function getCause()
    {
        return $this->cause;
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
     * @param array $context
     * @return AppError
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Retrieve the context of the error.
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Retrieve the current application version.
     *
     * @return string
     */
    public function getVersion()
    {
        return Version::public();
    }

    /**
     * Render the error.
     *
     * @return array
     */
    public function render()
    {
        return [
            'type' => $this->getErrorType(),
            'message' => $this->getMessage(),
            'cause' => $this->getCause(),
            'context' => $this->getContext(),
            'meta' => [
                'request' => [
                    'id' => $this->getId(),
                    'status' => $this->getHttpStatus()
                ],
                'version' => $this->getVersion()
            ],
        ];
    }
}
