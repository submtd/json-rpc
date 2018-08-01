<?php

namespace Submtd\JsonRpc;

class JsonRpc
{
    protected $id;
    protected $host;
    protected $method;
    protected $parameters = [];
    protected $version = '2.0';
    protected $response;

    public function __construct()
    {
    }

    public function __invoke(string $host, string $method, array $parameters = [], string $version = '2.0')
    {
        return $host;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(integer $id)
    {
        $this->id = $id;
        return $this->getId();
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
        return $this->getHost();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this->getMethod();
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this->getParameters();
    }

    public function addParameter(string $parameter)
    {
        $this->parameters[] = $parameter;
        return $this->getParameters();
    }

    public function removeParameters($parameters)
    {
        $this->parameters = array_diff($this->parameters, (array) $parameters);
        return $this->getParameters();
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
        return $this->getVersion();
    }
}
