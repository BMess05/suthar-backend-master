<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'notifications';
    protected $fillable = [
        'contractor_id', 'text', 'read', 'type', 'type_id'
    ];

    protected $casts = [
        'id' => 'integer',
        'contractor_id' => 'integer',
        'read' => 'integer',
        'type_id' => 'integer'
    ];
}
