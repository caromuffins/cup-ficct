<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CUP FICCT – UAGRM</title>
    @vite(['resources/css/app.css'])
    <style>
        body { margin: 0; font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif; background: #f0f4f8; }
        .cup-hero { background: linear-gradient(135deg, #1F4E79 0%, #163a5f 60%, #7F0000 100%); }
    </style>
</head>
<body>

{{-- Header --}}
<header style="background-color: #1F4E79;" class="shadow-md">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shrink-0 shadow">
                <span style="color:#1F4E79; font-size:8px; font-weight:900; text-align:center; line-height:1.1;">CUP<br>FICCT</span>
            </div>
            <div>
                <p class="text-white font-bold text-sm leading-tight">Sistema CUP FICCT &ndash; UAGRM</p>
                <p class="text-blue-200 text-xs leading-tight">Facultad de Ingeniería en Ciencias de la Computación</p>
            </div>
        </div>
        <a href="{{ route('login') }}"
           class="text-sm font-semibold text-white px-4 py-2 rounded-md transition"
           style="border: 1px solid rgba(255,255,255,0.4);"
           onmouseover="this.style.backgroundColor='rgba(255,255,255,0.15)'"
           onmouseout="this.style.backgroundColor='transparent'">
            Iniciar sesión
        </a>
    </div>
</header>

{{-- Hero --}}
<section class="cup-hero py-20 px-6">
    <div class="max-w-3xl mx-auto text-center">
        <p class="text-blue-200 text-sm font-semibold uppercase tracking-widest mb-3">Universidad Autónoma Gabriel René Moreno</p>
        <h1 class="text-white text-4xl sm:text-5xl font-extrabold leading-tight mb-4">
            Cursos Pre Universitarios
        </h1>
        <p class="text-blue-100 text-lg mb-8 leading-relaxed">
            Plataforma oficial de gestión del CUP de la FICCT.<br>
            Inscripciones, evaluaciones y resultados de admisión en un solo lugar.
        </p>
        <a href="{{ route('login') }}"
           class="inline-flex items-center gap-2 font-bold py-3 px-8 rounded-lg text-base transition"
           style="background-color: white; color: #1F4E79;"
           onmouseover="this.style.backgroundColor='#f0f4f8'"
           onmouseout="this.style.backgroundColor='white'">
            Ingresar al sistema
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>
</section>

{{-- Role cards --}}
<section class="max-w-5xl mx-auto px-6 py-16">
    <h2 class="text-center text-2xl font-bold text-gray-700 mb-10">¿Quién sos?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Administrador --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4" style="border-top-color: #1F4E79;">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #e8f0f7;">
                <svg class="w-6 h-6" style="color:#1F4E79;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Administrador</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Gestiona gestiones, postulantes, grupos, docentes, notas y resultados de admisión.
            </p>
        </div>

        {{-- Docente --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-green-600">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center mb-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Docente</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Visualiza tus grupos asignados, registra notas por materia y consulta tu horario de clases.
            </p>
        </div>

        {{-- Estudiante --}}
        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4" style="border-top-color: #7F0000;">
            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4" style="background-color: #fdf2f2;">
                <svg class="w-6 h-6" style="color:#7F0000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Estudiante</h3>
            <p class="text-gray-500 text-sm leading-relaxed">
                Realizá tu inscripción, seguí el estado de tus requisitos, consultá notas y resultado de admisión.
            </p>
        </div>

    </div>
</section>

{{-- Info strip --}}
<section class="py-10 px-6" style="background-color: #1F4E79;">
    <div class="max-w-4xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-8 text-center">
        <div>
            <p class="text-white text-3xl font-extrabold">4</p>
            <p class="text-blue-200 text-sm mt-1">Gestiones activas por año</p>
        </div>
        <div>
            <p class="text-white text-3xl font-extrabold">70</p>
            <p class="text-blue-200 text-sm mt-1">Estudiantes por grupo</p>
        </div>
        <div>
            <p class="text-white text-3xl font-extrabold">UAGRM</p>
            <p class="text-blue-200 text-sm mt-1">Facultad FICCT</p>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="py-6 px-6 text-center" style="background-color: #111827;">
    <p class="text-gray-400 text-sm">
        &copy; {{ date('Y') }} FICCT – Universidad Autónoma Gabriel René Moreno &nbsp;|&nbsp;
        Sistema CUP FICCT
    </p>
</footer>

@vite(['resources/js/app.js'])
</body>
</html>
