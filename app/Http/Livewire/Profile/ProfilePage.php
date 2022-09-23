<?php

namespace App\Http\Livewire\Profile;

use App\Models\Follower;
use App\Models\User;
use App\Models\Post;
use Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ProfilePage extends Component
{
    public $user;

    public $showFollower;

    public $showFollower1;

    public $showFollowing;

    public $showFollowing1;

    public $followers;

    public $viewFollowersId;

    public $isOpenViewFollowersModal = false;

    public $isOpenViewFollowingModal = false;

    public $followersCount;

    public $followings;

    public $followingsCount;

    public $posts;
    
    public $postsCount;

    public function mount()
    {
        $this->postsCount = $this->user->posts_count;
        $this->followersCount = $this->user->followers_count;
        $this->followingsCount = $this->user->followings_count;
    }

    public function render()
    {
        return view('livewire.profile.profile-page');
    }

    public function incrementFollow(User $user)
    {
        Gate::authorize('is-not-user-profile', $this->user);

        $follow = Follower::where('following_id', Auth::id())
            ->where('follower_id', $user->id);

        if (! $follow->count()) {
            $new = Follower::create([
                'following_id' => Auth::id(),
                'follower_id' => $user->id,
            ]);
        } else {
            $follow->delete();
        }

        return redirect()->route('profile', ['username' => $user->username]);
    }

    public function showViewFollowersModal(User $user)
    {
        Gate::authorize('is-user-profile', $this->user);
        $this->isOpenViewFollowersModal = true;
        $showFollower = Follower::where('follower_id', $user->id)->select('following_id')->pluck('following_id'); 

        if($showFollower == '[]'){
            $this->showFollower1 = "You don't have follower.";
        }else{
            $this->showFollower1 = User::whereIn('id', $showFollower)->select('name')->pluck('name');  
        }
        
    }

    public function showViewFollowingModal(User $user)
    {
        Gate::authorize('is-user-profile', $this->user);
        $this->isOpenViewFollowingModal = true;
        $showFollowing = Follower::where('following_id', $user->id)->select('follower_id')->pluck('follower_id'); 
        
        if($showFollowing == '[]'){
            $this->showFollowing1 = "You haven't follow anyone.";
        }else{
            $this->showFollowing1 = User::whereIn('id', $showFollowing)->select('name')->pluck('name');
        }
    }
}
