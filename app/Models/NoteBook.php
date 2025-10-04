<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteBook extends Model
{
    protected $table = 'notebooks';
    protected $guarded = [];

    // The relation between note and notebook is very similar to that of note and user. 
    // It's a many-to-one relationship. A notebook has many notes and the note belongs to one notebook
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
}


/**
 * 
 * protected $table = 'notebooks';
 * The class is named NoteBook (camelCase with capital B), so Laravel 
 * automatically assumes the table name is note_books. You need to explicitly tell it 
 * to use notebooks
 * 
 * */ 