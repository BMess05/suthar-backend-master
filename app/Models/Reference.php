<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "references";
    protected $fillable = [
        'full_name', 'phone_number', 'email', 'building_type', 'state', 'city', 'address', 'landmark', 'area_in_sqft', 'frames_count', 'status', 'created_by'
    ]; // , 'store_id'

    public function contractor()
    {
        return $this->belongsTo('App\Models\Contractor', 'created_by', 'id');
    }

    // public function store() {
    //     return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    // }

    public function point()
    {
        return $this->hasOne('App\Models\Point', 'reference_id', 'id');
    }

    public function saveReference($data)
    {
        $this->full_name = $data['full_name'];
        $this->phone_number = $data['phone_number'];
        $this->email = $data['email'] ?? '';
        $this->building_type = $data['building_type'];
        $this->state = $data['state'];
        $this->city = $data['city'];
        $this->address = $data['address'];
        $this->landmark = $data['landmark'] ?? '';
        $this->area_in_sqft = $data['area_in_sqft'];
        $this->frames_count = $data['frames_count'];
        // $this->store_id = $data['store_id'];
        $this->status = 'Pending';
        $this->created_by = auth('api')->user()->id;
        return $this->save();
    }
}
