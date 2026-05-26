<?php

namespace Leaptel\API;

use GuzzleHttp\Client;
use Leaptel\Leaptel;

/** @package Leaptel\API */
class APIBase
{
    public static ?Client $guz = null;

    // These are all loaded on demand.
    protected ?string $baseurl = null;
    protected ?string $username = null;
    protected ?string $password = null;

    protected string $path;
    protected string $query = "";

    protected array $filters = [];

    public function addFilter(callable $filter)
    {
        $this->filters[] = $filter;
    }

    /** @return string  */
    public function getUrl(): string
    {
        if ($this->baseurl === null) {
            $secrets = Leaptel::getSecrets();
            $this->baseurl = $secrets['baseurl'];
        }
        return $this->baseurl . $this->path;
    }
    /** @return string  */
    public function getFullUrl(): string
    {
        return $this->getUrl() . $this->query;
    }

    /** @return Client  */
    public function getGuzClient(): Client
    {
        if (APIBase::$guz === null) {
            APIBase::$guz = new Client();
        }
        return APIBase::$guz;
    }

    /** @return array  */
    public function getFormParams()
    {
        return [];
    }

    /** @return array  */
    public function getGuzParams(): array
    {
        if ($this->username === null) {
            $secrets = Leaptel::getSecrets();
            $this->username = $secrets['username'];
            $this->password = $secrets['password'];
        }

        $auth = [$this->username, $this->password];
        $params = ['debug' => false, 'http_errors' => false, "auth" => $auth, 'curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,]];
        $formparams = $this->getFormParams();
        if ($formparams) {
            $params['form_params'] = $formparams;
        }
        return $params;
    }
}
