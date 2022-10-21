<?php

namespace App\Http\Livewire\Posts;

use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Share;
use App\Models\Post;
use Auth;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;
?>
<div class="container px-3 mx-auto grid bg-gray-100">
<style>
	input, textarea, button, select, a { -webkit-tap-highlight-color: rgba(0,0,0,0); }
	button:focus{ outline:0 !important; } }
	
</style>

    <!-- component -->

    <div class="bg-white my-12 pb-6 w-full justify-center items-center overflow-hidden md:max-w-sm rounded-lg shadow-sm mx-auto">
      <div class="relative h-40">
        <img class="absolute h-full w-full object-cover" src="https://images.unsplash.com/photo-1448932133140-b4045783ed9e?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80">
      </div>

      <div class="relative shadow mx-auto h-24 w-24 -my-12 border-white rounded-full overflow-hidden border-4">
        <img class="object-cover w-full h-full" src="{{ $user->profile_photo_url }}">
      </div>

      <div class="mt-16">
        <h1 class="text-lg text-center font-semibold">
          {{ $user->name }} 
        </h1>
        <p class="text-sm text-gray-600 text-center">
          {{ '@' . $user->username }}
        </p>
        <div class="mx-auto text-center my-3">
        @can('is-not-user-profile', $user)
        	@if($user->isFollowed->count())
	        <button type="button" wire:click="incrementFollow({{ $user->id }})" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-md active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-red">
	      	<span wire:loading wire:target="incrementFollow({{ $user->id }})">Unfollowing...</span>
			  <span wire:loading.remove wire:target="incrementFollow({{ $user->id }})">Unfollow</span>
		    </button>
			@else
			<button type="button" wire:click="incrementFollow({{ $user->id }})" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
	      	<span wire:loading wire:target="incrementFollow({{ $user->id }})">Following...</span>
			  <span wire:loading.remove wire:target="incrementFollow({{ $user->id }})">Follow</span>
		    </button>
			@endif
	  @endcan
		</div>
      </div>

      <div class="mt-6 pt-3 flex flex-wrap mx-6 border-t">
        <div class="py-4 flex justify-center items-center w-full divide-x divide-gray-400 divide-solid">
			<span class="text-center px-4">
				<button
					id=""
					wire:click="showViewFollowersModal({{ $user->id }})"
					class="flex float-right items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-red-600 focus:outline-none focus:shadow-outline-gray"
					wire:offline.class.remove="text-blue-600"
					wire:offline.class="text-gray-400"
					aria-label="View Followers"
					wire:loading.class.remove="text-blue-600"
					wire:loading.class="bg-gray text-gray-400"
					wire:offline.attr="disabled"
				>
					View Followers
				</button>
				<span class="font-bold text-gray-700">{{ $followersCount }}</span>
				<span class="text-gray-600">Followers</span>
			</span>
			<span class="text-center px-4">
				<button
					id=""
					wire:click="showViewFollowingModal({{ $user->id }})"
					class="flex float-right items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-blue-600 rounded-lg dark:text-red-600 focus:outline-none focus:shadow-outline-gray"
					wire:offline.class.remove="text-blue-600"
					wire:offline.class="text-gray-400"
					aria-label="View Following"
					wire:loading.class.remove="text-blue-600"
					wire:loading.class="bg-gray text-gray-400"
					wire:offline.attr="disabled"
				>
					View Following
				</button>	
				<span class="font-bold text-gray-700">{{ $followingsCount }}</span>
				<span class="text-gray-600">Followings</span>
			</span>

			<span class="text-center px-4">
				<span class="font-bold text-gray-700"> {{ $postsCount }} </span>
				<span class="text-gray-600">Posts</span>
			</span>

			@include('elements.view-followers-modal')
			@include('elements.view-following-modal')
			
		</div>
      </div>
    </div>

	<!--My Post-->
	
	<?php
		//My posts
		$id = User::where('username', $user->username)->pluck('id');
		
		//$userIdsFollowing = Auth::user()->followings()->pluck('follower_id');
		$posts = Post::withCount(['likes', 'comments'])->whereIn('user_id', $id)->with(['userLikes', 'postImages', 'user' => function ($query) {
			$query->select(['id', 'name', 'username', 'profile_photo_path']);  
		},
		])->get(); 
		
		foreach($posts as $post){
			?>
			@include('elements.post')
			@include('elements.comments-post-modal')
			@include('elements.delete-post-modal')
			@include('elements.edit-post-modal')
			@include('elements.edit-comments-post-modal')
			@include('elements.share-post-modal')
			<?php
		}
		//My shared posts
		$name = User::where('username', $user->username)->value('name');
		$profilepic = User::where('username', $name)->get();

		foreach($profilepic as $profile){
			$profilepic = $profile->profile_photo_url;
		}
		$userIds = Share::where('user_id', $id)->select('post_id')->pluck('post_id'); 
	
        $mySharedPosts = Post::withCount(['likes', 'comments'])->whereIn('id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
            $query->select(['id', 'name', 'username', 'profile_photo_path']);  
        },
        ])->get();
	?>
		<center><h1>Shared Posts</h1></center>
	<?php
		foreach($mySharedPosts as $post){
			?>
			@include('elements.mypost')
			@include('elements.comments-post-modal')
			@include('elements.delete-post-modal')
			@include('elements.edit-post-modal')
			@include('elements.edit-comments-post-modal')
			@include('elements.share-post-modal')
			<?php
		}
	
	?>
</div>
