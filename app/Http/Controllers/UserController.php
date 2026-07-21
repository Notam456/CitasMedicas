<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() && $request->has('draw')) {
            return $this->dataTableResponse($request);
        }

        $roles = Role::all();
        $permisos = Permission::all();

        $title = '¿Estas seguro de que deseas eliminar este usuario?';
        $text = 'Esta acción no se puede deshacer.';
        confirmDelete($title, $text);

        return view('user.listaUsuarios', compact('roles', 'permisos'));
    }

    private function dataTableResponse(Request $request)
    {
        $query = User::query();

        $totalRecords = $query->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $filteredRecords = $query->count();

        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'asc';
        $columns = ['name', 'email'];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('name', 'asc');
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $users = $query->skip($start)->take($length)->get();

        $dataFormatted = [];
        foreach ($users as $user) {
            $roleNames = $user->getRoleNames();
            $roleBadges = '';
            foreach ($roleNames as $role) {
                $badgeClass = ($role === 'administrador') ? 'bg-danger' : 'bg-secondary';
                $roleBadges .= '<span class="badge ' . $badgeClass . ' text-capitalize me-1">' . $role . '</span>';
            }

            $actionBtn = '<div class="hstack gap-2 justify-content-end">';
            $actionBtn .= '<button type="button" data-id="' . $user->id . '" class="btn-edit btn btn-xs btn-square btn-neutral"><i class="bi bi-pencil"></i></button>';
            $actionBtn .= '<a href="' . route('users.destroy', $user->id) . '" class="btn btn-xs btn-square btn-neutral text-danger-hover border-danger-hover" data-confirm-delete="true"><i class="bi bi-trash"></i></a>';
            $actionBtn .= '</div>';

            $dataFormatted[] = [
                $user->name,
                $user->email,
                $roleBadges,
                $actionBtn,
            ];
        }

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $dataFormatted,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'name' => ucfirst(mb_strtolower(trim($request->name), 'UTF-8')),
        ]);

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
    public function update(Request $request, int $id)
    {
        $request->merge([
            'name' => ucfirst(mb_strtolower(trim($request->name), 'UTF-8')),
        ]);

        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/u',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
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
        if (Auth::check() && Auth::id() == $user->id) {
            Alert::error('Error al eliminar', 'No se puede eliminar el usuario actual.');
            return redirect()->route('users.index');
        }


        $user->delete();

        Alert::success('Usuario eliminado exitosamente.');

        return redirect()->route('users.index');
    }
}
