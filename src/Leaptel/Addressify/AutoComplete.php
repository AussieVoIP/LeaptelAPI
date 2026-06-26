<?php

namespace Leaptel\Addressify;

use Exception;
use Leaptel\Addressify\Objects\AddressInfo;
use Leaptel\Models\Location;
use Leaptel\Models\LocationLookup;

class AutoComplete extends Core
{
    public function __construct(
        public string $req,
    ) {}

    /**
     * Return an array of Location models for this lookup
     *
     * @param bool $refresh
     * @return array<Location>
     * @throws Exception
     */
    public function go(bool $refresh = false)
    {
        $req = strtoupper($this->req);
        if ($refresh) {
            LocationLookup::where(["source" => $req])->delete();
        }

        $cache = LocationLookup::where(["source" => $req])->get();
        $retarr = [];

        // Nothing cached, recreate
        if (!$cache->count()) {
            $secrets = $this->getSecrets();
            $query = [
                "api_key" => $secrets['addressify_api_key'],
                "term" => $req,
                "address_types" => 2,
                "info" => "true",
                "close_matches" => "true",
            ];
            $url = $secrets['addressifyurl'] . '/address/autocomplete';
            $c = $this->getGuzClient();
            $resp = $c->request('GET', $url, ['query' => $query, 'debug' => $this->debug]);
            foreach (json_decode($resp->getBody(), true) as $row) {
                $ai = new AddressInfo($row);
                $key = $ai->AddressFull;
                $ai->__raw = json_encode($row);
                if (!empty($retarr[$key])) {
                    throw new \Exception("Bug: Duplicate full address $key");
                }
                $l = Location::fromAddressInfo($ai);
                LocationLookup::storeLocationRef($req, $l);
                $retarr[$key] = $l;
            }
        } else {
            // It's cached, get all the locations for this
            foreach ($cache as $ll) {
                $location = Location::where("prikeyhash", $ll->lochash)->first();
                if (!$location) {
                    throw new \Exception("Bug, can't find " . $ll->lochash);
                }
                $key = $location->name;
                $retarr[$key] = $location;
            }
        }
        return $retarr;
    }
}
