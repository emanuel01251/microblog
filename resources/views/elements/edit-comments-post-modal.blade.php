<!-- Edit User Comment Modal -->
<x-dialog-modal wire:model="isOpenEditCommentModal">
    <x-slot name="title">
        {{ __('Edit Comment') }}
    </x-slot>

    <x-slot name="content">
            
        @if(session()->has('post.error'))
            <div class="bg-red-100 border my-3 border-red-400 text-red-700 dark:bg-red-700 dark:border-red-600 dark:text-red-100 px-4 py-3 rounded relative" role="alert">
				  <span class="block sm:inline text-center">{{ session()->get('post.error') }}</span>
			</div>
        @endif
        <form wire:submit.prevent="editComment({{ $editCommentId }})" >    
            <!--For edit comment-->
            <div class="col-span-6 sm:col-span-4">
                <x-label for="comment" value="{{ __('Your Comment') }}" />
                @foreach($comments as $comment)
                    @if($comment->id == $editCommentId)
                        <x-input wire:model.live.blur="commentSection" id="" name="commentSection" type="text" class="mt-1 block w-full" value="{{ $comment->comment }}" />
                    @endif    
                @endforeach
            </div>

            {{ __('Are you sure you want to edit your comment in this post?') }}
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('isOpenEditCommentModal')" wire:loading.attr="enabled">
            {{ __('Cancel') }}
        </x-secondary-button>

        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition" wire:loading.attr="enabled">
            {{ __('Edit') }}
        </button>
        
        </form>
    </x-slot>

</x-dialog-modal>
