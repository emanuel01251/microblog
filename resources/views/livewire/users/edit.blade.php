<div>
    <x-authentication-card>
        <x-slot name="logo">
            <!---<x-authentication-card-logo />-->
            <img src="{{ url('https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png') }}" class="block h-9 w-auto" />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" wire:submit.prevent="submit">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" wire:model.live.blur="name" />
            </div>

            <div class="mt-4">
                <x-label for="username" value="{{ __('Username') }}" />
                <x-input id="username" class="block mt-1 w-full" type="text" name="username" wire:model.live.blur="username" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" wire:model.live.blur="email" />
            </div>

            <div class="mt-4">
                <x-label for="is_private" value="{{ __('Account visibility') }}" />
                <select id="is_private" class="block border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" wire:model.live.blur="is_private">
                	<option value="0">Public</option>
	                <option value="1">Private</option>
                <select>
            </div>

            <div class="mt-4">
                <x-label for="is_private" value="{{ __('Account Role') }}" />
                <select id="is_private" class="block border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm mt-1 w-full" wire:model.live.blur="role_id">
                	<option value="1">User</option>
	                <option value="2">Admin</option>
                <select>
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" placeholder="Use Default password" name="password" wire:model.live.blur="password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"  wire:model.live.blur="password_confirmation" model="password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">

                <x-button class="ml-4">
                    {{ __('Update') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</div>
