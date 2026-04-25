<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Muestra la lista de pacientes con su ubicación completa
     */
    public function index()
    {
        // El cambio importante es añadir with(['estado', 'municipio', 'parroquia'])
        // Esto carga los nombres de las tablas relacionadas automáticamente
        $pacientes = Paciente::with(['estado', 'municipio', 'parroquia'])->get();
        
        return view('pacientes.index', compact('pacientes'));
    }

    /**
     * Muestra el formulario de registro
     */
    public function create()
    {
        // Mantenemos la carga de estados para el formulario de registro
        $estados = Estado::all();
        return view('pacientes.create', compact('estados'));
    }

    /**
     * Guarda el paciente en la base de datos
     */
    public function store(Request $request)
    {
        // 1. Validamos que los campos no lleguen vacíos y la cédula sea única
        $request->validate([
            'nombre'       => 'required|string|max:255',
            'cedula'       => 'required|string|unique:pacientes,cedula',
            'estado_id'    => 'required',
            'municipio_id' => 'required',
            'parroquia_id' => 'required',
        ]);

        // 2. Creamos el registro en la base de datos
        Paciente::create([
            'nombre'       => $request->nombre,
            'cedula'       => $request->cedula,
            'estado_id'    => $request->estado_id,
            'municipio_id' => $request->municipio_id,
            'parroquia_id' => $request->parroquia_id,
        ]);

        // 3. Redireccionamos al index con mensaje de éxito
        return redirect()->route('pacientes.index')->with('success', 'Paciente guardado con éxito');
    }
}

