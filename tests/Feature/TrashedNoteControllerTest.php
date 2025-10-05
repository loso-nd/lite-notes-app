<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrashedNoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_trashed_notes_index()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $note->delete();

        $response = $this->actingAs($user)->get(route('trashed.index'));

        $response->assertOk();
        $response->assertSee($note->title);
    }

    /** @test */
    public function user_cannot_see_other_users_trashed_notes()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);
        $otherNote->delete();

        $response = $this->actingAs($user)->get(route('trashed.index'));

        $response->assertOk();
        $response->assertDontSee($otherNote->title);
    }

    /** @test */
    public function user_can_view_their_trashed_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $note->delete();

        $response = $this->actingAs($user)->get(route('trashed.show', $note));

        $response->assertOk();
        $response->assertSee($note->title);
    }

    /** @test */
    public function user_cannot_view_other_users_trashed_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);
        $otherNote->delete();

        $response = $this->actingAs($user)->get(route('trashed.show', $otherNote));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_restore_their_trashed_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $note->delete();

        $response = $this->actingAs($user)->put(route('trashed.update', $note));

        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
            'deleted_at' => null,
        ]);

        $response->assertRedirect(route('notes.show', $note));
        $response->assertSessionHas('success', 'Note has been restored!');
    }

    /** @test */
    public function user_cannot_restore_other_users_trashed_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);
        $otherNote->delete();

        $response = $this->actingAs($user)->put(route('trashed.update', $otherNote));

        $response->assertForbidden();
    }

    /** @test */
    public function user_can_permanently_delete_their_trashed_note()
    {
        $user = User::factory()->create();
        $note = Note::factory()->create(['user_id' => $user->id]);
        $note->delete();

        $response = $this->actingAs($user)->delete(route('trashed.destroy', $note));

        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
        $response->assertRedirect(route('trashed.index'));
        $response->assertSessionHas('success', 'Note has been deleted!');
    }

    /** @test */
    public function user_cannot_permanently_delete_other_users_trashed_note()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNote = Note::factory()->create(['user_id' => $otherUser->id]);
        $otherNote->delete();

        $response = $this->actingAs($user)->delete(route('trashed.destroy', $otherNote));

        $response->assertForbidden();
    }

    /** @test */
    public function trashed_notes_are_not_shown_in_regular_notes_index()
    {
        $user = User::factory()->create();
        $activeNote = Note::factory()->create(['user_id' => $user->id]);
        $trashedNote = Note::factory()->create(['user_id' => $user->id]);
        $trashedNote->delete();

        $response = $this->actingAs($user)->get(route('notes.index'));

        $response->assertOk();
        $response->assertSee($activeNote->title);
        $response->assertDontSee($trashedNote->title);
    }

    /** @test */
    public function guest_cannot_access_trashed_notes()
    {
        $note = Note::factory()->create();
        $note->delete();

        $this->get(route('trashed.index'))->assertRedirect(route('login'));
        $this->get(route('trashed.show', $note))->assertRedirect(route('login'));
    }
}
