<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderGift extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "order_gifts";
    protected $fillable = [
        'order_id', 'gift_id', 'qty', 'points'
    ];

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function gift()
    {
        return $this->belongsTo('App\Models\Gift', 'gift_id', 'id');
    }
}
