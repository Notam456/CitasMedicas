<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('permissions')->where('name', 'Usuarios')->update(['name' => 'Usuario']);
        DB::table('permissions')->where('name', 'Médicos')->update(['name' => 'Medico']);
        DB::table('permissions')->where('name', 'Citas')->update(['name' => 'Cita']);

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        DB::table('permissions')->where('name', 'Usuario')->update(['name' => 'Usuarios']);
        DB::table('permissions')->where('name', 'Medico')->update(['name' => 'Médicos']);
        DB::table('permissions')->where('name', 'Cita')->update(['name' => 'Citas']);

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
