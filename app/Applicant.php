<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    //fillable variables
    protected $fillable = ['email', 'name', 'isHired', 'created_at'];
}
