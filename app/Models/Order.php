<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "orders";
    protected $fillable = [
        'contractor_id', 'total_points', 'status'
    ];

    public function order_gifts()
    {
        return $this->hasMany('App\Models\OrderGift', 'order_id', 'id');
    }

    public function contractor()
    {
        return $this->hasMany('App\Models\Contractor', 'contractor_id', 'id');
    }
}
