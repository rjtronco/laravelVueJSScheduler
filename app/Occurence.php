<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Occurence extends Model
{
    protected $fillable = ['title','start_time','end_time'];
}
