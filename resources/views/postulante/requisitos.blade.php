<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Mis Requisitos
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-100">
                    Documentos requeridos
                </h3>

                @foreach($requisitos as $requisito)
                    <div class="border rounded p-4 mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $requisito->nombre }}
                                @if($requisito->obligatorio)
                                    <span class="text-red-500 text-xs ml-1">*obligatorio</span>
                                @endif
                            </h4>
                            @if(isset($requisitosEntregados[$requisito->id]))
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $requisitosEntregados[$requisito->id]->estado === 'aprobado' ? 'bg-green-100 text-green-800' :
                                       ($requisitosEntregados[$requisito->id]->estado === 'rechazado' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($requisitosEntregados[$requisito->id]->estado) }}
                                </span>
                            @else
                                <span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                    Pendiente
                                </span>
                            @endif
                        </div>

                        @if($requisito->descripcion)
                            <p class="text-gray-500 text-sm mb-3">{{ $requisito->descripcion }}</p>
                        @endif

                        <form method="POST" action="{{ route('postulante.requisitos.store') }}"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="requisito_id" value="{{ $requisito->id }}">
                            <div class="flex items-center gap-3">
                                <input type="file" name="archivo"
                                    class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                           file:rounded file:border-0 file:text-sm file:font-medium
                                           file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <button type="submit"
                                    class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                    Subir
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>