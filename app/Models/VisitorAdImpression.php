<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorAdImpression extends Model
{
    use HasFactory;

    protected $fillable = ['visitor_id', 'ad_id', 'impressions'];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
