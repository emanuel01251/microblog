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
<div>
    <div class="container">
	    <div class="row">
	        <div class="col-md-12">	            
	            <input type="text"  class="form-control" placeholder="Search" wire:model="searchTerm" />
	            <table class="table table-bordered" style="margin: 10px 0 10px 0;">
	                <tr>
						<th></th>
	                    <th>Name</th>
                        <th>Username</th>
						<th>Link</th>
	                </tr>
	                @foreach($users as $user)
						<tr>
							<?php
								//Profile Picture
								$id = User::where('username', $user->username)->pluck('id');
									
								$posts = User::whereIn('id', $id)->get(); 
									
								foreach($posts as $post){
									if($post->username == $user->username){
										?>	
											<td>
												<img src="{{ $post->profile_photo_url }}" alt="{{ $post->username }}" class="inline-block object-cover w-8 h-8 mr-1 text-white rounded-full shadow-sm cursor-pointer" 
													wire:offline.class="filter grayscale">
											</td>
										<?php
										break;
									}
								}
							?>
						<td>
							{{ $user->name }}
						</td>
						<td>
							{{ $user->username }}
						</td>
						<td>
							<a href="{{ $user->username }}" style="color: blue">View Profile</a>
						</td>
						</tr>
	                @endforeach
	            </table>

	            {{ $users->links() }}
	        </div>
	    </div>
	</div>
</div>