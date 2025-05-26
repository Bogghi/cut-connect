<?php

namespace App\Utility;

class Result
{
    public array $data;
    public int $statusCode = 200;
    private const string KO = 'KO';
    private const string OK = 'OK';

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function setInvalidParameters(?array $parameters = null): self
    {
        $this->data['status'] = self::KO;
        $this->data['message'] = 'Invalid parameters ';
        $this->statusCode = 400;
        if($parameters) {
            $this->data['message'] .= implode(',', $parameters);
        }
        return $this;
    }

    public function setUnauthorized(): self
    {
        $this->data['status'] = self::KO;
        $this->data['message'] = 'Unauthorized';
        $this->statusCode = 401;

        return $this;
    }

    public function setSuccessResult(?array $array): self
    {
        $this->data['status'] = self::OK;

        if($array) {
            $this->data = [...$this->data, ...$array];
        }

        return $this;
    }
}