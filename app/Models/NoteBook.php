<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteBook extends Model
{
    protected $table = 'notebooks';
    protected $guarded = [];
}


/**
 * 
 * The class is named NoteBook (camelCase with capital B), so Laravel 
 * automatically assumes the table name is note_books. You need to explicitly tell it 
 * to use notebooks
 * 
 * */ 