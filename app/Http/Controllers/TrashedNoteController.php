<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrashedNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     * Each user should be able to view only their notes
     */
    public function index()
    {
        // Fetching notes by using the relationship where notes belonging to the user.
        $notes = Note::whereBelongsTo(Auth::user())->onlyTrashed()->latest('updated_at')->paginate(5);

        return view('notes.index')->with('notes', $notes);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        //Always check what data is being passed in
        // dd($note);

        if(!$note->user->is(Auth::user())){
            abort(403);
        }

        return view('notes.show')->with('note', $note);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Note $note)
    {
       if(!$note->user->is(Auth::user())){
            abort(403);
        }

        $note->restore();

        return to_route('notes.show', $note)
            ->with('success', 'Note has been restored!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if(!$note->user->is(Auth::user())){
            abort(403);
        }

        $note->forceDelete();

        return to_route('trashed.index')
            ->with('success', 'Note has been deleted!');
    }
}
