<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\NoteBook;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     * Each user should be able to view only their notes
     */
    public function index()
    {
        // Fetching notes by using the relationship where notes belonging to the user.
        $notes = Note::whereBelongsTo(Auth::user())->latest('updated_at')->paginate(5);

        // // Fetch the notes that belong to the authenticated user using the Elo relationship.
        // $notes = Auth::user()->notes()->latest('updated_at')->paginate(5);

        return view('notes.index')->with('notes', $notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // fetch all the notebooks and pass it to the create view, then retun notes.create view and pass it with $notebooks.
        $notebooks = NoteBook::where('user_id', Auth::id())->get();
        return view('notes.create')->with('notebooks', $notebooks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        // instead of manually entering the user ID with the authenticated users ID, we can create this note using... 
        // Auth::user()-> notes and use the create method
        $note = Auth::user()->notes()->create([
            'uuid' => Str::uuid(),
            'title' => $request->title,
            'text' => $request->text,
            'notebook_id' => $request->notebook_id
        ]);
        // $note->save();

        // // create a new Note to be saved
        // $note = new Note([
        //     'user_id' => Auth::id(),
        //     'uuid' => Str::uuid(),
        //     'title' => $request->title,
        //     'text' => $request->text,
        //     'notebook_id' => $request->notebook_id
        // ]);
        // $note->save();

        return to_route('notes.show', $note);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        // if($note->user_id !== Auth::id()){
        //     abort(403);
        // }

        // instead of checking the ID against the foreign key, we can access the note's user model 
        // with $note->user and directly check if this user is the authorized user using is(Auth::user). 
        // comparing the primary key of the same model. So if it's not the same user then abort.
        if(!$note->user->is(Auth::user())){
            abort(403);
        }

        return view('notes.show', ['note' => $note]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
       if(!$note->user->is(Auth::user())){
            abort(403);
        }

        $notebooks = NoteBook::where('user_id', Auth::id())->get();
        return view('notes.edit', ['note' => $note, 'notebooks' => $notebooks]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
       if(!$note->user->is(Auth::user())){
            abort(403);
        }

        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        $note->update([
            'title' => $request->title,
            'text' => $request->text,
            'notebook_id' => $request->notebook_id,
        ]);

        return to_route('notes.show', $note)
            ->with('success', 'Changes saved');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        if(!$note->user->is(Auth::user())){
            abort(403);
        }

        $note->delete();

        return to_route('notes.index')
            ->with('success', 'Note has been deleted!');
    }
}
