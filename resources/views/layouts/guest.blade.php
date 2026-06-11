<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema CUP FICCT') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        <!-- Cabecera institucional UAGRM -->
        <div style="background-color: #1F4E79;">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shrink-0 shadow-md">
                    <span style="color: #1F4E79; font-size: 11px; font-weight: 800; line-height: 1.2; text-align: center;">FICCT</span>
                </div>
                <div class="min-w-0">
                    <p class="text-xs uppercase tracking-wider font-medium truncate" style="color: #bfdbfe;">
                        Universidad Autónoma Gabriel René Moreno
                    </p>
                    <h1 class="text-sm font-bold leading-tight text-white hidden sm:block">
                        Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones
                    </h1>
                    <h1 class="text-sm font-bold leading-tight text-white sm:hidden">
                        FICCT &ndash; UAGRM
                    </h1>
                </div>
            </div>
        </div>
        <!-- Barra de acento rojo oscuro -->
        <div style="background-color: #7F0000; height: 3px;"></div>

        <!-- Contenido principal -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-8 sm:pt-0 bg-gray-100">

            <div class="mb-6 text-center px-4">
                <h2 class="text-2xl font-bold" style="color: #1F4E79;">Sistema CUP FICCT</h2>
                <p class="text-gray-500 text-sm mt-1">Curso de Ubicación Profesional &bull; Gestión Académica</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-lg overflow-hidden sm:rounded-lg"
                 style="border-top: 4px solid #1F4E79;">
                {{ $slot }}
            </div>

            <p class="mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} FICCT &ndash; UAGRM. Sistema de Gestión CUP.</p>
        </div>
    </body>
</html>
