<x-app-layout>

    <div class="container px-3 mx-auto grid bg-gray-100">
        <style>
            input, textarea, button, select, a { -webkit-tap-highlight-color: rgba(0,0,0,0); }
            button:focus{ outline:0 !important; } }
            
        </style>

        <livewire:posts.view :type="'me'" />
        <center><h1>Your Shared Posts</h1></center>
        <livewire:posts.view :type="'MyShare'" />
        <livewire:posts.view :type="'MyShareNoContent'" />

    </div>
            
</x-app-layout>
