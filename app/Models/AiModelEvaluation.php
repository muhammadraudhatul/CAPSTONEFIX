<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiModelEvaluation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'raw_metrics' => 'array',
    ];
}