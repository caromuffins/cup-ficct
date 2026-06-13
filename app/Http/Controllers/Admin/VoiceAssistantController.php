<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class VoiceAssistantController extends Controller
{
    public function processVoice(Request $request)
    {
        $apiKey = config('services.gemini.key');

        // Obtener datos dinámicos para contextualizar a Gemini
        $gestion = DB::table('gestiones')->where('activa', true)->first();
        $gestionId = $gestion ? $gestion->id : null;

        $gruposQuery = DB::table('grupos');
        if ($gestionId) {
            $gruposQuery->where('gestion_id', $gestionId);
        }
        $grupos = $gruposQuery->select('id', 'nombre', 'turno')->get();
        $materias = DB::table('materias')->select('id', 'nombre')->get();

        // 1. ESCENARIO DE AUDIO SUBIDO (Brave / Safari / Local recording upload)
        if ($request->hasFile('audio')) {
            if (empty($apiKey)) {
                return response()->json([
                    'route' => null,
                    'message' => 'Para usar el asistente de voz en Brave se requiere configurar "GEMINI_API_KEY" en tu archivo .env. Esto permite a Gemini escuchar y transcribir el audio en el servidor.'
                ], 400);
            }

            $audioFile = $request->file('audio');
            $mimeType = $audioFile->getMimeType();
            
            // Mapear MIME types de video/contenedor de audio a sus contrapartes de audio puro para Gemini
            if (str_contains($mimeType, 'webm') || empty($mimeType) || $mimeType === 'application/octet-stream') {
                $mimeType = 'audio/webm';
            } elseif (str_contains($mimeType, 'mp4') || str_contains($mimeType, 'm4a') || str_contains($mimeType, 'quicktime')) {
                $mimeType = 'audio/mp4';
            } elseif (str_contains($mimeType, 'ogg')) {
                $mimeType = 'audio/ogg';
            }



            try {
                $audioBase64 = base64_encode(file_get_contents($audioFile->getPathname()));
                $prompt = $this->buildAudioPrompt($grupos, $materias);

                // Solicitud HTTP Multimodal a Gemini
                $client = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ]);
                if (config('app.env') === 'local') {
                    $client = $client->withoutVerifying();
                }

                $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'inlineData' => [
                                        'mimeType' => $mimeType,
                                        'data' => $audioBase64
                                    ]
                                ],
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $jsonResult = $response->json();
                    \Illuminate\Support\Facades\Log::info('Gemini raw response: ' . json_encode($jsonResult));

                    if (isset($jsonResult['candidates'][0]['content']['parts'][0]['text'])) {
                        $rawText = $jsonResult['candidates'][0]['content']['parts'][0]['text'];
                        
                        // Limpiar bloques markdown de código ```json ... ``` si existieran
                        $cleanText = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', trim($rawText));
                        $data = json_decode($cleanText, true);

                        if (json_last_error() === JSON_ERROR_NONE) {
                            if (isset($data['route']) && !empty($data['route'])) {
                                $filteredParams = array_filter($data['params'] ?? [], function($val) {
                                    return $val !== null;
                                });
                                $redirectUrl = route($data['route'], $filteredParams);

                                return response()->json([
                                    'route' => $data['route'],
                                    'params' => $data['params'],
                                    'message' => $data['message'] ?? 'Redirigiendo...',
                                    'redirect_url' => $redirectUrl,
                                    'mode' => 'gemini_multimodal',
                                    'transcript' => $data['transcript'] ?? null
                                ]);
                            } else {
                                return response()->json([
                                    'route' => null,
                                    'message' => $data['message'] ?? 'Gemini procesó el audio pero determinó que no corresponde a ningún reporte de la base de datos.',
                                    'transcript' => $data['transcript'] ?? null
                                ]);
                            }
                        } else {
                            \Illuminate\Support\Facades\Log::error('Gemini JSON decode error: ' . json_last_error_msg() . ' for text: ' . $rawText);
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::error('Gemini API Request failed with status ' . $response->status() . ': ' . $response->body());
                }

                return response()->json([
                    'route' => null,
                    'message' => 'Gemini no logró comprender el audio o no retornó un reporte válido. Intenta hablar más lento y claro.'
                ]);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Exception in VoiceAssistant: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
                return response()->json([
                    'route' => null,
                    'message' => 'Error al procesar el audio con Gemini API: ' . $e->getMessage()
                ], 500);
            }
        }

        // 2. ESCENARIO DE TEXTO DIRECTO (Compatibilidad / Fallback con WebSpeechAPI)
        $text = $request->input('text');
        if (empty($text)) {
            return response()->json([
                'route' => null,
                'message' => 'No se recibió ninguna entrada de voz o audio.'
            ], 400);
        }

        if ($apiKey) {
            try {
                $prompt = $this->buildTextPrompt($text, $grupos, $materias);

                $client = Http::withHeaders([
                    'Content-Type' => 'application/json'
                ]);
                if (config('app.env') === 'local') {
                    $client = $client->withoutVerifying();
                }

                $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $prompt
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json'
                    ]
                ]);

                if ($response->successful()) {
                    $jsonResult = $response->json();
                    if (isset($jsonResult['candidates'][0]['content']['parts'][0]['text'])) {
                        $rawText = $jsonResult['candidates'][0]['content']['parts'][0]['text'];
                        $data = json_decode(trim($rawText), true);

                        if (json_last_error() === JSON_ERROR_NONE && isset($data['route'])) {
                            $redirectUrl = null;
                            if (!empty($data['route'])) {
                                $filteredParams = array_filter($data['params'] ?? [], function($val) {
                                    return $val !== null;
                                });
                                $redirectUrl = route($data['route'], $filteredParams);
                            }

                            return response()->json([
                                'route' => $data['route'],
                                'params' => $data['params'],
                                'message' => $data['message'] ?? 'Redirigiendo...',
                                'redirect_url' => $redirectUrl,
                                'mode' => 'gemini_text'
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silently pass to local regex parser
            }
        }

        return $this->parseVoiceLocally($text, $grupos, $materias);
    }

    private function buildAudioPrompt($grupos, $materias)
    {
        $gruposJson = $grupos->toJson();
        $materiasJson = $materias->toJson();

        return "Escucha el audio adjunto en español. Interpreta el comando hablado por el usuario (un administrador) e identifica cuál de los 3 reportes disponibles desea consultar, junto con sus filtros.

Los reportes disponibles son:
1. Reporte de Aprobados y Grupos ('admin.reportes.aprobados'):
   - Filtros:
     * 'grupo_id': ID del grupo seleccionado.
     * 'admitido': '1' para admitidos, '0' para no admitidos.
2. Reporte de Docentes ('admin.reportes.docentes'):
   - Filtros:
     * 'estado_contratacion': 'contratado', 'pendiente', 'rechazado'.
3. Reporte de Notas por Grupo ('admin.reportes.notas'):
   - Filtros:
     * 'grupo_id': ID del grupo seleccionado.
     * 'materia_id': ID de la materia seleccionada.

Aquí tienes los datos actuales de la base de datos para mapear los nombres hablados a IDs reales:
Grupos: {$gruposJson}
Materias: {$materiasJson}

Debes responder ÚNICAMENTE con un objeto JSON válido que cumpla estrictamente con este esquema, sin bloques markdown de código adicionales ni texto introductorio:
{
  \"route\": \"nombre_de_la_ruta_o_null\",
  \"params\": {
    \"grupo_id\": int_o_null,
    \"admitido\": \"1\"|\"0\"|null,
    \"estado_contratacion\": \"contratado\"|\"pendiente\"|\"rechazado\"|null,
    \"materia_id\": int_o_null
  },
  \"message\": \"Mensaje en español amigable explicando la acción tomada, ej. \'Mostrando reporte de aprobados para el Grupo 1\'.\",
  \"transcript\": \"La transcripción exacta de lo que escuchaste en el audio.\"
}

Si el comando no se relaciona con ningún reporte o tiene ambigüedad, responde con \"route\": null y un mensaje explicativo en \"message\".";
    }

    private function buildTextPrompt($text, $grupos, $materias)
    {
        $gruposJson = $grupos->toJson();
        $materiasJson = $materias->toJson();

        return "Eres un asistente de inteligencia artificial para el sistema académico CUP-FICCT. Tu trabajo es interpretar un comando de voz transcrito a texto por el usuario (administrador) y mapearlo al reporte correcto con sus respectivos filtros.

Los reportes disponibles son:
1. Reporte de Aprobados y Grupos ('admin.reportes.aprobados'):
   - Filtros:
     * 'grupo_id': ID del grupo seleccionado.
     * 'admitido': '1' para admitidos, '0' para no admitidos.
2. Reporte de Docentes ('admin.reportes.docentes'):
   - Filtros:
     * 'estado_contratacion': 'contratado', 'pendiente', 'rechazado'.
3. Reporte de Notas por Grupo ('admin.reportes.notas'):
   - Filtros:
     * 'grupo_id': ID del grupo seleccionado.
     * 'materia_id': ID de la materia seleccionada.

Aquí tienes los datos actuales de la base de datos para mapear los nombres hablados a IDs reales:
Grupos: {$gruposJson}
Materias: {$materiasJson}

Comando de voz del usuario: \"{$text}\"

Debes responder ÚNICAMENTE con un objeto JSON válido que cumpla estrictamente con este esquema, sin bloques markdown de código adicionales ni texto introductorio:
{
  \"route\": \"nombre_de_la_ruta_o_null\",
  \"params\": {
    \"grupo_id\": int_o_null,
    \"admitido\": \"1\"|\"0\"|null,
    \"estado_contratacion\": \"contratado\"|\"pendiente\"|\"rechazado\"|null,
    \"materia_id\": int_o_null
  },
  \"message\": \"Mensaje en español amigable explicando la acción tomada, ej. 'Mostrando reporte de aprobados para el Grupo 1'.\"
}

Si el comando no se relaciona con ningún reporte o tiene ambigüedad, responde con \"route\": null y un mensaje explicativo en \"message\".";
    }

    private function parseVoiceLocally($text, $grupos, $materias)
    {
        $lower = mb_strtolower($text, 'UTF-8');
        
        $route = null;
        $params = [
            'grupo_id' => null,
            'admitido' => null,
            'estado_contratacion' => null,
            'materia_id' => null
        ];
        $message = '';

        // Buscar correspondencia de grupo
        $matchedGrupoId = null;
        $matchedGrupoNombre = '';
        foreach ($grupos as $g) {
            $nombreGrupoLower = mb_strtolower($g->nombre, 'UTF-8');
            $nombreGrupoClean = str_replace(' ', '', $nombreGrupoLower);
            $textClean = str_replace(' ', '', $lower);
            
            if (strpos($lower, $nombreGrupoLower) !== false || strpos($textClean, $nombreGrupoClean) !== false) {
                $matchedGrupoId = $g->id;
                $matchedGrupoNombre = $g->nombre;
                break;
            }
        }

        // Buscar correspondencia de materia
        $matchedMateriaId = null;
        $matchedMateriaNombre = '';
        foreach ($materias as $m) {
            $nombreMateriaLower = mb_strtolower($m->nombre, 'UTF-8');
            $nombreMateriaClean = $this->removeAccents($nombreMateriaLower);
            $textClean = $this->removeAccents($lower);

            if (strpos($textClean, $nombreMateriaClean) !== false) {
                $matchedMateriaId = $m->id;
                $matchedMateriaNombre = $m->nombre;
                break;
            }
        }

        // 1. Reporte de Notas
        if (strpos($lower, 'nota') !== false || strpos($lower, 'calificacion') !== false || strpos($lower, 'rendimiento') !== false) {
            $route = 'admin.reportes.notas';
            $params['grupo_id'] = $matchedGrupoId;
            $params['materia_id'] = $matchedMateriaId;

            $msgParts = [];
            if ($matchedGrupoNombre) $msgParts[] = "del {$matchedGrupoNombre}";
            if ($matchedMateriaNombre) $msgParts[] = "para la materia {$matchedMateriaNombre}";

            $message = 'Mostrando reporte de notas ' . implode(' ', $msgParts) . '.';
        } 
        // 2. Reporte de Docentes
        elseif (strpos($lower, 'docente') !== false || strpos($lower, 'profesor') !== false) {
            $route = 'admin.reportes.docentes';
            
            if (strpos($lower, 'contratado') !== false) {
                $params['estado_contratacion'] = 'contratado';
                $message = 'Mostrando reporte de docentes contratados.';
            } elseif (strpos($lower, 'pendiente') !== false) {
                $params['estado_contratacion'] = 'pendiente';
                $message = 'Mostrando reporte de docentes pendientes.';
            } elseif (strpos($lower, 'rechazado') !== false) {
                $params['estado_contratacion'] = 'rechazado';
                $message = 'Mostrando reporte de docentes rechazados.';
            } else {
                $message = 'Mostrando reporte general de docentes.';
            }
        }
        // 3. Reporte de Aprobados / Admitidos / General de Grupos
        elseif (strpos($lower, 'aprobado') !== false || strpos($lower, 'admitido') !== false || strpos($lower, 'ingresante') !== false || strpos($lower, 'grupo') !== false) {
            $route = 'admin.reportes.aprobados';
            $params['grupo_id'] = $matchedGrupoId;

            if (strpos($lower, 'no admitido') !== false || strpos($lower, 'reprobado') !== false || strpos($lower, 'rechazado') !== false) {
                $params['admitido'] = '0';
                $message = 'Mostrando reporte de postulantes no admitidos ' . ($matchedGrupoNombre ? "para el {$matchedGrupoNombre}" : "") . '.';
            } elseif (strpos($lower, 'admitido') !== false || strpos($lower, 'aprobado') !== false) {
                $params['admitido'] = '1';
                $message = 'Mostrando reporte de postulantes admitidos ' . ($matchedGrupoNombre ? "para el {$matchedGrupoNombre}" : "") . '.';
            } else {
                $message = 'Mostrando reporte de aprobados y grupos ' . ($matchedGrupoNombre ? "para el {$matchedGrupoNombre}" : "") . '.';
            }
        }

        if ($route) {
            $filteredParams = array_filter($params, function($val) {
                return $val !== null;
            });
            
            return response()->json([
                'route' => $route,
                'params' => $params,
                'message' => $message,
                'redirect_url' => route($route, $filteredParams),
                'mode' => 'local_fallback'
            ]);
        }

        return response()->json([
            'route' => null,
            'message' => 'No logré identificar el reporte solicitado para el comando: "' . $text . '". Intenta diciendo algo como: "Reporte de aprobados del Grupo 1" o "Notas del Grupo 1 para Computación".',
            'mode' => 'local_fallback'
        ]);
    }

    private function removeAccents($string)
    {
        $replacements = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u',
            'ü' => 'u', 'Ü' => 'u'
        ];
        return strtr($string, $replacements);
    }
}
