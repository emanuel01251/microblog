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

class View extends Component
{
    use WithPagination;

    public $comments = [];

    public $comment;

    public $commentSection;

    public $title;

    public $body;

    public $location;

    public $type;

    public $queryType;

    public $postId;

    public $deletePostId;
    
    public $editPostId;

    public $editCommentId;

    public $isOpenCommentModal = false;

    public $isOpenDeletePostModal = false;
    
    public $isOpenEditPostModal = false;

    public $isOpenEditCommentModal = false;

    public function mount($type = null)
    {
        $this->queryType = $type;
    }

    public function render()
    {
        $posts = $this->setQuery();

        return view('livewire.posts.view', ['posts' => $posts]);
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
            return true;
        }
        $like->delete();
        session()->flash('success', 'You unliked the post!');
    }

    public function incrementShare(Post $post)
    {
        $share = Share::where('user_id', Auth::id())
            ->where('post_id', $post->id);

        if (! $share->count()) {
            $new = Share::create([
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ]);
            session()->flash('success', 'You shared the post!');
            return true;
        }
        $share->delete();
        session()->flash('success', 'You unshared the post!');
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

    private function setQuery()
    {
        if (! empty($this->queryType) && $this->queryType === 'me') {
            $posts = Post::withCount(['likes', 'comments'])->where('user_id', Auth::id())->with(['userLikes', 'postImages', 'user' => function ($query) {
                $query->select(['id', 'name', 'username', 'profile_photo_path']);
            },
            ])->latest()->paginate(10);
        } elseif (! empty($this->queryType) && $this->queryType === 'followers') {
            $userIds = Auth::user()->followings()->pluck('follower_id');
            $userIds[] = Auth::id();
            $posts = Post::withCount(['likes', 'comments'])->whereIn('user_id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
                $query->select(['id', 'name', 'username', 'profile_photo_path']);
            },
            ])->latest()->paginate(10);
        } else {
            $posts = Post::withCount(['likes', 'comments'])->with(['userLikes', 'postImages', 'user' => function ($query) {
                $query->select(['id', 'name', 'username', 'profile_photo_path']);
            },
            ])->latest()->paginate(10);
        }
        $userIds = Share::where('user_id', auth()->user()->id)->select('post_id')->value('post_id'); 
        if (! empty($this->queryType) && $this->queryType === 'share') {
            while($userIds == auth()->user()->id){
                
                    $userIds = Share::where('user_id', auth()->user()->id)->select('post_id')->value('post_id'); 
                    
                    $posts = Post::withCount(['likes', 'comments'])->where('id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
                        $query->select(['id', 'name', 'username', 'profile_photo_path']);
                    },
                    ])->latest()->paginate(10);
            
                
            }
        } 

        return $posts;
    }
}
