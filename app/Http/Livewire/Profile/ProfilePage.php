<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Follower;
use App\Models\Share;
use App\Models\Post;
use Auth;
use Exception;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class ProfilePage extends Component
{
    public $user;

    public $showFollower;

    public $showFollower1 = [];

    public $showFollowing;

    public $showFollowing1 = [];

    public $followers;

    public $viewFollowersId;

    public $isOpenViewFollowersModal = false;

    public $isOpenViewFollowingModal = false;

    public $followersCount;

    public $followings;

    public $followingsCount;

    public $posts;
    
    public $postsCount;

    public $comments = [];  

    public $comment;

    public $commentSection;

    public $title;

    public $caption;
    
    public $count = 0;

    public $noContent;

    public $i = 0;

    public $body;

    public $location;

    public $type;

    public $queryType;

    public $postId;

    public $deletePostId;
    
    public $editPostId;

    public $editCommentId;

    public $sharePostId;

    public $sharedBy;
    
    public $sharedBy1;

    public $multipleShared;

    public $multipleSharedUser1;
    
    public $shareCaptionSameUser;

    public $conditionSharedBy;

    public $shareCaption;

    public $shareUser;

    public $shareCaption1 = [];
    
    public $count1;

    public $deletedIds = [];

    public $userDeleted = [];

    public $isOpenCommentModal = false;

    public $isOpenDeletePostModal = false;
    
    public $isOpenEditPostModal = false;

    public $isOpenEditCommentModal = false;

    public $isOpenShareModal = false;

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

    public function incrementLike(Post $post)
    {
        $like = Like::where('user_id', Auth::id())
            ->where('post_id', $post->id);

        if (! $like->count()) {
            $new = Like::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);
            session()->flash('success', 'You liked the post!');
            return redirect()->home();
        }
        $like->delete();
        session()->flash('success', 'You unliked the post!');
        return redirect()->home();
    }

    public function sharePost(Post $post)
    {
        $share = Share::where('user_id', Auth::id())
            ->where('post_id', $post->id);
        if (! $share->count()) {
            $new = Share::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
                'caption' => $this->caption,
            ]);
            session()->flash('success', 'You shared the post!');
            $this->isOpenShareModal = false;
            return true;
        }
        return true;
    }

    public function comments($post)
    {
        $post = Post::with(['comments.user' => function ($query) {
            $query->select('id', 'name');
        },
        ])->find($post);
        $this->postId = $post->id;
        $this->resetValidation('comment');
        $this->isOpenCommentModal = true;
        $this->setComments($post);
        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function createComment(Post $post)
    {
        $validatedData = Validator::make(
            ['comment' => $this->comment],
            ['comment' => 'required|max:5000']
        )->validate();

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'comment' => $validatedData['comment'],
        ]);

        session()->flash('success', 'Comment created successfully');

        $this->setComments($post);
        $this->comment = '';

        //$this->isOpenCommentModal = false;
        return redirect()->home();
    }

    public function setComments($post)
    {
        $this->comments = $post->comments;
        return true;
    }

    public function showDeletePostModal(Post $post)
    {
        $this->deletePostId = $post->id;
        $this->isOpenDeletePostModal = true;
    }

    public function showEditPostModal(Post $post)
    {
        $this->editPostId = $post->id;
        $this->isOpenEditPostModal = true;
    }

    public function showShareModal(Post $post)
    {
        $this->sharePostId = $post->id;
        $this->isOpenShareModal = true;
        
        $shareCondition = Share::where('user_id', Auth::id())
            ->where('post_id', $post->id)->value('post_id');
        if($shareCondition != NULL){
            $share = Share::where('user_id', Auth::id())
            ->where('post_id', $post->id);
            $share->delete();
            $this->isOpenShareModal = false;
            session()->flash('success', 'You unshared the post!');
        }

    }

    public function deletePost(Post $post)
    {
        $response = Gate::inspect('delete', $post);

        if ($response->allowed()) {
            try {
                $post->delete();
                session()->flash('success', 'Post deleted successfully');
            } catch (Exception $e) {
                session()->flash('error', 'Cannot delete post');
            }
        } else {
            session()->flash('error', $response->message());
        }
        $this->isOpenDeletePostModal = false;
        return redirect()->back();
    }

    public function editPost(Post $post)
    {
        Post::where('id', $post->id)->update(['title' => $this->title, 'body' => $this->body]);
        $this->isOpenEditPostModal = false;
        session()->flash('success', 'Post edited sucessfully');
        return redirect()->back();
    }

    public function editComment(Comment $comment)
    {
        Comment::where('id', $comment->id)->update(['comment' => $this->commentSection]);
        $this->isOpenEditCommentModal = false;
        session()->flash('success', 'Comment edited successfully');
        return redirect()->home();
    }

    public function showEditCommentModal(Comment $comment)
    {
        $this->editCommentId = $comment->id;
        $this->isOpenEditCommentModal = true;
    }

    public function deleteComment(Post $post, Comment $comment)
    {
        $response = Gate::inspect('delete', [$comment, $post]);

        if ($response->allowed()) {
            $comment->delete();
            $this->isOpenCommentModal = false;
            session()->flash('success', 'Comment deleted successfully');
        } else {
            session()->flash('comment.error', $response->message());
        }

        return redirect()->back();
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

        if($showFollower == ''){
            $this->showFollower1 = "You don't have follower.";
        }else{
            $g = 0;
            $following = User::whereIn('id', $showFollower)->pluck('id');
            foreach($following as $follow){
                $this->showFollower1[$g] = $follow;
                $g++;
            }
        }
        
    }

    public function showViewFollowingModal(User $user)
    {
        Gate::authorize('is-user-profile', $this->user);
        $this->isOpenViewFollowingModal = true;
        $showFollowing = Follower::where('following_id', $user->id)->pluck('follower_id'); 
        
        if($showFollowing == ''){
            $this->showFollowing1 = "You haven't follow anyone.";
        }else{
            $g = 0;
            $following = User::whereIn('id', $showFollowing)->pluck('id');
            foreach($following as $follow){
                $this->showFollowing1[$g] = $follow;
                $g++;
            }
        }
    }
}
