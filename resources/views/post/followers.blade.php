<x-app-layout>
    
<div class="container px-3 mx-auto grid bg-gray-100">
<style>
	input, textarea, button, select, a { -webkit-tap-highlight-color: rgba(0,0,0,0); }
	button:focus{ outline:0 !important; } }
	
</style>

    <livewire:posts.view :type="'followers'" />
	
	<livewire:posts.view :type="'shareHome'" />
	
    <livewire:posts.view :type="'share'" />
	
	<livewire:posts.view :type="'noShare'" />
	
	<livewire:posts.view :type="'noShareFeed'" />

</div>
            
</x-app-layout>
