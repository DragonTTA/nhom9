<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model;

class RequestLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'request_logs';

    protected $fillable = [
        'user_id',
        'method',
        'url',
        'ip',
        'status_code',
        'response_time',
        'request_data',
        'response_data',
    ];
}
