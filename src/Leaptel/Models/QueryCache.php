<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class QueryCache extends Model
{
    public $timestamps = true;
    public $incrementing = false;
    protected $table = 'generic_query_cache';
    protected $keyType = 'string';
    protected $primaryKey = 'prikeyhash';
    protected $guarded = [];
    protected $casts = ['respbody' => 'array'];

    public static function getPriKey(string $baseurl, array $params)
    {
        $hash = hash('sha256', $baseurl . json_encode($params));
        $retarr = [
            "baseurl" => $baseurl,
            "prikeyhash" => $hash,
        ];
        return $retarr;
    }

    public static function purgeCachedUrl(string $baseurl)
    {
        $o = self::where(['baseurl' => $baseurl])->first();
        if ($o) {
            $o->delete();
            return true;
        }
        return false;
    }

    public static function getCachedObj(string $baseurl, array $params, int $maxage = 300)
    {
        $key = self::getPriKey($baseurl, $params);
        $o = self::where($key)->first();
        if (!is_object($o)) {
            return null;
        }
        // If maxage is -1, never expire
        if ($maxage === -1) {
            return $o;
        }
        // Otherwise, see if it's too old, and if this updated_at is older
        // than time() - $maxage, it's time to go.
        $utime = (int) $o->updated_at->format('U');
        $cutoff = time() - $maxage;
        if ($utime < $cutoff) {
            // print "Purged cache obj $baseurl and " . json_encode($params) . "\n";
            $o->delete();
            return null;
        }
        return $o;
    }

    public static function getCachedResult(string $baseurl, array $params, int $maxage = 300)
    {
        $o = self::getCachedObj($baseurl, $params, $maxage);
        if (!$o) {
            return false;
        }
        return $o->respbody;
    }

    public static function cacheResult(string $baseurl, array $params, array $result)
    {
        $key = self::getPriKey($baseurl, $params);
        $c = self::firstOrCreate($key);
        if (!$c->rawparams) {
            $c->rawparams = $params;
        }
        // Can't cache a debug guzzle response!
        unset($result['__guzzle']);
        $c->respbody = $result;
        $c->save();
        return $c;
    }
}
