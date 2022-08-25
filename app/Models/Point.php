<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Point extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "points";
    protected $fillable = [
        'contractor_id', 'redeem', 'points', 'added_by', 'reference_id', 'redeem_type', 'redeem_id', 'completed_at', 'redeem_status', 'redeem_req_id'
    ];

    protected $appends = [
        'redeem_type_name', 'redeem_credit'
    ];

    protected $casts = [
        'id' => 'integer',
        'contractor_id' => 'integer',
        'redeem' => 'integer',
        'points' => 'integer',
        'added_by' => 'integer',
        'reference_id' => 'integer',
        'redeem_type' => 'integer',
        'redeem_id' => 'integer',
        'completed_at' => 'string',
        'redeem_status' => 'integer',
        'redeem_req_id' => 'integer'
    ];

    public function getRedeemTypeNameAttribute()
    {
        if ($this->attributes['redeem_type'] == 1) {
            return "Cash";
        } elseif ($this->attributes['redeem_type'] == 2) {
            return "Bank Account";
        } elseif ($this->attributes['redeem_type'] == 3) {
            return "By Gifts";
        } else {
            return null;
        }
    }

    public function getRedeemCreditAttribute()
    {
        if ($this->attributes['redeem'] == 0) {
            return "Credited";
        } elseif ($this->attributes['redeem'] == 1) {
            if ($this->attributes['redeem_status'] == 0) {
                return "Redeem (Pending)";
            } else {
                return "Redeem";
            }
        } else {
            return null;
        }
    }

    public function contractor()
    {
        return $this->belongsTo('App\Models\Contractor', 'contractor_id', 'id');
    }

    public function reference()
    {
        return $this->belongsTo('App\Models\Reference', 'reference_id', 'id');
    }

    public function store_manager()
    {
        return $this->belongsTo('App\Models\User', 'added_by', 'id');
    }
}
