<x-app-layout>
    
<div class="container px-3 mx-auto grid bg-gray-100">
<style>
	input, textarea, button, select, a { -webkit-tap-highlight-color: rgba(0,0,0,0); }
	button:focus{ outline:0 !important; } }
	
</style>
	<center><h1>followers</h1></center>
    <livewire:posts.view :type="'followers'" />
	<center><h1>sharehome</h1></center>
	<livewire:posts.view :type="'shareHome'" />
	<center><h1>share</h1></center>
    <livewire:posts.view :type="'share'" />
	<center><h1>noShare</h1></center>
	<livewire:posts.view :type="'noShare'" />
	<center><h1>noShareFeed</h1></center>
	<livewire:posts.view :type="'noShareFeed'" />

</div>
            
</x-app-layout>
