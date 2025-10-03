<?php

namespace App\Http\Controllers;

use App\Models\NoteBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth::id();
        $notebooks = NoteBook::where('user_id', $user_id)->latest('updated')->get();
        return view('notebooks.index')->with('notebooks', $notebooks);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notebooks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        // create a new Notebook to be saved
        $notebook = new NoteBook([
            'user_id' => Auth::id(), //Authenticate the user ID
            'name' => $request->name
        ]);

        $notebook->save();

        return to_route('notebooks.index');

        //One thing, to be able to save values like this, that is mass assignment, we need to set the guarded property to an empty array in the notebook model
    }

    /**
     * Display the specified resource.
     */
    public function show(NoteBook $noteBook)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NoteBook $noteBook)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NoteBook $noteBook)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NoteBook $noteBook)
    {
        //
    }
}
