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

    public $isOpenCommentModal = false;

    public $isOpenDeletePostModal = false;
    
    public $isOpenEditPostModal = false;

    public $isOpenEditCommentModal = false;

    public $isOpenShareModal = false;

    public function mount($type = null)
    {
        $this->queryType = $type;
    }

    public function render()
    {
        $posts = $this->setQuery();
        $shares = $this->setQueryForShares();
        
        return view('livewire.posts.view', ['posts' => $posts, 'shares' => $shares]);
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

    private function setQueryForShares(){
        if (! empty($this->queryType) && $this->queryType === 'shareOtherUser') {
            $userIds = Auth::user()->followings()->pluck('follower_id');
            $userIds[] = Auth::id();
            
            $shares = Share::whereIn('user_id', $userIds)->select('caption', 'post_id', 'user_id')->get();

        }
        else if (! empty($this->queryType) && $this->queryType === 'MyShare') {
            $shares = Share::where('user_id', Auth::id())->select('caption', 'post_id', 'user_id')->get();
        }
        else if(! empty($this->queryType) && $this->queryType === 'MyShareNoContent'){
            $userIds = Post::onlyTrashed()->pluck('id');
            $shares = Share::where('user_id', auth()->user()->id)->select('post_id')->pluck('post_id'); 
        }
        else if(! empty($this->queryType) && $this->queryType === 'shareNoContentUser'){
            $userIds = Post::onlyTrashed()->pluck('id');
            $shares = Share::where('user_id', Auth::id())->select('caption', 'post_id', 'user_id')->get();
        }
        else{
            $shares = Share::whereIn('user_id', [0])->select('caption', 'post_id')->get();
        }
        
        return $shares;
    }
    
    private function setQuery(){
        if (! empty($this->queryType) && $this->queryType === 'me') {
            $userIds = Auth::user()->pluck('id');
            //$userIdsFollowing = Auth::user()->followings()->pluck('follower_id');

            $posts = Post::withCount(['likes', 'comments'])/*->whereNull('deleted_at')*/->where('user_id', Auth::id())->with(['userLikes', 'postImages', 'user' => function ($query) {
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
        //My Post - my shared post
        if (! empty($this->queryType) && $this->queryType === 'MyShare') {
            
            $userIds = Share::where('user_id', auth()->user()->id)->select('post_id')->pluck('post_id'); 
            
            $posts = Post::withCount(['likes', 'comments'])->whereIn('id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
                $query->select(['id', 'name', 'username', 'profile_photo_path']);  
            },
            ])->latest()->paginate(10);

        }
        //No Content Found of other users - Feeds
        if (! empty($this->queryType) && $this->queryType === 'shareNoContentUser') {
            $count = 0;
            $noContent = 0;

            $userIds = Auth::user()->followings()->pluck('follower_id');
            $userIds1 = Post::onlyTrashed()->pluck('id');
            $trashIds = Post::onlyTrashed()->pluck('id');
            $this->sharedBy = User::whereIn('id', $userIds)->value('name');

            //This code is for "Shared by: " logic
            foreach($userIds as $followingIds){
                foreach($trashIds as $trash){
                    $new = Share::where('user_id', $followingIds)->select('post_id')->pluck('post_id');
                }
            }
            
            //This code is for No Content Found
            foreach($userIds as $var1){
                foreach($userIds as $var2){
                    if($var1 == $var2){
                        $count++;
                    }
                    else{
                        $count += 0;
                    }
                }
            }
            if($count >= 1){
                foreach($userIds1 as $deletedIds){
                    foreach($userIds as $postIds){
                        if($postIds == $deletedIds ){
                            $noContent++;
                            
                        }
                        else{
                           
                        }
                    }
                }
                
                $posts = Post::withCount(['likes', 'comments'])->whereIn('id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                },
                ])->latest()->paginate(10);
            }else{
                $userIds = $userIds1;
                $posts = Post::withCount(['likes', 'comments'])->whereIn('id', [0])->with(['userLikes', 'postImages', 'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                },
                ])->latest()->paginate(10);
            }   
            $this->noContent = $noContent;
            $this->count = $count;
        }
        //No Content Found - My Post
        if (! empty($this->queryType) && $this->queryType === 'MyShareNoContent') {
            
            $userIds = Post::onlyTrashed()->pluck('id');
            $userIds1 = Share::where('user_id', auth()->user()->id)->select('post_id')->pluck('post_id'); 
            $count = 0;
            $noContent = 0;

            foreach($userIds1 as $var1){
                foreach($userIds as $var2){
                    if($var1 == $var2){
                        $count++;
                    }
                    else{
                        $count += 0;
                    }
                }
            }
                
            if($count >= 1){
                foreach($userIds as $deletedIds){
                    foreach($userIds1 as $postIds){
                        if($postIds == $deletedIds ){
                            $this->noContent++;
                            break;
                        }
                        else{
                           $noContent = $count;
                        }
                    }
                }
                $posts = Post::withCount(['likes', 'comments'])->whereIn('id', $userIds)->with(['userLikes', 'postImages', 'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                },
                ])->latest()->paginate(10);
            }else{
                $userIds = $userIds1;
                $posts = Post::withCount(['likes', 'comments'])->whereIn('id', [0])->with(['userLikes', 'postImages', 'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                },
                ])->latest()->paginate(10);
            }   
        } 
        //Code for a Share post of other users in Feeds
        if (! empty($this->queryType) && $this->queryType === 'shareOtherUser') {
            $userIds = Auth::user()->followings()->pluck('follower_id');
            $userPosts = Share::whereIn('user_id', $userIds)->select('post_id')->pluck('post_id');
            $shareCaption = Share::whereIn('user_id', $userIds)->select('caption')->pluck('caption');

            if($userPosts == "[]"){
                $posts = Post::withCount(['likes', 'comments'])->whereIn('id', [0])->with(['userLikes', 'postImages', 'user' => function ($query) {
                    $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                },
                ])->latest()->paginate(10);  
            }else{
                //Caption
                foreach($userIds as $ids){
                    foreach($shareCaption as $caption){
                        $userCaption = Share::where('caption', $caption)->where('user_id', $ids)->value('caption');
                        if($userCaption == $caption){

                            $posts = Post::withCount(['likes', 'comments'])->whereIn('id', $userPosts)->with(['userLikes', 'postImages', 'user' => function ($query) {
                                $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                            },
                            ])->latest()->paginate(10);
                            
                        }else{
                            $posts = Post::withCount(['likes', 'comments'])->whereIn('id', $userPosts)->with(['userLikes', 'postImages', 'user' => function ($query) {
                                $query->select(['id', 'name', 'username', 'profile_photo_path']);  
                            },
                            ])->latest()->paginate(10);  
                        }
                    }   
                }
            }
        }
        return $posts;
    }
}
