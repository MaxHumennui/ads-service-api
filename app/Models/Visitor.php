<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'country', 'region', 'city', 'ad_clicks'];

    protected $casts = [
        'ad_clicks' => 'array',
    ];

    public function adImpressions()
    {
        return $this->hasMany(VisitorAdImpression::class);
    }
}
