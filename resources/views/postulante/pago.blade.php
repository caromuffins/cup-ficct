<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pago de Inscripcion
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="bg-red-100 text-red-800 p-4 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8">

                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">Pago Seguro con Stripe</h3>
                    <p class="text-gray-500 text-sm mt-2">Seras redirigido a la plataforma segura de Stripe</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Concepto</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">Inscripcion CUP FICCT</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-2">
                        <span class="text-gray-500">Gestion</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $gestion->periodo }} {{ $gestion->anio }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t">
                        <span class="font-bold text-gray-800 dark:text-gray-100">Total</span>
                        <span class="font-bold text-xl text-blue-600">${{ $gestion->monto_inscripcion }} USD</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('postulante.pago.crear') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-blue-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-blue-700 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Pagar con Stripe
                    </button>
                </form>

                <p class="text-center text-xs text-gray-400 mt-4">
                    Tu pago es procesado de forma segura por Stripe. No almacenamos datos de tu tarjeta.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
