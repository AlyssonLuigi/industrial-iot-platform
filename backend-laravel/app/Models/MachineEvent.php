<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineEvent extends Model
{
    protected $fillable = [
        'machine_id',
        'state',
        'produced',
        'rejected',
        'total_production',
        'good_count',
        'reject_count',
        'temperature',
        'vibration',
        'availability',
        'performance',
        'quality',
        'oee',
        'event_time'
    ];
}
