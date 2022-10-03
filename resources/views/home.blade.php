<x-app-layout>
    
<div class="container px-3 mx-auto grid bg-gray-100">
<style>
	input, textarea, button, select, a { -webkit-tap-highlight-color: rgba(0,0,0,0); }
	button:focus{ outline:0 !important; } }
	
</style>
	<center><h1>followers</h1></center>
    <livewire:posts.view :type="'followers'" />
	<center><h1>shareOtherUser</h1></center>
	<livewire:posts.view :type="'shareOtherUser'" />
	<center><h1>MyShare</h1></center>
    <livewire:posts.view :type="'MyShare'" />
	<center><h1>MyShareNoContent</h1></center>
	<livewire:posts.view :type="'MyShareNoContent'" />
	<center><h1>shareNoContentUser</h1></center>
	<livewire:posts.view :type="'shareNoContentUser'" />

</div>
            
</x-app-layout>
