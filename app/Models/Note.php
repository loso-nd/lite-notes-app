<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     *
     * Example of route model binding uses ID key by default to resolve in the model
     * We can customize this and return uuid where any model that you pass using route model binding will be resolved using uuid.
     *
     * */ 
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Inverse relationship that lets us access the user of a specific note.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Inverse relationship that lets us access the note from a specific note.
    public function notebook()
    {
        return $this->belongsTo(NoteBook::class);
    }
}
