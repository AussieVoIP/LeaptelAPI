<?php

namespace Leaptel\API;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
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

    // This is used in service-assurance-tests, to add the id of the response to the object
    protected bool $add_resp_id = false;

    // To assist in keeping responses unique, generate a hash of the body if requested. This
    // also adds __orig_row to the object, too.
    protected bool $addhash = false;

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
        // Allow queries to be cached, too!
        $params['querycache'] = $this->query;
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

    protected function getMultipleNotPaginated(bool $refresh = false, int $loopcount = 0, ?Response $prev = null): array
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts  - last result returned " . $prev->getStatusCode() . " and contained '" . $prev->getBody() . "'");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }

        // $params['debug'] = true;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        $body = null;
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getFullUrl() . "\n";
            }
            $respbody = $qc['respbody'] ?? null;
            if ($respbody) {
                $body = json_decode($respbody, true);
            }
        }
        if (!$body) {
            if ($this->showurl) {
                print $this->getFullUrl() . "\n";
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
                return $this->getMultipleNotPaginated(false, $loopcount, $resp);
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["respbody" => (string) $resp->getBody()]);
        }

        $retarr = [];
        foreach ($body as $resp_id => $row) {
            if (!$row) {
                if ($this->showurl) {
                    print "WTF no row\n";
                    var_dump($body);
                    exit;
                }
                $loopcount++;
                return $this->getMultipleNotPaginated(false, $loopcount);
            }
            if ($this->addhash) {
                // Not wonderfully happy with this, but it'll have to do. Ordering of
                // hash key names is not guaranteed
                $hashstr = json_encode($row);
                $row['request_hash'] = hash("sha256", $hashstr);
                $row['__orig_row'] = $hashstr;
            }
            $obj = new $this->retclass($row);

            // Used with service-assurance-tests. The name may need to change if it's used
            // with anything else
            if ($this->add_resp_id) {
                $obj->test_id = $resp_id;
            }

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
        return $this->filterResults($retarr);
    }

    public function getMultiplePaginated(string $bodykey, string $paginationkey = "pagination", bool $refresh = false, int $loopcount = 0, ?Response $prev = null): array
    {
        if ($loopcount > $this->retrycount) {
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts  - last result returned " . $prev->getStatusCode() . " and contained '" . $prev->getBody() . "'");
            throw new \Exception("Aborting " . $this->getUrl() . " after $loopcount attempts");
        }
        $params = $this->getGuzParams();
        if ($refresh) {
            QueryCache::purgeCachedUrl($this->getUrl());
        }
        // $params['debug'] = true;
        $chunks = [];
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $chunksjson = $qc['chunksjson'] ?? null;
            if ($chunksjson) {
                $chunks = json_decode($chunksjson, true);
            }
        }
        if (!$chunks) {
            if ($this->showurl) {
                print "Requesting " . $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $pagination = ["next_page" => 1];
            while ($pagination["next_page"] !== null) {
                $this->query = "?page=" . $pagination["next_page"];
                $resp = $c->request('GET', $this->getFullUrl(), $params);
                $body = json_decode((string) $resp->getBody(), true);
                if (empty($body[$bodykey])) {
                    $loopcount++;
                    if ($this->showurl) {
                        print "Retrying " . $this->showurl . " - attempt $loopcount\n";
                    }
                    return $this->getMultiplePaginated($bodykey, $paginationkey, false, $loopcount, $resp);
                } else {
                    foreach ($body[$bodykey] as $c) {
                        if ($this->addtimestamp) {
                            $c[$this->addtimestamp] = time();
                        }
                        $chunks[] = $c;
                    }
                }
                $pagination = $body[$paginationkey];
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["chunksjson" => json_encode($chunks)]);
        }
        $retarr = [];
        foreach ($chunks as $row) {
            if ($this->addhash) {
                // Not wonderfully happy with this, but it'll have to do. Ordering of
                // hash key names is not guaranteed
                $hashstr = json_encode($row);
                $row['request_hash'] = hash("sha256", $hashstr);
                $row['__orig_row'] = $hashstr;
            }

            $obj = new $this->retclass($row);

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
        $body = null;
        $hash = null;
        $respbody = null;
        $qc = QueryCache::getCachedResult($this->getUrl(), $params, $this->cacheforsecs);
        if ($qc) {
            if ($this->showurl) {
                print "Using cached request: " . $this->getUrl() . "\n";
            }
            $respbody = $qc['respbody'] ?? null;
            $body = json_decode($respbody, true);
            if ($this->addhash) {
                $hash = hash("sha256", $respbody);
            }
        }
        if (!$body) {
            if ($this->showurl) {
                print $this->getUrl() . "\n";
            }
            $c = $this->getGuzClient();
            $loopcount++;
            $resp = $c->request('GET', $this->getFullUrl(), $params);
            $respbody = (string) $resp->getBody();
            if ($this->addhash) {
                $hash = hash("sha256", $respbody);
            }
            $body = json_decode($respbody, true);
            if (isset($body['error'])) {
                if ($this->showurl) {
                    print $resp->getBody() . "\n";
                    print "Retrying\n";
                }
                return $this->getSingle(false, $validator, $loopcount);
            }
            QueryCache::cacheResult($this->getUrl(), $params, ["respbody" => $respbody]);
        }

        $origbody = $body;

        if (is_callable($validator)) {
            $body = $validator($body);
        }
        // This really should never happen
        if ($body === false) {
            throw new \Exception("Body is now false, that is bad - was " . json_encode($origbody));
            return $this->getSingle(false, $validator, $loopcount);
        }

        // Null back from Validator means don't return anything
        if ($body === null) {
            return null;
        }

        if ($hash) {
            $body['request_hash'] = $hash;
            $body['__orig_row'] = $respbody;
        }
        $obj = new $this->retclass($body);

        if ($this->addtimestamp) {
            $obj[$this->addtimestamp] = time();
        }

        return $this->filterResults([$obj])[0];
    }

    protected function postSingle(bool $refresh = false, $validator = null, int $loopcount = 0, ?Response $err = null): mixed
    {
        if ($loopcount > $this->retrycount) {
            if ($err) {
                $body = (string) $err->getBody();
            } else {
                $body = "";
            }
            throw new \Exception("Aborting " . $this->getFullUrl() . " after $loopcount attempts $body");
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
                return $this->postSingle(false, $validator, $loopcount, $resp);
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
        return $this->filterResults([$obj])[0];
    }

    /**
     * This is for idempotent actions that should never be cached. It will never retry,
     * it will always crash and fail if it doesn't work the first time.
     *
     * @param null|callable $validator
     * @return mixed
     * @throws \Exception
     */
    protected function postEvent($validator = null): mixed
    {
        $params = $this->getGuzParams();
        if ($this->showurl) {
            print $this->getUrl() . "\n";
        }
        $c = $this->getGuzClient();
        $resp = $c->request('POST', $this->getFullUrl(), $params);
        $body = json_decode((string) $resp->getBody(), true);
        if (isset($body['error'])) {
            throw new \Exception("Found error - " . $resp->getBody());
        }

        if (is_callable($validator)) {
            $body = $validator($body);
        }

        if ($body === false) {
            throw new \Exception("Body is false after validator from " . $resp->getBody());
        }

        // If validator returned null, return null.
        if ($body === null) {
            return null;
        }

        $obj = new $this->retclass($body);

        if ($this->addtimestamp) {
            $obj[$this->addtimestamp] = time();
        }

        return $this->filterResults([$obj])[0];
    }
}
