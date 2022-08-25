<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\ContractorRegistered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contractor  extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = "contractors";
    protected $fillable = [
        'name', 'email', 'phone', 'address', 'photo', 'password', 'block_unblock', 'created_by', 'otp', 'total_points', 'push_notifications', 'type', 'store_id'
    ];

    protected $hidden = [
        'password'
    ];

    protected $appends = [
        'photo_url', 'type_name'
    ];

    protected $casts = [
        'id' => 'integer',
        'push_notifications' => 'integer',
        'total_points' => 'integer',
        'otp' => 'integer'
    ];

    public function getPhotoUrlAttribute()
    {
        if ($this->attributes['photo'] == "") {
            return null;
        }
        return url('/uploads/contractors/' . $this->attributes['photo']);
    }

    public function getTypeNameAttribute()
    {
        if ($this->attributes['type'] == "") {
            return null;
        }
        if ($this->attributes['type'] == 1) {
            return "Architect";
        } elseif ($this->attributes['type'] == 2) {
            return "Contractor";
        } else {
            return null;
        }
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function references()
    {
        return $this->hasMany('App\Models\Reference', 'created_by', 'id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'contractor_id', 'id');
    }

    public function redeem_requests()
    {
        return $this->hasMany('App\Models\RedeemRequest', 'contractor_id', 'id');
    }

    public function points()
    {
        return $this->hasMany('App\Models\Point', 'contractor_id', 'id');
    }

    public function store()
    {
        return $this->belongsTo('App\Models\Store', 'store_id', 'id');
    }

    public function saveContractor($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->type = $data['type'];
        $this->store_id = $data['store'];
        if ($this->exists) {
            $new_user = 0;
        } else {
            $new_user = 1;
        }

        if ($new_user == 1) {
            $this->password = bcrypt($data['generated']);
            $this->block_unblock = 0;
            $this->created_by = \Auth::user()->id;
        }
        if ($this->save()) {
            if ($new_user == 1) {
                event(new ContractorRegistered($this, $data['generated']));
            }
            return true;
        }
        return false;
    }
}
