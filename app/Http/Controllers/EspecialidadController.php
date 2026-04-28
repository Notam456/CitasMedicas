<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class EspecialidadController extends Controller
{
    // Función para capitalizar primera letra
    private function capitalizarPrimeraLetra($texto)
    {
        return ucfirst(strtolower($texto));
    }

    // Función para validar solo letras y espacios
    private function validarSoloLetras($texto)
    {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $texto);
    }

    public function index()
    {
        $especialidades = Especialidad::all();
        return view('especialidades.index', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:especialidad',
            'descripcion' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        // Validar que nombre solo tenga letras
        if (!$this->validarSoloLetras($request->nombre)) {
            Alert::error('Error', 'El nombre de la especialidad solo puede contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que descripcion solo tenga letras (si se ingresa)
        if ($request->descripcion && !$this->validarSoloLetras($request->descripcion)) {
            Alert::error('Error', 'La descripción solo puede contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Capitalizar primera letra del nombre y descripción
        $data = $request->all();
        $data['nombre'] = $this->capitalizarPrimeraLetra($data['nombre']);
        if (isset($data['descripcion'])) {
            $data['descripcion'] = $this->capitalizarPrimeraLetra($data['descripcion']);
        }

        Especialidad::create($data);
        Alert::success('Éxito', 'Especialidad creada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function edit($id_especialidad)
    {
        $especialidadToEdit = Especialidad::findOrFail($id_especialidad);
        $especialidades = Especialidad::all();
        return view('especialidades.index', compact('especialidades', 'especialidadToEdit'));
    }

    public function update(Request $request, $id_especialidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:especialidad,nombre,' . $id_especialidad . ',id_especialidad',
            'descripcion' => 'nullable|string',
            'estado' => 'boolean'
        ]);

        // Validar que nombre solo tenga letras
        if (!$this->validarSoloLetras($request->nombre)) {
            Alert::error('Error', 'El nombre de la especialidad solo puede contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que descripcion solo tenga letras (si se ingresa)
        if ($request->descripcion && !$this->validarSoloLetras($request->descripcion)) {
            Alert::error('Error', 'La descripción solo puede contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        $especialidad = Especialidad::findOrFail($id_especialidad);
        
        $data = $request->all();
        $data['nombre'] = $this->capitalizarPrimeraLetra($data['nombre']);
        if (isset($data['descripcion'])) {
            $data['descripcion'] = $this->capitalizarPrimeraLetra($data['descripcion']);
        }
        
        $especialidad->update($data);
        Alert::success('Éxito', 'Especialidad actualizada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function destroy($id_especialidad)
    {
        $especialidad = Especialidad::findOrFail($id_especialidad);
        $especialidad->delete();
        Alert::success('Éxito', 'Especialidad eliminada exitosamente.');
        return redirect()->route('especialidades.index');
    }

    public function show($id_especialidad)
    {
        return redirect()->route('especialidades.index');
    }
}