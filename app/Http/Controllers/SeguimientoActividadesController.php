<?php

namespace App\Http\Controllers;

use App\Models\SupervisorTurno;
use App\Models\Turno;
use App\Models\Actividades;
use App\Models\ImagenesActividades;
use App\Models\SedesActivos;
use App\Models\SedesInsumos;
use App\Models\Estados; // Importar el modelo Estados
use App\Models\Mantenimiento; // Importar el modelo Mantenimientos
use App\Models\Activos;// Importar el modelo Activo
use App\Models\Insumos;// Importar el modelo Insumo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Usuario; // Importar el modelo Usuario
use App\Models\Sede; // Importar el modelo Sede
use App\Models\Cliente; // Importar el modelo Cliente

class SeguimientoActividadesController extends Controller
{
    // Muestra la vista de inventario con los insumos y activos de las sedes
    public function inventario()
    {
        // Obtiene todos los insumos de las sedes
        $sedesInsumos = SedesInsumos::with('insumo.estados')->get();

        // Obtiene todos los activos de las sedes
        $sedesActivos = SedesActivos::with('activo.estados')->get();

        // Obtiene todos los estados
        $estados = Estados::all();

        // Retorna la vista con los insumos, activos, estados y mantenimientos de las sedes
        return view('seguimiento-actividades.inventario', compact('sedesInsumos', 'sedesActivos', 'estados'));
    }

    // Obtener todos los estados
    public function obtenerEstados()
    {
        $estados = Estados::all();
        return response()->json($estados);
    }

    // Guarda las observaciones y novedades de un insumo
    public function guardarObservaciones(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'novedades' => 'required|integer',
            'observaciones' => 'nullable|string',
        ]);

        // Encuentra el insumo de la sede por su ID
        $sedesInsumo = SedesInsumos::findOrFail($id);

        // Actualiza las observaciones y novedades del insumo
        $sedesInsumo->update([
            'novedades' => $request->novedades,
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Observaciones guardadas exitosamente']);
    }

    // Reporta un fallo en un activo
    public function reportarFallo(Request $request, $id)
    {
        // Valida los datos recibidos
        $request->validate([
            'observaciones' => 'required|string',
        ]);

        // Encuentra el activo de la sede por su ID
        $sedeActivo = SedesActivos::findOrFail($id);

        // Actualiza las observaciones del activo y marca el fallo
        $sedeActivo->update([
            'novedades' => 2, // Código para fallo
            'observaciones' => $request->observaciones,
        ]);

        // Retorna una respuesta exitosa
        return response()->json(['message' => 'Fallo reportado exitosamente']);
    }

    public function index()
    {
        $userId = Auth::id();
        $turnos = SupervisorTurno::with(['supervisor', 'sede', 'turno'])
            ->where('supervisor_id', $userId)
            ->get();
        return view('seguimiento-actividades.index', compact('turnos'));
    }

    public function obtenerActividades(Request $request)
    {
        $userId = Auth::id();
        $turnoId = $request->input('id') ?? "<script>document.write(localStorage.getItem('turno_id'))</script>";
        $sedeId = $request->input('sede_id') ?? "<script>document.write(localStorage.getItem('sede_id'))</script>";

        if (!$turnoId || !$sedeId) {
            return redirect()->route('seguimiento.actividades.index');
        }

        // Validate and store in localStorage if not exist
        echo "<script>
            if (!localStorage.getItem('turno_id') || !localStorage.getItem('sede_id')) {
                localStorage.setItem('turno_id', '$turnoId');
                localStorage.setItem('sede_id', '$sedeId');
            }
        </script>";

        $supervisorTurno = SupervisorTurno::with(['supervisor', 'sede', 'turno.actividades'])
            ->where('supervisor_id', $userId)
            ->whereHas('turno', function($query) use ($turnoId) {
                $query->where('id', $turnoId);
            })
            ->first();

        $actividadesTrue = $supervisorTurno->turno->actividades->where('estado', true)->values();
        $actividadesFalse = $supervisorTurno->turno->actividades->where('estado', false)->values();

        return view('seguimiento-actividades.actividades', compact('supervisorTurno', 'actividadesTrue', 'actividadesFalse', 'sedeId', 'turnoId'));
    }

    public function guardarCalificacion(Request $request, $id)
    {
        $actividad = Actividades::findOrFail($id);
        $actividad->calificacion = $request->input('calificacion');
        $actividad->estado = false;
        $actividad->save();

        if ($request->hasFile('evidencias')) {
            foreach ($request->file('evidencias') as $file) {
                $path = $file->store('evidencias', 'public');
                ImagenesActividades::create([
                    'actividad_id' => $actividad->id,
                    'imagen' => $path
                ]);
            }
        }

        return redirect()->back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function obtenerInventarios(Request $request)
    {
        $sedeId = $request->input('sede_id') ?? "<script>document.write(localStorage.getItem('sede_id'))</script>";

        if (!$sedeId) {
            return redirect()->route('seguimiento.actividades.index');
        }

        $sedesInsumos = SedesInsumos::with(['insumo.estados', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();
        /* dd($sedesInsumos); */
        $sedesActivos = SedesActivos::with(['activo.estados', 'sede'])
            ->where('sede_id', $sedeId)
            ->get();
        /* dd($sedesActivos); */
        // Obtiene los mantenimientos con estado_id = 3
        $mantenimientos = Mantenimiento::with('sedeActivo.activo')
            ->where('estado_id', 3)
            ->get();
        /* dd($mantenimientos); */
        // Obtiene todos los insumos
        $insumos = Insumos::all();

        // Obtiene todos los activos
        $activos = Activos::all();
        // dd($activos);
        return view('seguimiento-actividades.inventario',
        compact('sedesActivos', 'sedesInsumos', 'sedeId', 'mantenimientos', 'insumos', 'activos'));
    }

    public function finalizarTurno(Request $request)
    {
        $userId = Auth::id();
        $turno = SupervisorTurno::where('supervisor_id', $userId)->latest()->first();

        if ($turno) {
            $turno->turno->observacion = $request->input('observaciones');
            $turno->turno->estado = false;
            $turno->turno->save();
        }

        return redirect()->route('inventarios.turno')->with('success', 'Turno finalizado correctamente.');
    }

    public function actualizarInsumo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_insumos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'nullable|string',
        ]);

        $sedesInsumo = SedesInsumos::find($request->id);
        $sedesInsumo->observacion = $request->observacion;
        $sedesInsumo->save();

        $insumo = $sedesInsumo->insumo;
        $insumo->estado_id = $request->estado_id;
        $insumo->save();

        return response()->json(['message' => 'Insumo actualizado correctamente']);
    }

    // Obtener detalles de un insumo
    public function obtenerInsumo($id)
    {
        $sedesInsumo = SedesInsumos::with('insumo.estados')->findOrFail($id);
        return response()->json([
            'insumo' => [
                'estado_id' => $sedesInsumo->insumo->estado_id,
                'observacion' => $sedesInsumo->observacion
            ]
        ]);
    }

    public function obtenerObservaciones($id)
    {
        $sedesInsumo = SedesInsumos::find($id);
        if ($sedesInsumo) {
            return response()->json(['observacion' => $sedesInsumo->observacion]);
        } else {
            return response()->json(['error' => 'Insumo no encontrado'], 404);
        }
    }

    public function actualizarActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_activos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'nullable|string',
        ]);

        $activo = SedesActivos::find($request->id);
        $activo->observacion = $request->observacion;
        $activo->save();

        $activoModel = $activo->activo;
        $activoModel->estado_id = $request->estado_id;
        $activoModel->save();

        return response()->json(['message' => 'Activo actualizado correctamente']);
    }

    public function obtenerActivo($id)
    {
        $activo = SedesActivos::with('activo')->find($id);
        if ($activo) {
            return response()->json(['activo' => $activo]);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    public function obtenerObservacionesActivo($id)
    {
        $activo = SedesActivos::find($id);
        if ($activo) {
            return response()->json(['observacion' => $activo->observacion]);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    // Función para reportar un activo
    public function reportarActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sedes_activos,id',
            'estado_id' => 'required|exists:estados,id',
            'observacion' => 'required|string',
        ]);

        $activo = SedesActivos::find($request->id);
        if ($activo) {
            // Actualizar el estado y la observación del activo
            $activo->estado_id = $request->estado_id;
            $activo->observacion = $request->observacion;
            $activo->save();

            // Crear un nuevo registro en la tabla de mantenimientos
            Mantenimiento::create([
                'estado_id' => $request->estado_id,
                'sede_activo_id' => $activo->id,
                'observaciones_reportadas' => $request->observacion,
                'estado' => 1,
                'creador_id' => Auth::id(),
            ]);

            return response()->json(['message' => 'Activo reportado y registrado en mantenimientos correctamente']);
        }

        return response()->json(['message' => 'Activo no encontrado'], 404);
    }

    // Función para manejar la solicitud de insumo/activo
    public function solicitarInsumoActivo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'cantidad' => 'required|integer',
            'observaciones' => 'nullable|string',
            'tipo' => 'required|string|in:insumo,activo', // Validar el tipo de solicitud
            'sedeId' => 'required|exists:sedes,id',
        ]);
        // dd($request->all());


        $cantidad = $request->input('cantidad');
        $observaciones = $request->input('observaciones');
        $tipo = $request->input('tipo');
        $usuario = Auth::user()->nombres;
        $sedes = Sede::with('cliente')->find($request->input('sedeId'));
        $sede = $sedes->nombre;
        $cliente = $sedes->cliente->nombre;
        // Llamar a la función para enviar el correo
        if ($tipo === 'insumo') {
            $nombre = Insumos::find($request->input('id'))->nombre_elemento;
            //dd($cliente, $sede, $usuario, $observaciones, $nombre, $cantidad, $tipo);
            $this->enviarSolicitudCorreoInsumo($nombre, $cantidad, $observaciones, $usuario, $sede, $cliente);
        } else {
            $nombre = Activos::find($request->input('id'))->nombre_elemento;
            //dd($cliente, $sede, $usuario, $observaciones, $nombre, $cantidad, $tipo);
            $this->enviarSolicitudCorreoActivo($nombre, $cantidad, $observaciones, $usuario, $sede, $cliente);
        }

        return response()->json(['message' => 'Solicitud enviada correctamente']);
    }

    // Función para enviar correo de solicitud de insumo a los administradores
    public function enviarSolicitudCorreoInsumo($nombre, $cantidad, $observaciones, $usuario, $sede, $cliente)
    {
        // Obtener los correos de los usuarios con rol de Administrador
        $administradores = Usuario::whereHas('roles', function($query) {
            $query->where('name', 'Administrador');
        })->pluck('email');

        // Datos del correo
        $data = [
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'observaciones' => $observaciones,
            'usuario' => $usuario,
            'sede' => $sede,
            'cliente' => $cliente,
        ];

        // Enviar el correo a cada administrador
        foreach ($administradores as $email) {
            Mail::send('emails.solicitud_insumo', $data, function($message) use ($email) {
                $message->to($email)
                        ->subject('Solicitud de Insumo');
            });
        }

        return response()->json(['message' => 'Correo de solicitud de insumo enviado correctamente']);
    }

    // Función para enviar correo de solicitud de activo a los administradores
    public function enviarSolicitudCorreoActivo($nombre, $cantidad, $observaciones, $usuario, $sede, $cliente)
    {
        // Obtener los correos de los usuarios con rol de Administrador
        $administradores = Usuario::whereHas('roles', function($query) {
            $query->where('name', 'Administrador');
        })->pluck('email');

        // Datos del correo
        $data = [
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'observaciones' => $observaciones,
            'usuario' => $usuario,
            'sede' => $sede,
            'cliente' => $cliente,
        ];

        // Enviar el correo a cada administrador
        foreach ($administradores as $email) {
            Mail::send('emails.solicitud_activo', $data, function($message) use ($email) {
                $message->to($email)
                        ->subject('Solicitud de Activo');
            });
        }

        return response()->json(['message' => 'Correo de solicitud de activo enviado correctamente']);
    }
}
