<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First ensure the roles exist
        if (Role::where('name', 'administrador')->count() === 0) {
            Role::create(['name' => 'administrador']);
        }
        if (Role::where('name', 'usuario')->count() === 0) {
            Role::create(['name' => 'usuario']);
        }

        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            } else {
                $user->assignRole('usuario');
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('usuario');
        });

        $users = User::all();
        foreach ($users as $user) {
            $role = $user->getRoleNames()->first();
            if ($role) {
                $user->update(['role' => $role]);
            }
        }
    }
};
