<?php

namespace App\Import;

use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

class FailedImportResult implements ImportResultInterface {

    /**
     * @Serializer\Type("boolean")
     * @Serializer\Accessor(getter="isSuccessful")
     * @Serializer\SerializedName("success")
     */
    private $isSuccessful = false;

    /**
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getCode")
     * @Serializer\SerializedName("code")
     */
    public $code;

    /**
     * @Serializer\Type("string")
     * @Serializer\Accessor(getter="getMessage")
     * @Serializer\SerializedName("message")
     */
    public $message;

    /**
     * @Serializer\Exclude()
     */
    private $exception;

    public function __construct(\Exception $exception) {
        $this->exception = $exception;

        $this->code = $exception->getCode();
        $this->message = $exception->getMessage();
    }

    public function getCode() {
        return $this->exception->getCode();
    }

    public function getMessage() {
        return $this->exception->getMessage();
    }

    public function getException() {
        return $this->exception;
    }

    public function isSuccessful() {
        return $this->isSuccessful;
    }
}