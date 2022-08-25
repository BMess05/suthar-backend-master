<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftStore extends Model
{
    use HasFactory;
    protected $table = 'gifts_stores';
    protected $fillable = [
        'gift_id', 'store_id'
    ];

    public function gift() {
        return $this->belongsTo('App\Models\Gift', 'gift_id', 'id');
    }

    public function store() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
}
