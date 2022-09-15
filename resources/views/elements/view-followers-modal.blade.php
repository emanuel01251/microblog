<!-- Edit User Comment Modal -->
<x-jet-dialog-modal wire:model="isOpenViewFollowersModal">
    <x-slot name="title">
        {{ __('Your Followers') }}
    </x-slot>

    <x-slot name="content">
            
        @if(session()->has('post.error'))
            <div class="bg-red-100 border my-3 border-red-400 text-red-700 dark:bg-red-700 dark:border-red-600 dark:text-red-100 px-4 py-3 rounded relative" role="alert">
				  <span class="block sm:inline text-center">{{ session()->get('post.error') }}</span>
			</div>
        @endif
        <form>    
            <!--View Followers-->

            {{ $showFollower1; }}
    </x-slot>

    <x-slot name="footer">
        <x-jet-secondary-button wire:click="$toggle('isOpenViewFollowersModal')" wire:loading.attr="enabled">
            {{ __('Ok') }}
        </x-jet-secondary-button>
        
        </form>
    </x-slot>

</x-jet-dialog-modal>