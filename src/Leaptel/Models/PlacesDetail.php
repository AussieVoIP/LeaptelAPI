<?php

namespace Leaptel\Models;

use Illuminate\Database\Eloquent\Model;

class PlacesDetail extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $table = 'places_details';
    protected $keyType = 'string';
    protected $primaryKey = 'location_id';
    protected $guarded = [];
    protected $casts = ['raw' => 'array'];
}
