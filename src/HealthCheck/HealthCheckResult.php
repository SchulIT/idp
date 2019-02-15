<?php

namespace App\HealthCheck;

class HealthCheckResult {

    /** @var HealthCheckResultType */
    private $type;

    /** @var string */
    private $title;

    /** @var string */
    private $message;

    /** @var string[] */
    private $messageParameter = [ ];

    /** @var string|null */
    private $route;

    /** @var string[] */
    private $routeParameter = [ ];

    public function __construct(HealthCheckResultType $type, string $title, string $message, $messageParameter = [ ], ?string $route = null, array $routeParameter = [ ]) {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->messageParameter = $messageParameter;
        $this->route = $route;
        $this->routeParameter = $routeParameter;
    }

    /**
     * @return HealthCheckResultType
     */
    public function getType(): HealthCheckResultType {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function getMessageParameter(): array {
        return $this->messageParameter;
    }

    /**
     * @param string $key
     * @param string $value
     * @return HealthCheckResult
     */
    public function addMessageParameter(string $key, string $value): HealthCheckResult {
        $this->messageParameter[$key] = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string {
        return $this->route;
    }

    /**
     * @param string|null $route
     * @return HealthCheckResult
     */
    public function setRoute(?string $route): HealthCheckResult {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRouteParameter(): array {
        return $this->routeParameter;
    }

    /**
     * @param string[] $parameter
     * @return HealthCheckResult
     */
    public function setRouteParameter(array $parameter): HealthCheckResult {
        $this->routeParameter = $parameter;
        return $this;
    }
}