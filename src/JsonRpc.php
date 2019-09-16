<?php

namespace Submtd\JsonRpc;

use GuzzleHttp\Client;

class JsonRpc
{
    protected $host;
    protected $method;
    protected $parameters = [];
    protected $version = '2.0';
    protected $response;
    protected $status = ['code' => null, 'message' => null];
    protected $errors = [];
    protected $verifyId = true;

    public function __construct($host = null, $version = '2.0', $verifyId = true)
    {
        $this->setHost($host);
        $this->setVersion($version);
        $this->verifyId = $verifyId;
    }

    public function __invoke($method, array $parameters = [])
    {
        $this->setMethod($method);
        $this->setParameters($parameters);
        return $this->request();
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setHost($host)
    {
        $this->host = $host;
        return $this->getHost();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
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

    public function addParameter($parameter)
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

    public function setVersion($version)
    {
        $this->version = $version;
        return $this->getVersion();
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function setResponse($response)
    {
        $this->response = $response;
        return $this->response;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusCode()
    {
        return $this->status['code'];
    }

    public function getStatusMessage()
    {
        return $this->status['message'];
    }

    private function setStatus($code, $message)
    {
        $this->status = [
            'code' => $code,
            'message' => $message,
        ];
    }

    public function hasErrors()
    {
        return !!$this->errors;
    }

    public function getErrors()
    {
        if (!$this->errors) {
            return false;
        }
        return $this->errors;
    }

    public function getLastError()
    {
        return end($this->errors);
    }

    public function getLastErrorCode()
    {
        $lastError = $this->getLastError();
        return $lastError['code'];
    }

    public function getLastErrorMessage()
    {
        $lastError = $this->getLastError();
        return $lastError['message'];
    }

    public function clearErrors()
    {
        $this->errors = [];
        return $this->getErrors();
    }

    private function addError($code, $message)
    {
        $this->errors[] = [
            'code' => $code,
            'message' => $message,
        ];
        $this->setStatus($code, $message);
        return $this->getErrors();
    }

    public function verifyId(bool $verify = true)
    {
        $this->verifyId = $verify;
        return $this->verifyId;
    }

    private function request()
    {
        $id = rand(1, 10000);
        $data = [
            'jsonrpc' => $this->getVersion(),
            'id' => (int) $id,
            'method' => $this->getMethod(),
            'params' => $this->getParameters(),
        ];
        $client = new Client();
        try {
            $response = $client->post($this->getHost(), [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $data,
                'connect_timeout' => 5,
                'timeout' => 5,
            ]);
            $this->setStatus($response->getStatusCode(), $response->getReasonPhrase());
            $this->setResponse(json_decode($response->getBody()->getContents()));
            if ($this->verifyId) {
                if (!isset($this->getResponse()->id)) {
                    throw new \Exception('Response id not provided.', 401);
                }
                if ($this->getResponse()->id != $id) {
                    throw new \Exception('Reponse id does not match the id provided in the request.', 403);
                }
            }
            return $this->getResponse();
        } catch (\Exception $e) {
            $this->addError($e->getCode(), $e->getMessage());
            return false;
        }
    }
}
