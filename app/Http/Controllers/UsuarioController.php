<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Models\TiposDocumento;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('admin.usuarios.index');
    }

    public function cargarUsuarios(Request $request)
    {
        $buscar = $request->input('buscar');
        $registrosPorPagina = $request->input('registros_por_pagina', 10);

        $usuarios = Usuario::query()
            ->with('roles') // Include roles relationship
            ->when($buscar, function ($query, $buscar) {
                return $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('numero_documento', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            })
            ->paginate($registrosPorPagina);

        // Add role to each user
        $usuarios->each(function ($usuario) {
            $usuario->rol = $usuario->roles->first()->name; // Assuming a user has one role
        });

        return response()->json($usuarios);
    }

    public function guardar(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'perfil' => 'required|exists:roles,id',
                'tipoIdentificacion' => 'required|exists:tipos_documentos,id',
                'numeroIdentificacion' => 'required|string|unique:usuarios,numero_documento',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fechaNacimiento' => 'required|date',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'required|email|max:255|unique:usuarios,email',
                'cargo' => 'nullable|string|max:255',
                'cliente_id' => 'nullable|exists:clientes,id', // Validar cliente_id
            ]);
            $password = Str::random(8); // Generar una contraseña temporal
            $usuario = Usuario::create([
                'tipo_documento_id' => $request->tipoIdentificacion,
                'numero_documento' => $request->numeroIdentificacion,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'fecha_nacimiento' => $request->fechaNacimiento,
                'telefono' => $request->telefono,
                'email' => $request->correo,
                'cargo' => $request->cargo,
                'password' => Hash::make($password)
            ]);

            $usuario->roles()->attach($request->perfil);

            // Relacionar usuario con cliente si se seleccionó un cliente
            if ($request->perfil == '3' && $request->cliente_id) {
                $usuario->clientes()->attach($request->cliente_id);
            }

            // Enviar correo con la contraseña temporal
           //  Mail::to($usuario->email)->send(new \App\Mail\UsuarioCreado($usuario, $password));

            return response()->json(['message' => 'Usuario creado con éxito']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerUsuario(Request $request)
    {
        $id = $request->input('id');
        $usuario = Usuario::with('tipoDocumento', 'roles', 'clientes')->findOrFail($id);
        $usuario->rol = $usuario->roles->first()->id; // Assuming a user has one role
        return response()->json($usuario);
    }

    public function actualizarUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        try {
            $validatedData = $request->validate([
                'perfil' => 'required|exists:roles,id',
                'tipoIdentificacion' => 'required|exists:tipos_documentos,id',
                'numeroIdentificacion' => 'required|string|unique:usuarios,numero_documento,' . $usuario->id,
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'fechaNacimiento' => 'required|date',
                'telefono' => 'nullable|string|max:20',
                'correo' => 'required|email|max:255|unique:usuarios,email,' . $usuario->id,
                'cargo' => 'nullable|string|max:255',
                'cliente_id' => 'nullable|exists:clientes,id', // Validar cliente_id
            ]);

            $usuario->update([
                'tipo_documento_id' => $request->tipoIdentificacion,
                'numero_documento' => $request->numeroIdentificacion,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'fecha_nacimiento' => $request->fechaNacimiento,
                'telefono' => $request->telefono,
                'email' => $request->correo,
                'cargo' => $request->cargo,
            ]);

            // Actualizar rol del usuario
            $usuario->roles()->sync([$request->perfil]);

            // Actualizar relación con cliente si se seleccionó un cliente
            if ($request->perfil == '3' && $request->cliente_id) {
                $usuario->clientes()->sync([$request->cliente_id]);
            } else {
                $usuario->clientes()->detach();
            }

            return response()->json(['message' => 'Usuario actualizado correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    public function obtenerRoles()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function obtenerTiposDocumentos()
    {
        $tiposDocumentos = TiposDocumento::all();
        return response()->json($tiposDocumentos);
    }
}
