<?php

namespace Leaptel;

use Illuminate\Support\Facades\DB;

class RawDB
{
    /** @var \PDO[] $pdo */
    public static $pdo = [];

    /**
     * @param string $store
     * @param boolean $refresh
     * @return \PDO
     */
    public static function getPdo(string $store = 'mysql', bool $refresh = false)
    {
        if ($refresh || empty(self::$pdo[$store])) {
            $c = DB::reconnect($store);
            self::$pdo[$store] = $c->getPdo();
        }
        return self::$pdo[$store];
    }
}
