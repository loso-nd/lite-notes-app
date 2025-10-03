<?php

namespace App\Http\Controllers;

use App\Models\Note;
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
        $user_id = Auth::id();
        $notes= Note::where('user_id', $user_id)->latest('updated')->paginate(5);
        return view('notes.index')->with('notes', $notes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // First check to see what request is being passed in
        // dd($request);

        // validate our request [https://laravel.com/docs/12.x/validation]
        $request->validate([
            'title' => 'required|max:120',
            'text' => 'required'
        ]);

        // create a new Note to be saved
        $note = new Note([
            'user_id' => Auth::id(), //Authenticate the user ID
            'title' => $request->title,
            'text' => $request->text
        ]);
        $note->save();
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {

        //User can only see thier own notes
        if($note->user_id !== Auth::id()){
            abort(403);
        }

        // Returns a view with a specific note
        return view('notes.show', ['note' => $note]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        //
    }
}
