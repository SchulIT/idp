<?php

namespace App\HealthCheck;

class HealthCheckResult {

    /**
     * @param string[] $messageParameter
     * @param string[] $routeParameter
     */
    public function __construct(private readonly HealthCheckResultType $type, private readonly string $title, private readonly string $message, private $messageParameter = [ ], private ?string $route = null, private array $routeParameter = [ ])
    {
    }

    public function getType(): HealthCheckResultType {
        return $this->type;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getMessage(): string {
        return $this->message;
    }

    /**
     * @return string[]
     */
    public function getMessageParameter(): array {
        return $this->messageParameter;
    }

    public function addMessageParameter(string $key, string $value): HealthCheckResult {
        $this->messageParameter[$key] = $value;
        return $this;
    }

    public function getRoute(): ?string {
        return $this->route;
    }

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
     */
    public function setRouteParameter(array $parameter): HealthCheckResult {
        $this->routeParameter = $parameter;
        return $this;
    }
}