<!-- Botón Flotante para invocar el asistente de voz -->
<div class="fixed bottom-6 right-6 z-50">
    <button id="btn-voice-assistant" title="Asistente de Voz IA" class="relative flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-[#1F4E79] to-[#2E75B6] text-white shadow-lg shadow-blue-500/30 hover:scale-110 hover:shadow-blue-500/50 active:scale-95 transition-all duration-300 focus:outline-none group">
        <!-- Anillo pulsante de fondo -->
        <span class="absolute -inset-1 rounded-full bg-blue-400/20 animate-ping group-hover:bg-blue-400/40"></span>
        <!-- Icono de Micrófono -->
        <svg class="w-6 h-6 z-10 transition-transform group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
        </svg>
    </button>
</div>

<!-- Modal del Asistente de Voz (Glassmorphism) -->
<div id="modal-voice-assistant" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-md hidden transition-all duration-300 opacity-0">
    
    <!-- Contenedor del Modal -->
    <div id="card-voice-assistant" class="relative w-full max-w-md p-8 overflow-hidden bg-slate-900/90 border border-white/10 rounded-2xl shadow-2xl transition-all duration-300 scale-95 opacity-0">
        
        <!-- Efecto de luz de fondo cian -->
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-cyan-500/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>

        <!-- Botón de Cerrar -->
        <button id="btn-close-voice" class="absolute top-4 right-4 text-gray-400 hover:text-white transition-colors duration-200 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Cabecera -->
        <div class="text-center mb-6">
            <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-300">
                Asistente de Voz IA (Gemini)
            </h3>
            <p id="voice-status" class="text-xs text-gray-400 mt-1">
                Haz clic para empezar a grabar
            </p>
        </div>

        <!-- Visualizador de Ondas de Sonido (Animación CSS) -->
        <div id="voice-visualizer" class="flex items-center justify-center space-x-1.5 h-16 my-8">
            <div class="wave-bar bar-1"></div>
            <div class="wave-bar bar-2"></div>
            <div class="wave-bar bar-3"></div>
            <div class="wave-bar bar-4"></div>
            <div class="wave-bar bar-5"></div>
            <div class="wave-bar bar-6"></div>
            <div class="wave-bar bar-7"></div>
        </div>

        <!-- Transcripción / Respuesta de la IA -->
        <div class="bg-white/5 border border-white/5 rounded-xl p-4 min-h-[80px] flex items-center justify-center text-center mb-6">
            <p id="voice-transcript" class="text-sm text-gray-300 italic">
                "Ej: Mostrar reporte de notas del Grupo 1"
            </p>
        </div>

        <!-- Botones de Acción / Control -->
        <div class="flex justify-center space-x-4">
            <button id="btn-action-voice" class="px-6 py-2.5 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-700 hover:to-cyan-600 transition-all duration-300 shadow-md shadow-cyan-500/20 active:scale-95 focus:outline-none">
                Hablar
            </button>
            <button id="btn-cancel-voice" class="px-6 py-2.5 rounded-full text-sm font-semibold text-gray-400 bg-white/5 hover:bg-white/10 hover:text-white transition-all duration-300 focus:outline-none">
                Cancelar
            </button>
        </div>
        
    </div>
</div>

<style>
    /* Estilos personalizados para las ondas de voz */
    .wave-bar {
        width: 4px;
        height: 12px;
        background-color: #22d3ee; /* cyan-400 */
        border-radius: 9999px;
        transition: all 0.3s ease;
    }
    
    /* Animación de ondas activas */
    .voice-listening .wave-bar {
        animation: voice-wave-anim 1.2s ease-in-out infinite;
    }
    
    .voice-listening .bar-1 { animation-delay: 0.1s; background-color: #60a5fa; }
    .voice-listening .bar-2 { animation-delay: 0.3s; background-color: #3b82f6; }
    .voice-listening .bar-3 { animation-delay: 0.5s; background-color: #2563eb; }
    .voice-listening .bar-4 { animation-delay: 0.7s; background-color: #1d4ed8; }
    .voice-listening .bar-5 { animation-delay: 0.5s; background-color: #2563eb; }
    .voice-listening .bar-6 { animation-delay: 0.3s; background-color: #3b82f6; }
    .voice-listening .bar-7 { animation-delay: 0.1s; background-color: #60a5fa; }

    @keyframes voice-wave-anim {
        0%, 100% { height: 12px; transform: scaleY(1); }
        50% { height: 48px; transform: scaleY(1.2); }
    }

    /* Estado procesando (pulso suave conjunto) */
    .voice-processing .wave-bar {
        animation: voice-pulse-anim 1s ease-in-out infinite alternate;
        background-color: #a855f7; /* violet-500 */
    }
    .voice-processing .bar-1 { animation-delay: 0.0s; }
    .voice-processing .bar-2 { animation-delay: 0.1s; }
    .voice-processing .bar-3 { animation-delay: 0.2s; }
    .voice-processing .bar-4 { animation-delay: 0.3s; }
    .voice-processing .bar-5 { animation-delay: 0.2s; }
    .voice-processing .bar-6 { animation-delay: 0.1s; }
    .voice-processing .bar-7 { animation-delay: 0.0s; }

    @keyframes voice-pulse-anim {
        0% { height: 16px; opacity: 0.5; }
        100% { height: 28px; opacity: 1; }
    }

    /* Estado de éxito */
    .voice-success .wave-bar {
        background-color: #10b981; /* emerald-500 */
        height: 24px;
        transform: scale(1.1);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btnOpen = document.getElementById('btn-voice-assistant');
    const modal = document.getElementById('modal-voice-assistant');
    const card = document.getElementById('card-voice-assistant');
    const btnClose = document.getElementById('btn-close-voice');
    const btnCancel = document.getElementById('btn-cancel-voice');
    const btnAction = document.getElementById('btn-action-voice');
    
    const visualizer = document.getElementById('voice-visualizer');
    const statusText = document.getElementById('voice-status');
    const transcriptText = document.getElementById('voice-transcript');

    let mediaRecorder = null;
    let audioStream = null;
    let audioChunks = [];
    let isRecording = false;
    let autoStartTimeout = null;
    let recordMaxTimeout = null;

    // Abrir Modal
    function openModal() {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            card.classList.remove('opacity-0', 'scale-95');
        }, 50);
        setUIReady();
        
        // Autoiniciar la grabación tras abrir el modal
        autoStartTimeout = setTimeout(() => {
            startRecording();
        }, 500);
    }

    // Cerrar Modal
    function closeModal() {
        stopRecording();
        clearTimeout(autoStartTimeout);
        clearTimeout(recordMaxTimeout);
        
        card.classList.add('opacity-0', 'scale-95');
        modal.classList.add('opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Iniciar Grabación Local
    function startRecording() {
        if (isRecording) return;
        audioChunks = [];

        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                audioStream = stream;
                mediaRecorder = new MediaRecorder(stream);
                
                mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        audioChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    // Generar Blob con el audio crudo grabado en el navegador
                    const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    uploadAudio(audioBlob);
                };

                mediaRecorder.start();
                isRecording = true;
                setUIListening();

                // Límite de tiempo: detener automáticamente a los 6 segundos de silencio/habla
                recordMaxTimeout = setTimeout(() => {
                    if (isRecording) {
                        stopRecording();
                    }
                }, 6000);
            })
            .catch(err => {
                console.error('Microphone access error:', err);
                setUIError('Acceso al micrófono denegado. Por favor, aprueba los permisos en la barra de direcciones.');
            });
    }

    // Detener Grabación Local
    function stopRecording() {
        if (!isRecording) return;
        
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
        }
        if (audioStream) {
            audioStream.getTracks().forEach(track => track.stop());
        }
        
        clearTimeout(recordMaxTimeout);
        isRecording = false;
    }

    // Subir el audio grabado a Laravel mediante FormData
    function uploadAudio(blob) {
        setUIProcessing();

        const formData = new FormData();
        formData.append('audio', blob, 'recording.webm');

        fetch("{{ route('admin.reportes.voz') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: formData
        })
        .then(async response => {
            if (!response.ok) {
                const text = await response.text();
                let errMsg = "Ocurrió un error en el servidor.";
                try {
                    const errData = JSON.parse(text);
                    errMsg = errData.message || errMsg;
                } catch (e) {
                    // Si el error no es JSON (ej: pantalla de error de Laravel), mostrar parte del texto
                    errMsg = text.substring(0, 150) || errMsg;
                }
                throw new Error(errMsg);
            }
            return response.json();
        })
        .then(data => {
            if (data.route && data.redirect_url) {
                setUISuccess(data.message);
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1800);
            } else {
                setUIError(data.message || "No logré identificar el reporte. Intenta de nuevo.");
            }
        })
        .catch(error => {
            setUIError(error.message);
        });
    }

    // Cambios de Estado en la UI
    function setUIReady() {
        visualizer.className = 'flex items-center justify-center space-x-1.5 h-16 my-8';
        statusText.textContent = 'Conectando micrófono...';
        statusText.className = 'text-xs text-yellow-400 font-semibold mt-1 animate-pulse';
        transcriptText.textContent = 'Preparando micrófono, por favor espera...';
        btnAction.textContent = 'Grabar';
        btnAction.style.display = 'inline-block';
    }

    function setUIListening() {
        visualizer.className = 'flex items-center justify-center space-x-1.5 h-16 my-8 voice-listening';
        statusText.textContent = 'Grabando tu voz...';
        statusText.className = 'text-xs text-cyan-400 font-semibold mt-1 animate-pulse';
        transcriptText.innerHTML = '<span class="text-cyan-300 font-bold animate-pulse text-base block mb-2">🎙️ ¡HABLA AHORA!</span><span class="text-gray-400 text-xs">Di algo como "Notas del grupo 1" o "Docentes contratados".</span>';
        btnAction.textContent = 'Detener';
    }

    function setUIProcessing() {
        visualizer.className = 'flex items-center justify-center space-x-1.5 h-16 my-8 voice-processing';
        statusText.textContent = 'Enviando y analizando con Gemini...';
        statusText.className = 'text-xs text-purple-400 font-semibold mt-1 animate-pulse';
        transcriptText.textContent = 'Procesando tu voz directamente con IA...';
        btnAction.style.display = 'none';
    }

    function setUISuccess(message) {
        visualizer.className = 'flex items-center justify-center space-x-1.5 h-16 my-8 voice-success';
        statusText.textContent = '¡Reporte Mapeado!';
        statusText.className = 'text-xs text-emerald-400 font-bold mt-1';
        transcriptText.innerHTML = `<span class="text-emerald-400 font-semibold">${message}</span>`;
        btnAction.style.display = 'none';
    }

    // Visualizar Error
    function setUIError(message) {
        visualizer.className = 'flex items-center justify-center space-x-1.5 h-16 my-8';
        statusText.textContent = 'No se pudo procesar';
        statusText.className = 'text-xs text-red-400 font-semibold mt-1';
        transcriptText.textContent = message;
        btnAction.textContent = 'Reintentar';
        btnAction.style.display = 'inline-block';
    }

    // Event Listeners
    btnOpen.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);

    btnAction.addEventListener('click', () => {
        if (isRecording) {
            stopRecording();
        } else {
            startRecording();
        }
    });
});
</script>
