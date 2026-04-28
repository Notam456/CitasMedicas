<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class MedicoController extends Controller
{
    // Función para capitalizar primera letra de cada palabra
    private function capitalizarPrimeraLetra($texto)
    {
        return ucwords(strtolower($texto));
    }

    // Función para validar solo letras y espacios
    private function validarSoloLetras($texto)
    {
        return preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $texto);
    }

    // Función para validar teléfono con los prefijos permitidos
    private function validarTelefono($telefono)
    {
        $prefijos = ['0412', '0414', '0416', '0424', '0426', '0422'];
        $prefix = substr($telefono, 0, 4);
        return in_array($prefix, $prefijos) && is_numeric($telefono) && strlen($telefono) === 11;
    }

    public function index()
    {
        $medicos = Medico::with('especialidad')->get();
        $especialidades = Especialidad::all();
        return view('medicos.index', compact('medicos', 'especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medico',
            'telefono' => 'required|string|max:20',
            'id_especialidad' => 'required|exists:especialidad,id_especialidad',
            'estado' => 'boolean'
        ]);

        // Validar que nombres solo tenga letras
        if (!$this->validarSoloLetras($request->nombres)) {
            Alert::error('Error', 'Los nombres solo pueden contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que apellidos solo tenga letras
        if (!$this->validarSoloLetras($request->apellidos)) {
            Alert::error('Error', 'Los apellidos solo pueden contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que cédula solo tenga números
        if (!is_numeric($request->cedula)) {
            Alert::error('Error', 'La cédula solo debe contener números.');
            return redirect()->back()->withInput();
        }

        // Validar formato de teléfono
        if (!$this->validarTelefono($request->telefono)) {
            Alert::error('Error', 'El teléfono debe comenzar con 0412, 0414, 0416, 0424, 0426 o 0422 y tener 11 dígitos.');
            return redirect()->back()->withInput();
        }

        // Capitalizar primera letra de nombres y apellidos
        $data = $request->all();
        $data['nombres'] = $this->capitalizarPrimeraLetra($data['nombres']);
        $data['apellidos'] = $this->capitalizarPrimeraLetra($data['apellidos']);

        Medico::create($data);
        Alert::success('Éxito', 'Médico creado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function edit($id_medico)
    {
        $medicoToEdit = Medico::with('especialidad')->findOrFail($id_medico);
        $medicos = Medico::with('especialidad')->get();
        $especialidades = Especialidad::all();
        return view('medicos.index', compact('medicos', 'especialidades', 'medicoToEdit'));
    }

    public function update(Request $request, $id_medico)
    {
        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'cedula' => 'required|string|unique:medico,cedula,' . $id_medico . ',id_medico',
            'telefono' => 'required|string|max:20',
            'id_especialidad' => 'required|exists:especialidad,id_especialidad',
            'estado' => 'boolean'
        ]);

        // Validar que nombres solo tenga letras
        if (!$this->validarSoloLetras($request->nombres)) {
            Alert::error('Error', 'Los nombres solo pueden contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que apellidos solo tenga letras
        if (!$this->validarSoloLetras($request->apellidos)) {
            Alert::error('Error', 'Los apellidos solo pueden contener letras y espacios.');
            return redirect()->back()->withInput();
        }

        // Validar que cédula solo tenga números
        if (!is_numeric($request->cedula)) {
            Alert::error('Error', 'La cédula solo debe contener números.');
            return redirect()->back()->withInput();
        }

        // Validar formato de teléfono
        if (!$this->validarTelefono($request->telefono)) {
            Alert::error('Error', 'El teléfono debe comenzar con 0412, 0414, 0416, 0424, 0426 o 0422 y tener 11 dígitos.');
            return redirect()->back()->withInput();
        }

        $medico = Medico::findOrFail($id_medico);
        
        $data = $request->all();
        $data['nombres'] = $this->capitalizarPrimeraLetra($data['nombres']);
        $data['apellidos'] = $this->capitalizarPrimeraLetra($data['apellidos']);
        
        $medico->update($data);
        Alert::success('Éxito', 'Médico actualizado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function destroy($id_medico)
    {
        $medico = Medico::findOrFail($id_medico);
        $medico->delete();
        Alert::success('Éxito', 'Médico eliminado exitosamente.');
        return redirect()->route('medicos.index');
    }

    public function show($id_medico)
    {
        return redirect()->route('medicos.index');
    }
}