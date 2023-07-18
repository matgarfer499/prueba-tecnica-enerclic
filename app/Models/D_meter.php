<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class D_meter extends Model
{
    use HasFactory;

    protected $fillable = [
        'datatime',
        'power',
        'power2',
        'energy',
        'energy2'
    ];
}
