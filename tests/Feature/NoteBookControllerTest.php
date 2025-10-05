<?php

namespace Tests\Feature;

use App\Models\NoteBook;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteBookControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_notebooks_index()
    {
        $user = User::factory()->create();
        $notebook = NoteBook::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('notebooks.index'));

        $response->assertOk();
        $response->assertSee($notebook->name);
    }

    /** @test */
    public function user_cannot_see_other_users_notebooks_in_index()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherNotebook = NoteBook::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('notebooks.index'));

        $response->assertOk();
        $response->assertDontSee($otherNotebook->name);
    }

    /** @test */
    public function user_can_view_create_notebook_form()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('notebooks.create'));

        $response->assertOk();
        $response->assertViewIs('notebooks.create');
    }

    /** @test */
    public function user_can_create_a_notebook()
    {
        $user = User::factory()->create();

        $notebookData = [
            'name' => 'My Test Notebook',
        ];

        $response = $this->actingAs($user)->post(route('notebooks.store'), $notebookData);

        $this->assertDatabaseHas('notebooks', [
            'name' => 'My Test Notebook',
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('notebooks.index'));
    }

    /** @test */
    public function notebook_requires_a_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notebooks.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function guest_cannot_access_notebooks()
    {
        $this->get(route('notebooks.index'))->assertRedirect(route('login'));
        $this->get(route('notebooks.create'))->assertRedirect(route('login'));
    }
}
