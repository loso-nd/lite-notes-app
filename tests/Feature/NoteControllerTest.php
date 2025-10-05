<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\NoteBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_notes_index()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertOk();
        $response->assertSee($note->title);
    }

    /** @test */
    public function user_cannot_see_other_users_notes_in_index()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertOk();
        $response->assertDontSee($otherNote->title);
    }

    /** @test */
    public function user_can_view_create_note_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notes.create'));

        $response->assertOk();
        $response->assertViewIs('notes.create');
    }

    /** @test */
    public function user_can_create_a_note()
    {
        $user = User::factory()->create();
        $notebook = NoteBook::factory()->create(['user_id' => $user->id]);

        $noteData = [
            'title' => 'Test Note',
            'text' => 'This is a test note content.',
            'notebook_id' => $notebook->id,
        ];

        $response = $this->actingAs($user)->post(route('notes.store'), $noteData);

        $this->assertDatabaseHas('notes', [
            'title' => 'Test Note',
            'text' => 'This is a test note content.',
            'user_id' => $user->id,
            'notebook_id' => $notebook->id,
        ]);

        $note = Note::where('title', 'Test Note')->first();
        $response->assertRedirect(route('notes.show', $note));
    }

    /** @test */
    public function note_requires_title_and_text()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'title' => '',
            'text' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'text']);
    }

    /** @test */
    public function user_can_view_their_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('notes.show', $note));

        $response->assertOk();
        $response->assertSee($note->title);
        $response->assertSee($note->text);
    }

    /** @test */
    public function user_cannot_view_other_users_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('notes.show', $otherNote));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_view_edit_form_for_their_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('notes.edit', $note));

        $response->assertOk();
        $response->assertViewIs('notes.edit');
        $response->assertViewHas('note', $note);
    }

    /** @test */
    public function user_cannot_edit_other_users_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('notes.edit', $otherNote));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_update_their_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $notebook = NoteBook::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'title' => 'Updated Title',
            'text' => 'Updated content',
            'notebook_id' => $notebook->id,
        ];

        $response = $this->actingAs($user)->put(route('notes.update', $note), $updatedData);

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'title' => 'Updated Title',
            'text' => 'Updated content',
        ]);

        $response->assertRedirect(route('notes.show', $note));
        $response->assertSessionHas('success', 'Changes saved');
    }

    /** @test */
    public function user_cannot_update_other_users_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put(route('notes.update', $otherNote), [
            'title' => 'Hacked Title',
            'text' => 'Hacked content',
        ]);

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_soft_delete_their_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('notes.destroy', $note));

        $this->assertSoftDeleted('notes', ['id' => $note->id]);
        $response->assertRedirect(route('notes.index'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function user_cannot_delete_other_users_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('notes.destroy', $otherNote));

        $response->assertForbidden();
        $this->assertDatabaseHas('notes', ['id' => $otherNote->id]);
    }

    /** @test */
    public function guest_cannot_access_notes()
    {
        $note = Note::factory()->create();

        $this->get(route('notes.index'))->assertRedirect(route('login'));
        $this->get(route('notes.create'))->assertRedirect(route('login'));
        $this->get(route('notes.show', $note))->assertRedirect(route('login'));
        $this->get(route('notes.edit', $note))->assertRedirect(route('login'));
    }
}
