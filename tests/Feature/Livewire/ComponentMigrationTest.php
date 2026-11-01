<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Filter;
use App\Livewire\Posts\Create;
use App\Livewire\Profile\ProfilePage;
use App\Livewire\Users\Edit;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ComponentMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_component_finds_users_by_name_and_username(): void
    {
        User::factory()->create([
            'name' => 'Alice Example',
            'username' => 'alice-example',
        ]);
        User::factory()->create([
            'name' => 'Bob Example',
            'username' => 'bob-example',
        ]);

        Livewire::test(Filter::class)
            ->set('searchTerm', 'alice')
            ->assertSee('Alice Example')
            ->assertSee('alice-example')
            ->assertDontSee('Bob Example');
    }

    public function test_post_creation_component_creates_a_post_and_stores_an_image(): void
    {
        Storage::fake('public');
        $this->actingAs($user = User::factory()->create());

        Livewire::test(Create::class)
            ->set('title', 'Sunset')
            ->set('location', 'Manila')
            ->set('body', 'A sunset over the city.')
            ->set('file', UploadedFile::fake()->create('sunset.jpg', 100, 'image/jpeg'))
            ->call('submit')
            ->assertRedirect('/home');

        $post = Post::query()->where('user_id', $user->id)->firstOrFail();

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Sunset',
            'body' => 'A sunset over the city.',
        ]);

        $media = Media::query()->where('post_id', $post->id)->firstOrFail();

        $this->assertTrue((bool) $media->is_image);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_profile_component_can_follow_another_user(): void
    {
        $this->actingAs($actor = User::factory()->create());
        $profile = User::factory()->create();

        Livewire::test(ProfilePage::class, ['user' => $profile])
            ->call('incrementFollow', $profile)
            ->assertRedirect(route('profile', ['username' => $profile->username]));

        $this->assertDatabaseHas('followers', [
            'following_id' => $actor->id,
            'follower_id' => $profile->id,
        ]);
    }

    public function test_user_edit_component_updates_user_information(): void
    {
        $this->actingAs(User::factory()->create(['role_id' => 2]));
        $user = User::factory()->create(['role_id' => 1]);

        Livewire::test(Edit::class, ['user' => $user])
            ->set('name', 'EditedUser')
            ->set('username', 'edited-user')
            ->set('email', 'edited@example.com')
            ->set('is_private', '1')
            ->set('role_id', 2)
            ->call('submit')
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'EditedUser',
            'username' => 'edited-user',
            'email' => 'edited@example.com',
            'role_id' => 2,
            'is_private' => 1,
        ]);
    }
}
