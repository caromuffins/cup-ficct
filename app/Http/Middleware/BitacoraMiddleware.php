<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Bitacora;
use Illuminate\Support\Facades\Route;

class BitacoraMiddleware
{
    /**
     * Maneja una petición entrante y registra la acción en la bitácora si es administrativa y de escritura.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // Ejecutar primero la petición para registrar solo acciones que se procesaron exitosamente
        $response = $next($request);

        // Solo registrar si el usuario está autenticado y la petición fue exitosa (2xx o 3xx)
        if (auth()->check() && $response->getStatusCode() >= 200 && $response->getStatusCode() < 400) {
            $method = $request->method();

            // Interceptar únicamente peticiones de modificación/escritura
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $routeName = Route::currentRouteName();

                // Evitar registrar la navegación del propio listado de bitácora o consultas de lectura
                if ($routeName === 'admin.bitacora.index') {
                    return $response;
                }

                $accion = $this->getAccion($method, $routeName);
                $modulo = $this->getModulo($routeName);
                $descripcion = $this->getDescripcion($request, $routeName, $accion, $modulo);

                $modeloTipo = $this->getModeloTipo($routeName);
                $modeloId = $this->getModeloId($request, $routeName);

                Bitacora::registrar($accion, $modulo, $descripcion, $modeloTipo, $modeloId);
            }
        }

        return $response;
    }

    /**
     * Determina la acción realizada a partir del método HTTP y nombre de la ruta.
     */
    private function getAccion(string $method, ?string $routeName): string
    {
        if ($routeName === 'admin.requisitos.validar') {
            return 'VALIDAR REQUISITO';
        }
        if ($routeName === 'admin.gestiones.activar') {
            return 'ACTIVAR GESTIÓN';
        }
        if ($routeName === 'admin.postulantes.importar.procesar') {
            return 'IMPORTAR POSTULANTES';
        }
        if ($routeName === 'admin.admision.calcular') {
            return 'CALCULAR ADMISIÓN';
        }
        if ($routeName === 'admin.admision.asignarCarreras') {
            return 'ASIGNAR CARRERAS';
        }
        if ($routeName === 'admin.admision.publicar') {
            return 'PUBLICAR RESULTADOS';
        }
        if ($routeName === 'admin.grupos.generar') {
            return 'GENERAR GRUPOS';
        }

        switch ($method) {
            case 'POST':
                return 'CREAR';
            case 'PUT':
            case 'PATCH':
                return 'MODIFICAR';
            case 'DELETE':
                return 'ELIMINAR';
            default:
                return 'ACCIÓN';
        }
    }

    /**
     * Determina el módulo afectado a partir del nombre de la ruta.
     */
    private function getModulo(?string $routeName): string
    {
        if (!$routeName) {
            return 'Sistema';
        }

        if (str_contains($routeName, 'postulantes') || str_contains($routeName, 'requisitos')) {
            return 'Postulantes';
        }
        if (str_contains($routeName, 'grupos')) {
            return 'Grupos';
        }
        if (str_contains($routeName, 'docentes')) {
            return 'Docentes';
        }
        if (str_contains($routeName, 'horarios')) {
            return 'Horarios';
        }
        if (str_contains($routeName, 'notas')) {
            return 'Notas';
        }
        if (str_contains($routeName, 'admision')) {
            return 'Admisión';
        }
        if (str_contains($routeName, 'gestiones')) {
            return 'Gestiones';
        }
        if (str_contains($routeName, 'reportes')) {
            return 'Reportes';
        }
        if (str_contains($routeName, 'consultas')) {
            return 'Consultas';
        }

        return 'Administración';
    }

    /**
     * Genera una descripción detallada en español para las acciones administrativas.
     */
    private function getDescripcion(Request $request, ?string $routeName, string $accion, string $modulo): string
    {
        $usuario = auth()->user()->name;

        // Descripciones específicas y bien formateadas
        if ($routeName === 'admin.postulantes.store') {
            return "El administrador {$usuario} registró al postulante con CI: '" . $request->input('ci') . "' y Nombre: '" . $request->input('name') . "'.";
        }
        if ($routeName === 'admin.postulantes.update') {
            $id = $request->route('postulante') ?? $request->route('id');
            return "El administrador {$usuario} modificó el postulante ID {$id} (CI: '" . $request->input('ci') . "', Nombre: '" . $request->input('name') . "', Estado: '" . $request->input('estado') . "').";
        }
        if ($routeName === 'admin.postulantes.destroy') {
            $id = $request->route('postulante') ?? $request->route('id');
            return "El administrador {$usuario} eliminó al postulante ID {$id} y su usuario asociado.";
        }
        if ($routeName === 'admin.requisitos.validar') {
            $id = $request->route('id');
            return "El administrador {$usuario} validó el requisito ID {$id} cambiando su estado a: '" . $request->input('estado') . "'.";
        }
        if ($routeName === 'admin.postulantes.importar.procesar') {
            return "El administrador {$usuario} realizó una importación masiva de postulantes desde archivo Excel/CSV.";
        }
        if ($routeName === 'admin.docentes.store') {
            return "El administrador {$usuario} registró al docente '" . $request->input('name') . "' (Materia ID: '" . $request->input('materia_id') . "').";
        }
        if ($routeName === 'admin.docentes.update') {
            $id = $request->route('docente') ?? $request->route('id');
            return "El administrador {$usuario} actualizó los datos del docente ID {$id} (Nombre: '" . $request->input('name') . "').";
        }
        if ($routeName === 'admin.docentes.destroy') {
            $id = $request->route('docente') ?? $request->route('id');
            return "El administrador {$usuario} eliminó al docente ID {$id}.";
        }
        if ($routeName === 'admin.horarios.store') {
            return "El administrador {$usuario} asignó el horario: Aula ID " . $request->input('aula_id') . ", Grupo ID " . $request->input('grupo_id') . ", Hora Inicio: " . $request->input('hora_inicio') . ".";
        }
        if ($routeName === 'admin.horarios.destroy') {
            $id = $request->route('id');
            return "El administrador {$usuario} eliminó la asignación de horario ID {$id}.";
        }
        if ($routeName === 'admin.notas.store') {
            return "El administrador {$usuario} registró/actualizó las notas para el grupo ID " . $request->input('grupo_id') . ".";
        }
        if ($routeName === 'admin.gestiones.store') {
            return "El administrador {$usuario} creó la gestión " . $request->input('anio') . " - Período " . $request->input('periodo') . ".";
        }
        if ($routeName === 'admin.gestiones.update') {
            $id = $request->route('gestion') ?? $request->route('id');
            return "El administrador {$usuario} actualizó la gestión ID {$id} a '" . $request->input('anio') . " - Período " . $request->input('periodo') . "'.";
        }
        if ($routeName === 'admin.gestiones.activar') {
            $id = $request->route('id');
            return "El administrador {$usuario} activó la gestión ID {$id} como la gestión activa del sistema.";
        }
        if ($routeName === 'admin.gestiones.destroy') {
            $id = $request->route('gestion') ?? $request->route('id');
            return "El administrador {$usuario} eliminó la gestión ID {$id}.";
        }
        if ($routeName === 'admin.grupos.generar') {
            return "El administrador {$usuario} ejecutó la generación automática de grupos de estudio para la gestión activa.";
        }
        if ($routeName === 'admin.admision.calcular') {
            return "El administrador {$usuario} calculó el proceso de admisión general.";
        }
        if ($routeName === 'admin.admision.asignarCarreras') {
            return "El administrador {$usuario} asignó carreras correspondientes según los cupos disponibles.";
        }
        if ($routeName === 'admin.admision.publicar') {
            return "El administrador {$usuario} publicó oficialmente los resultados del proceso de admisión.";
        }

        // Mensaje genérico descriptivo por defecto
        return "El administrador {$usuario} ejecutó la acción de tipo '{$accion}' en el módulo '{$modulo}'.";
    }

    /**
     * Retorna la clase del modelo correspondiente para registros polimórficos.
     */
    private function getModeloTipo(?string $routeName): ?string
    {
        if (!$routeName) return null;

        if (str_contains($routeName, 'postulantes')) {
            return \App\Models\Postulante::class;
        }
        if (str_contains($routeName, 'docentes')) {
            return \App\Models\Docente::class;
        }
        if (str_contains($routeName, 'gestiones')) {
            return \App\Models\Gestion::class;
        }
        if (str_contains($routeName, 'grupos')) {
            return \App\Models\Grupo::class;
        }
        if (str_contains($routeName, 'horarios')) {
            return \App\Models\Horario::class;
        }
        if (str_contains($routeName, 'notas')) {
            return \App\Models\Nota::class;
        }

        return null;
    }

    /**
     * Intenta extraer el ID numérico del registro de la ruta actual.
     */
    private function getModeloId(Request $request, ?string $routeName): ?int
    {
        if (!$routeName) return null;

        $route = $request->route();
        if ($route) {
            foreach (['postulante', 'docente', 'gestion', 'grupo', 'horario', 'nota', 'id'] as $param) {
                $value = $route->parameter($param);
                if ($value) {
                    if (is_numeric($value)) {
                        return (int) $value;
                    }
                    if (is_object($value) && isset($value->id)) {
                        return (int) $value->id;
                    }
                }
            }
        }

        return null;
    }
}
