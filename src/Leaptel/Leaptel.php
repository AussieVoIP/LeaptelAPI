<?php

namespace Leaptel;

use Exception;

/** @package Leaptel */
class Leaptel
{
    // Cache for secret.json
    private static ?array $sjson = null;

    /**
     * Load secrets from secret.json
     *
     * @param bool $refresh Recreate cache
     * @return array
     * @throws Exception
     */
    public static function getSecrets(bool $refresh = false)
    {
        if ($refresh) {
            self::$sjson = null;
        }

        if (!is_array(self::$sjson)) {
            $sfile = __DIR__ . "/secret.json";
            if (!file_exists($sfile)) {
                throw new \Exception("$sfile does not exist - copy sample file into place and update credentials");
            }
            $sjson = json_decode(file_get_contents($sfile), true);
            self::$sjson = [];
            $shouldhave = ["baseurl", "username", "password", "addressifyurl", "addressify_api_key"];
            foreach ($shouldhave as $k) {
                if (empty($sjson[$k])) {
                    throw new \Exception("Key $k missing from $sfile");
                }
                self::$sjson[$k] = $sjson[$k];
            }
        }
        return self::$sjson;
    }
}
