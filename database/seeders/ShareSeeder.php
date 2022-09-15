<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;
use App\Models\Share;

class ShareSeeder extends Seeder
{
    protected $faker;
    
    protected $users;
    
    protected $userCount;
    
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->faker = \Faker\Factory::create();
        $users = User::all();
        $this->usersCount = $users->count();
        $this->users = $users->toArray();
        $posts = Post::all();
        
        foreach ($posts as $post) {
            $this->sharePost($post);
        }
    }
    
    private function sharePost($post)
    {
        $num = $this->faker->numberBetween(0, $this->usersCount);
        $users = array_slice($this->users, 0, $num);
        foreach ($users as $user) {
            $this->likePostByUser($post, $user);
        }
        return true;
    }
    
    private function sharePostByUser($post, $user)
    {
        $new = Shares::create([
                'post_id' => $post->id,
                'user_id' => $user['id'],
            ]);
        return true;
    }
}
