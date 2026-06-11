<x-guest-layout>
    @php $activeRole = old('rol_esperado', 'postulante'); @endphp

    {{-- Tabs de rol --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button type="button" onclick="selectTab('admin')" id="tab-admin"
            class="flex-1 py-2.5 text-sm font-semibold text-center transition-colors border-b-2">
            Administrador
        </button>
        <button type="button" onclick="selectTab('docente')" id="tab-docente"
            class="flex-1 py-2.5 text-sm font-semibold text-center transition-colors border-b-2">
            Docente
        </button>
        <button type="button" onclick="selectTab('postulante')" id="tab-postulante"
            class="flex-1 py-2.5 text-sm font-semibold text-center transition-colors border-b-2">
            Estudiante
        </button>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('role_error'))
    <div class="mb-4 p-3 rounded-md text-sm" style="background-color:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
        {{ session('role_error') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="rol_esperado" id="rol_esperado" value="{{ $activeRole }}">
        <input type="hidden" name="login_tipo"   id="login_tipo"
               value="{{ $activeRole === 'postulante' ? 'ci' : 'email' }}">

        {{-- CI — solo para Estudiante --}}
        <div id="campo-ci" style="{{ $activeRole !== 'postulante' ? 'display:none;' : '' }}">
            <x-input-label for="ci" value="Carnet de Identidad (CI)" />
            <x-text-input id="ci" class="block mt-1 w-full" type="text" name="ci"
                :value="old('ci')" autocomplete="off" />
            <x-input-error :messages="$errors->get('ci')" class="mt-2" />
        </div>

        {{-- Email — para Admin y Docente --}}
        <div id="campo-email" style="{{ $activeRole === 'postulante' ? 'display:none;' : '' }}">
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                :value="old('email')" autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- Contraseña --}}
        <div class="mt-4">
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        {{-- Recordarme --}}
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 shadow-sm" name="remember">
                <span class="ms-2 text-sm text-gray-600">Recordarme</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm hover:underline" style="color: #1F4E79;"
                   href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <button type="submit"
                class="text-white font-semibold py-2 px-6 rounded-md transition-colors duration-150"
                style="background-color: #1F4E79;"
                onmouseover="this.style.backgroundColor='#163a5f'"
                onmouseout="this.style.backgroundColor='#1F4E79'">
                Ingresar al Sistema
            </button>
        </div>

        @if (Route::has('register'))
        <div id="registro-link" class="mt-5 pt-4 border-t border-gray-100 text-center"
             style="{{ $activeRole !== 'postulante' ? 'display:none;' : '' }}">
            <p class="text-sm text-gray-500">
                ¿Primera vez en el sistema?
                <a href="{{ route('register') }}" class="font-semibold hover:underline"
                   style="color: #1F4E79;">
                    Crear cuenta
                </a>
            </p>
        </div>
        @endif
    </form>

    <script>
    function selectTab(rol) {
        document.getElementById('rol_esperado').value = rol;

        var esCi = (rol === 'postulante');
        document.getElementById('login_tipo').value    = esCi ? 'ci' : 'email';
        document.getElementById('campo-ci').style.display    = esCi ? '' : 'none';
        document.getElementById('campo-email').style.display = esCi ? 'none' : '';

        var registroLink = document.getElementById('registro-link');
        if (registroLink) registroLink.style.display = esCi ? '' : 'none';

        document.getElementById('ci').required    = esCi;
        document.getElementById('email').required = !esCi;

        ['admin', 'docente', 'postulante'].forEach(function(r) {
            var btn = document.getElementById('tab-' + r);
            if (r === rol) {
                btn.style.borderColor = '#1F4E79';
                btn.style.color       = '#1F4E79';
            } else {
                btn.style.borderColor = 'transparent';
                btn.style.color       = '#6b7280';
            }
        });
    }

    selectTab('{{ $activeRole }}');
    </script>
</x-guest-layout>
