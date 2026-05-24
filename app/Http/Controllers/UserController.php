<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        $roles = Role::all();
        $permisos = Permission::all();

        $title = '¿Estas seguro de que deseas eliminar este usuario?';
        $texrt = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $texrt);
        
        return view( ('user.listaUsuarios'), compact('usuarios', 'roles', 'permisos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password'=> Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        Alert::success('Usuario creado exitosamente.');

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $userToEdit = User::findOrFail($id);
        $role = $userToEdit->getRoleNames()->first();
        
        return response()->json([
            'id' => $userToEdit->id,
            'name' => $userToEdit->name,
            'email' => $userToEdit->email,
            'role' => $role
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,int $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        $user->syncRoles([$request->role]);

        Alert::success('Usuario actualizado exitosamente.');

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        alert()->success('Usuario eliminado exitosamente.');

        return redirect()->route('users.index');
    }
}
