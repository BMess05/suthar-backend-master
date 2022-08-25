<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use Illuminate\Database\Eloquent\SoftDeletes;

class RedeemRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'redeem_reqs';
    protected $fillable = [
        'contractor_id', 'points', 'req_type', 'req_id', 'req_status'
    ];

    protected $appends = [
        'request_type_name', 'request_status_name', 'in_rupees'
    ];

    public function getRequestTypeNameAttribute()
    {
        if ($this->attributes['req_type'] == 1) {
            return "Cash";
        } elseif ($this->attributes['req_type'] == 2) {
            return "Bank Account";
        } elseif ($this->attributes['req_type'] == 3) {
            return "Gift order";
        } else {
            return null;
        }
    }

    public function getRequestStatusNameAttribute()
    {
        if ($this->attributes['req_status'] == 0) {
            return "Pending";
        } elseif ($this->attributes['req_status'] == 1) {
            return "Complete";
        } else {
            return null;
        }
    }

    public function getInRupeesAttribute()
    {
        if ($this->attributes['points'] == 0) {
            return 0;
        }
        return Helper::points_to_rupees($this->attributes['points']);
    }

    public function contractor()
    {
        return $this->belongsTo('App\Models\Contractor', 'contractor_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'req_id', 'id');
    }

    public function gift_order()
    {
        return $this->belongsTo('App\Models\Order', 'req_id', 'id');
    }
}
