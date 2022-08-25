<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "banks";
    protected $fillable = [
        'contractor_id', 'account_holder_name', 'account_number', 'bank_name', 'ifsc_code', 'account_type', 'default'
    ];
}
