<!-- Edit User Comment Modal -->
<x-jet-dialog-modal wire:model="isOpenEditPostModal">
    <x-slot name="title">
        {{ __('Edit Post') }}
    </x-slot>

    <x-slot name="content">
            
        @if(session()->has('post.error'))
            <div class="bg-red-100 border my-3 border-red-400 text-red-700 dark:bg-red-700 dark:border-red-600 dark:text-red-100 px-4 py-3 rounded relative" role="alert">
				  <span class="block sm:inline text-center">{{ session()->get('post.error') }}</span>
			</div>
        @endif
        <form wire:submit.prevent="editPost({{ $editPostId }})" >    
            <!--For Edit-->
            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="title" value="{{ __('Title') }}" />
                <x-jet-input wire:model.lazy="title" id="" name="title" type="text" class="mt-1 block w-full" placeholder="{{ (empty($post->title) ? '' : $post->title) }}" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="description" value="{{ __('Description') }}" />
                <x-jet-input wire:model.lazy="title" id="" name="body" id="" name="body" type="text" class="mt-1 block w-full" placeholder="{{ (empty($post->body) ? '' : $post->body) }}" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-jet-label for="location" value="{{ __('Location') }}" />
                <x-jet-input wire:model.lazy="title" id="" name="location" id="" name="location" type="text" class="mt-1 block w-full" placeholder="{{ (empty($post->location) ? '' : $post->location) }}" />
            </div>
            
            {{ __('Are you sure you want to edit this post?') }}
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button wire:click="$toggle('isOpenEditPostModal')" wire:loading.attr="enabled">
            {{ __('Cancel') }}
        </x-jet-secondary-button>

        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:border-red-700 focus:ring focus:ring-red-200 active:bg-red-600 disabled:opacity-25 transition" wire:loading.attr="enabled">
            {{ __('Edit') }}
        </button>
        
        </form>
    </x-slot>

</x-jet-dialog-modal>