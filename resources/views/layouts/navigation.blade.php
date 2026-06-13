<style>
.cup-nav-link {
    padding: 4px 11px;
    border-radius: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    text-decoration: none;
    color: #93c5fd;
    transition: background-color 0.15s, color 0.15s;
    white-space: nowrap;
}
.cup-nav-link:hover { color: white; background-color: rgba(255,255,255,0.12); }
.cup-nav-link.active { color: white; background-color: rgba(255,255,255,0.2); }
</style>

<nav x-data="{ open: false }" style="background-color: #1F4E79;" class="border-b border-blue-900 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 gap-2">

            {{-- Logo --}}
            <div class="flex items-center shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center shrink-0 shadow">
                        <span style="color:#1F4E79; font-size:9px; font-weight:800; text-align:center; line-height:1.1;">CUP<br>FICCT</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-white font-bold text-sm leading-tight">Sistema CUP FICCT &ndash; UAGRM</p>
                        <p class="text-blue-200 text-xs leading-tight">Cursos Pre Universitarios</p>
                    </div>
                    <p class="text-white font-bold text-sm sm:hidden">CUP FICCT</p>
                </a>
            </div>

            {{-- Nav links por rol (desktop) --}}
            @auth
            <div class="hidden lg:flex items-center gap-0.5 flex-1 overflow-x-auto mx-4">
                @if(auth()->user()->role === 'admin')
                    @php
                    $links = [
                        ['route' => 'admin.postulantes.index', 'label' => 'Postulantes', 'match' => 'admin.postulantes.*'],
                        ['route' => 'admin.gestiones.index',   'label' => 'Gestiones',   'match' => 'admin.gestiones.*'],
                        ['route' => 'admin.grupos.index',      'label' => 'Grupos',       'match' => 'admin.grupos.*'],
                        ['route' => 'admin.docentes.index',    'label' => 'Docentes',     'match' => 'admin.docentes.*'],
                        ['route' => 'admin.horarios.index',    'label' => 'Horarios',     'match' => 'admin.horarios.*'],
                        ['route' => 'admin.notas.index',       'label' => 'Notas',        'match' => 'admin.notas.*'],
                        ['route' => 'admin.admision.index',    'label' => 'Admisión',     'match' => 'admin.admision.*'],
                        ['route' => 'admin.reportes.index',    'label' => 'Reportes',     'match' => 'admin.reportes.*'],
                        ['route' => 'admin.bitacora.index',    'label' => 'Bitácora',     'match' => 'admin.bitacora.*'],
                    ];
                    @endphp
                    @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="cup-nav-link {{ request()->routeIs($link['match']) ? 'active' : '' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach

                @elseif(auth()->user()->role === 'docente')
                    @php
                    $links = [
                        ['route' => 'docente.dashboard', 'label' => 'Dashboard',   'match' => 'docente.dashboard'],
                        ['route' => 'docente.grupos',    'label' => 'Mis Grupos',  'match' => 'docente.grupos'],
                        ['route' => 'docente.asistencia','label' => 'Asistencia',  'match' => 'docente.asistencia*'],
                        ['route' => 'docente.notas',     'label' => 'Notas',       'match' => 'docente.notas*'],
                        ['route' => 'docente.horario',   'label' => 'Mi Horario',  'match' => 'docente.horario'],
                    ];
                    @endphp
                    @foreach($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="cup-nav-link {{ request()->routeIs($link['match']) ? 'active' : '' }}">
                        {{ $link['label'] }}
                    </a>
                    @endforeach
                @endif
            </div>
            @endauth

            {{-- User dropdown (desktop) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 shrink-0">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border rounded-md text-sm leading-4 font-medium transition ease-in-out duration-150 focus:outline-none"
                            style="border-color: rgba(147,197,253,0.4); color: #bfdbfe;"
                            onmouseover="this.style.color='white'; this.style.borderColor='rgba(255,255,255,0.6)'"
                            onmouseout="this.style.color='#bfdbfe'; this.style.borderColor='rgba(147,197,253,0.4)'">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md transition duration-150 ease-in-out focus:outline-none"
                    style="color: #93c5fd;"
                    onmouseover="this.style.color='white'; this.style.backgroundColor='rgba(255,255,255,0.1)'"
                    onmouseout="this.style.color='#93c5fd'; this.style.backgroundColor='transparent'">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="background-color: #163a5f;">
        <div class="pt-2 pb-3 space-y-1 px-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Panel Principal
            </x-responsive-nav-link>

            @auth
            @if(auth()->user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.postulantes.index')" :active="request()->routeIs('admin.postulantes.*')">Postulantes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.gestiones.index')" :active="request()->routeIs('admin.gestiones.*')">Gestiones</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.grupos.index')" :active="request()->routeIs('admin.grupos.*')">Grupos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.docentes.index')" :active="request()->routeIs('admin.docentes.*')">Docentes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.horarios.index')" :active="request()->routeIs('admin.horarios.*')">Horarios</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.notas.index')" :active="request()->routeIs('admin.notas.*')">Notas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.admision.index')" :active="request()->routeIs('admin.admision.*')">Admisión</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reportes.index')" :active="request()->routeIs('admin.reportes.*')">Reportes</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.bitacora.index')" :active="request()->routeIs('admin.bitacora.*')">Bitácora</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.grupos')" :active="request()->routeIs('docente.grupos')">Mis Grupos</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.asistencia')" :active="request()->routeIs('docente.asistencia*')">Asistencia</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.notas')" :active="request()->routeIs('docente.notas*')">Notas</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('docente.horario')" :active="request()->routeIs('docente.horario')">Mi Horario</x-responsive-nav-link>
            @endif
            @endauth
        </div>

        <div class="pt-4 pb-1 border-t border-blue-800">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-blue-300">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Perfil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
