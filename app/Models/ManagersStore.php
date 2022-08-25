<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagersStore extends Model
{
    use HasFactory;
    protected $table = "managers_stores";
    protected $fillable = [
        'manager_id', 'store_id'
    ];

    public function manager() {
        return $this->belongsTo('App\Models\User', 'manager_id', 'id');
    }

    public function store() {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }
}
