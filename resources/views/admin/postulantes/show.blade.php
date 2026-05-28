<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Detalle del Postulante
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">{{ session('success') }}</div>
            @endif

            <!-- Datos personales -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100">Datos Personales</h3>
                    <span class="px-3 py-1 rounded text-sm font-medium
                        {{ $postulante->estado === 'admitido' ? 'bg-green-100 text-green-800' :
                           ($postulante->estado === 'habilitado' ? 'bg-blue-100 text-blue-800' :
                           ($postulante->estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) }}">
                        {{ ucfirst($postulante->estado) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Nombre</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->name }}</p></div>
                    <div><p class="text-gray-500">CI</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->ci }}</p></div>
                    <div><p class="text-gray-500">Email</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->email }}</p></div>
                    <div><p class="text-gray-500">Telefono</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->telefono ?? '—' }}</p></div>
                    <div><p class="text-gray-500">Colegio</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->colegio ?? '—' }}</p></div>
                    <div><p class="text-gray-500">Ciudad</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->ciudad ?? '—' }}</p></div>
                    <div><p class="text-gray-500">Sexo</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->sexo === 'M' ? 'Masculino' : ($postulante->sexo === 'F' ? 'Femenino' : '—') }}</p></div>
                    <div><p class="text-gray-500">Direccion</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $postulante->direccion ?? '—' }}</p></div>
                </div>
            </div>

            <!-- Inscripcion -->
            @if($inscripcion)
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-4">Inscripcion</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Estado</p>
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $inscripcion->estado === 'pagada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($inscripcion->estado) }}
                        </span>
                    </div>
                    <div><p class="text-gray-500">Fecha</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $inscripcion->fecha_inscripcion }}</p></div>
                    <div><p class="text-gray-500">1ra opcion</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $carreraPrimera->nombre ?? '—' }}</p></div>
                    <div><p class="text-gray-500">2da opcion</p><p class="font-medium text-gray-800 dark:text-gray-100">{{ $carreraSegunda->nombre ?? '—' }}</p></div>
                </div>
            </div>
            @endif

            <!-- Requisitos -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="font-bold text-lg text-gray-800 dark:text-gray-100 mb-4">Requisitos</h3>
                @forelse($requisitos as $req)
                <div class="border rounded p-4 mb-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $req->requisito_nombre }}</p>
                        <p class="text-xs text-gray-500 mt-1">Entregado: {{ $req->fecha_entrega }}</p>
                        @if($req->archivo_path)
                            <a href="{{ asset('storage/'.$req->archivo_path) }}" target="_blank"
                               class="text-blue-500 text-xs hover:underline">Ver archivo</a>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 rounded text-xs font-medium
                            {{ $req->estado === 'aprobado' ? 'bg-green-100 text-green-800' :
                               ($req->estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($req->estado) }}
                        </span>
                        <form method="POST" action="{{ route('admin.requisitos.validar', $req->id) }}" class="flex gap-1">
                            @csrf
                            <button name="estado" value="aprobado"
                                class="bg-green-500 text-white px-2 py-1 rounded text-xs hover:bg-green-600">Aprobar</button>
                            <button name="estado" value="rechazado"
                                class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Rechazar</button>
                        </form>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-sm">No hay requisitos entregados.</p>
                @endforelse
            </div>

            <a href="{{ route('admin.postulantes.index') }}"
               class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Volver</a>

        </div>
    </div>
</x-app-layout>
