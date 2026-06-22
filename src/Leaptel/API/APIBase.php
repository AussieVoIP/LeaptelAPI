<?php

namespace Leaptel\API;

use GuzzleHttp\Client;
use Leaptel\API\Response\GenericResponse;
use Leaptel\Leaptel;
use Leaptel\Models\QueryCache;

/** @package Leaptel\API */
class APIBase
{
    public static ?Client $guz = null;

    // Turn this on if you're having wierd errors, it'll show you every url it's requesting
    // protected bool $showurl = true;
    protected bool $showurl = false;

    // Retry requests this many times before aborting
    protected int $retrycount = 5;

    // This is what object is returned by the various get functions
    protected string $retclass = GenericResponse::class;

    // What the returned objects (when multiple) should be indexed by
    protected string $indexby = "";

    // How long to cache results for.
    protected int $cacheforsecs = 300;

    // Set this to __timestamp to add a hidden timestamp value
    protected string $addtimestamp = "";

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
        $params['headers'] = ['User-Agent' => 'LeaptelAPI/1.0.1', 'Accept' => 'application/json'];
        $formparams = $this->getFormParams();
        if ($formparams) {
            $params['form_params'] = $formparams;
        }
        return $params;
    }

    /**
     * Called by the get functions to filter results if needed
     *
     * @param array $results
     * @return array
     */
    private function filterResults(array $results): array
    {
        foreach ($this->filters as $callable) {
            foreach ($results as $k => $v) {
                $r = $callable($v);
                if ($r === false) {
                    unset($results[$k]);
                }
            }
        }
        return $results;
    }

    public function getRawResponse(): array
    {
        $c = $this->getGuzClient();
        $params = $this->getGuzParams();
        $retarr = [
            "url" => $this->getFullUrl(),
            "params" => $params,
        ];
        $resp = $c->request('GET', $this->getFullUrl(), $params);
        $retarr['code'] = $resp->getStatusCode();
        $retarr['body'] = (string) $resp->getBody();
        return $retarr;
    }

    protected function getMultipleNotPaginated(bool $refresh = false, int $loopcount = 0): array
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $retarr = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            if (isset($body['error'])) {
                if ($this->showurl) {
                    print $resp->getBody() . "\n";
                    print "Retrying\n";
                }
                $loopcount++;
                return $this->getMultipleNotPaginated(false, $loopcount);
            }

            $retarr = [];
            foreach ($body as $row) {
                if (!$row) {
                    if ($this->showurl) {
                        print $resp->getBody() . "\n";
                        print "Retrying\n";
                    }
                    $loopcount++;
                    return $this->getMultipleNotPaginated(false, $loopcount);
                }
                $obj = new $this->retclass($row);

                if ($this->addtimestamp) {
                    $obj[$this->addtimestamp] = time();
                }

                if ($this->indexby) {
                    $key = $obj->{$this->indexby};
                    if (!empty($retarr[$key])) {
                        print "Original: " . json_encode($retarr[$key]) . "\n";
                        print "New: " . json_encode($obj) . "\n";
                        throw new \Exception("Bug - duplicate key $key found");
                    }
                    $retarr[$key] = $obj;
                } else {
                    $retarr[] = $obj;
                }
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($retarr)]);
        }
        return $this->filterResults($retarr);
    }

    public function getMultiplePaginated(string $bodykey, string $paginationkey = "pagination", bool $refresh = false, int $loopcount = 0): array
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getUrl() . " after $loopcount attempts");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $retarr = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print "Requesting " . $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $pagination = ["next_page" => 1];
            $retarr = [];
            while ($pagination["next_page"] !== null) {
                $this->query = "?page=" . $pagination["next_page"];
                $resp = $c->request('GET', $this->getFullUrl(), $params);
                $body = json_decode((string) $resp->getBody(), true);
                if (empty($body[$bodykey])) {
                    $loopcount++;
                    if ($this->showurl) {
                        print "Retrying " . $this->showurl . " - attempt $loopcount\n";
                    }
                    return $this->getMultiplePaginated($bodykey, $paginationkey, false, $loopcount);
                }
                $pagination = $body[$paginationkey];
                foreach ($body[$bodykey] as $row) {
                    $obj = new $this->retclass($row);

                    if ($this->addtimestamp) {
                        $obj[$this->addtimestamp] = time();
                    }

                    if ($this->indexby) {
                        $key = $obj->{$this->indexby};
                        if (!empty($retarr[$key])) {
                            throw new \Exception("Bug - duplicate key $key found");
                        }
                        $retarr[$key] = $obj;
                    } else {
                        $retarr[] = $obj;
                    }
                }
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($retarr)]);
        }
        return $this->filterResults($retarr);
    }

    protected function getSingle(bool $refresh = false, $validator = null, int $loopcount = 0): mixed
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $obj = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $loopcount++;
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            if (isset($body['error'])) {
                if ($this->showurl) {
                    print $resp->getBody() . "\n";
                    print "Retrying\n";
                }
                return $this->getSingle(false, $validator, $loopcount);
            }

            if (is_callable($validator)) {
                $body = $validator($body);
            }
            if ($body === false) {
                return $this->getSingle(false, $validator, $loopcount);
            }
            if ($body === null) {
                return null;
            }

            $obj = new $this->retclass($body);

            if ($this->addtimestamp) {
                $obj[$this->addtimestamp] = time();
            }

            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($obj)]);
        }
        return $obj;
    }

    protected function postSingle(bool $refresh = false, $validator = null, int $loopcount = 0): mixed
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $obj = unserialize($qc['s']);
        } else {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $loopcount++;
            $resp = $c->request('POST', $this->getFullUrl(), $params);
            $body = json_decode((string) $resp->getBody(), true);
            if (isset($body['error'])) {
                if ($this->showurl) {
                    print $resp->getBody() . "\n";
                    print "Retrying\n";
                }
                return $this->postSingle(false, $validator, $loopcount);
            }

            if (is_callable($validator)) {
                $body = $validator($body);
            }
            if ($body === false) {
                return $this->postSingle(false, $validator, $loopcount);
            }
            if ($body === null) {
                return null;
            }

            $obj = new $this->retclass($body);

            if ($this->addtimestamp) {
                $obj[$this->addtimestamp] = time();
            }

            QueryCache::cacheResult($this->getUrl(), $params, ["s" => serialize($obj)]);
        }
        return $obj;
    }
}
