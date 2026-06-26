<?php

namespace Leaptel\API\Location;

use Leaptel\API\APIBase;
use Leaptel\API\Request\AVCValidate;
use Leaptel\API\Response\NBNSQResponse;
use Leaptel\Models\NTDPort;
use Override;

/** @package Leaptel\API */
class CheckAVC extends APIBase
{
    protected string $path = '/service-qualifications';

    protected string $retclass = NBNSQResponse::class;
    protected int $cacheforsecs = 86400;
    protected string $addtimestamp = "timestamp";

    protected AVCValidate $av;

    public function __construct(AVCValidate $av)
    {
        $this->av = $av;

        $f = function (NBNSQResponse $results) {
            $results->ntd_models = [];
            $results->valid_avc = null;
            foreach ($results->ntd_ports as $p) {
                if ($p->ServiceIDMatch == "yes") {
                    $p->avc_id = $this->av->avc_id;
                }
                $m = NTDPort::getFromPortRecord($p);
                $results->ntd_models[] = $m;
                if ($m->avc_id) {
                    $results->valid_avc = $m;
                }
            }
            return true;
        };

        $this->addFilter($f);
        return $this;
    }

    #[Override]
    public function getFormParams(): array
    {
        return $this->av->toArray();
    }

    /**
     * @param bool $refresh
     * @return mixed
     */
    public function go(bool $refresh = false): ?NTDPort
    {
        $validator = function ($body) {
            if (empty($body['addresses'])) {
                return false;
            }
            $bigbody = $body['addresses']['nbn'][0];
            unset($body['addresses']['nbn'][0]);
            $bodybody['__other'] = $body;
            $defaults = [
                "lotNumber" => null,
            ];
            foreach ($defaults as $k => $v) {
                if (empty($bigbody[$k])) {
                    $bigbody[$k] = $v;
                }
            }
            return $bigbody;
        };

        $res = $this->postSingle($refresh, $validator);
        return $res->valid_avc;
    }
}
