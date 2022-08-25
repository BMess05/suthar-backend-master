<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $table = "stores";
    protected $fillable = [
        'name', 'city', 'is_active'
    ];

    public function managers_stores() {
        return $this->hasMany('App\Models\ManagersStore', 'store_id', 'id');
    }

    public function store_gifts() {
        return $this->hasMany('App\Models\GiftStore', 'store_id', 'id');
    }

    public function references() {
        return $this->hasMany('App\Models\Reference', 'store_id', 'id');
    }

    public function contractors() {
        return $this->hasMany('App\Models\Contractor', 'store_id', 'id');
    }

    public function saveStore($data) {
        $this->name = $data['name'];
        $this->city = $data['city'];
        return $this->save();
    }
}
