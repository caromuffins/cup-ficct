<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mi Inscripcion
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($inscripcion)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Estado de tu Inscripcion</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-gray-500 text-sm">Estado</p>
                            <span class="px-2 py-1 rounded text-sm font-medium
                                {{ $inscripcion->estado === 'pagada' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($inscripcion->estado) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-gray-500 text-sm">Fecha de inscripcion</p>
                            <p class="font-medium text-gray-800">{{ $inscripcion->fecha_inscripcion }}</p>
                        </div>
                    </div>

                    @if($inscripcion->estado === 'pendiente' && $gestion)
                        <a href="{{ route('postulante.pago.crear') }}"
                           class="inline-block bg-green-500 text-gray-900 font-bold px-6 py-3 rounded hover:bg-green-400 mb-4 border border-green-700">
                            Pagar inscripcion - ${{ $gestion->monto_inscripcion }} USD
                        </a>
                    @endif

                    <div>
                        <a href="{{ route('postulante.requisitos.index') }}"
                           class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Ver mis requisitos
                        </a>
                    </div>
                </div>
            @else
                @if($gestion)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="font-bold text-lg mb-4 text-gray-800">Nueva Inscripcion</h3>
                        <p class="text-gray-500 text-sm mb-4">
                            Gestion activa: {{ $gestion->periodo }} {{ $gestion->anio }}
                            — Monto: ${{ $gestion->monto_inscripcion }}
                        </p>

                        <form method="POST" action="{{ route('postulante.inscripcion.store') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    Primera opcion de carrera
                                </label>
                                <select name="carrera_primera_id"
                                    class="w-full border rounded px-3 py-2">
                                    <option value="">Selecciona una carrera</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('carrera_primera_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    Segunda opcion de carrera
                                </label>
                                <select name="carrera_segunda_id"
                                    class="w-full border rounded px-3 py-2">
                                    <option value="">Selecciona una carrera</option>
                                    @foreach($carreras as $carrera)
                                        <option value="{{ $carrera->id }}">{{ $carrera->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('carrera_segunda_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                                Inscribirme
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-yellow-100 text-yellow-800 p-4 rounded">
                        No hay una gestion activa en este momento.
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
