<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- CI -->
        <div class="mt-4">
            <x-input-label for="ci" value="Carnet de Identidad" />
            <x-text-input id="ci" class="block mt-1 w-full" type="text"
                name="ci" :value="old('ci')" required autofocus />
            <x-input-error :messages="$errors->get('ci')" class="mt-2" />
        </div>

        <!-- Fecha de nacimiento -->
        <div class="mt-4">
            <x-input-label for="fecha_nacimiento" value="Fecha de Nacimiento" />
            <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date"
                name="fecha_nacimiento" :value="old('fecha_nacimiento')" required />
            <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
        </div>

        <!-- Telefono -->
        <div class="mt-4">
            <x-input-label for="telefono" value="Telefono" />
            <x-text-input id="telefono" class="block mt-1 w-full" type="text"
                name="telefono" :value="old('telefono')" required />
            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
        </div>

        <!-- Colegio -->
        <div class="mt-4">
            <x-input-label for="colegio" value="Colegio de Procedencia" />
            <x-text-input id="colegio" class="block mt-1 w-full" type="text"
                name="colegio" :value="old('colegio')" required />
            <x-input-error :messages="$errors->get('colegio')" class="mt-2" />
        </div>

        <!-- Ciudad -->
        <div class="mt-4">
            <x-input-label for="ciudad" value="Ciudad" />
            <x-text-input id="ciudad" class="block mt-1 w-full" type="text"
                name="ciudad" :value="old('ciudad')" required />
            <x-input-error :messages="$errors->get('ciudad')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
