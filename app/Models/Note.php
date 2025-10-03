<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $guarded = [];
}


/**
 * fillable property
 * *
 * specify all the fields that you want to be mass-assigned 
 * 
 * protected guarded 
 * variable and specify the fields that you don't want to be assigned, which you want to protect from mass assignment.
 * Just be careful when you do this, because in case you don't want some of the fields to be filled, you'll have to guard them
 * */ 