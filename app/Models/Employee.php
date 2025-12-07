<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'position',
        'email',
        'phone',
        'hire_date',
        'status',
        'notes',
    ];

    protected $dates = ['hire_date'];
}
