<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RestructuracionDiagnosticosYTratamientos extends Migration
{
    public function up()
    {
        // 1. Agregar columnas a citas
        Schema::table('citas', function (Blueprint $table) {
            $table->text('diagnostico_libre')->nullable()->after('observacion');
            $table->unsignedBigInteger('atendido_por')->nullable()->after('user_id');
            $table->foreign('atendido_por')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Crear tabla medicamentos
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // 3. Crear tabla cita_patologias (antes diagnosticos)
        Schema::create('cita_patologias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('patologia_id');
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('patologia_id')->references('id')->on('patologias')->onDelete('cascade');
            $table->unique(['cita_id', 'patologia_id']);
        });

        // 4. Crear tabla cita_referencias
        Schema::create('cita_referencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('especialidad_id');
            $table->text('observaciones')->nullable();
            $table->date('fecha_referencia')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('especialidad_id')->references('id')->on('especialidades')->onDelete('cascade');
        });

        // 5. Crear tabla cita_tratamiento (cita - medicamentos)
        Schema::create('cita_tratamiento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->string('dosis')->nullable();
            $table->string('duracion')->nullable();
            $table->text('indicaciones')->nullable();
            $table->timestamps();

            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('medicamento_id')->references('id')->on('medicamentos')->onDelete('cascade');
        });

        // 6. Migrar datos existentes desde diagnosticos
        // Primero, mover diagnostico_libre y asistio a citas
        $diagnosticos = DB::table('diagnosticos')->get();
        foreach ($diagnosticos as $diag) {
            // Actualizar cita con diagnostico_libre y asistio (asistio ya existía, pero lo mantenemos)
            DB::table('citas')
                ->where('id', $diag->cita_id)
                ->update([
                    'diagnostico_libre' => $diag->diagnostico_libre,
                    'atendido_por' => $diag->user_id,
                ]);

            // Si tiene patologia_id, insertar en cita_patologias
            if ($diag->patologia_id) {
                DB::table('cita_patologias')->insert([
                    'cita_id' => $diag->cita_id,
                    'patologia_id' => $diag->patologia_id,
                    'created_at' => $diag->created_at,
                    'updated_at' => $diag->updated_at,
                ]);
            }
        }

        // 7. Eliminar tabla diagnosticos
        Schema::dropIfExists('diagnosticos');
    }

    public function down()
    {
        // Revertir cambios: volver a crear diagnosticos, eliminar nuevas tablas, quitar columnas de citas
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cita_id');
            $table->unsignedBigInteger('patologia_id')->nullable();
            $table->text('diagnostico_libre')->nullable();
            $table->boolean('asistio')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('cita_id')->references('id')->on('citas')->onDelete('cascade');
            $table->foreign('patologia_id')->references('id')->on('patologias');
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Reinsertar datos desde cita_patologias y citas
        $citasConDiagnostico = DB::table('citas')->whereNotNull('diagnostico_libre')->orWhereHas('citaPatologias')->get();
        foreach ($citasConDiagnostico as $cita) {
            $patologias = DB::table('cita_patologias')->where('cita_id', $cita->id)->get();
            foreach ($patologias as $pat) {
                DB::table('diagnosticos')->insert([
                    'cita_id' => $cita->id,
                    'patologia_id' => $pat->patologia_id,
                    'diagnostico_libre' => $cita->diagnostico_libre,
                    'asistio' => true,
                    'user_id' => $cita->atendido_por ?? $cita->user_id,
                    'created_at' => $pat->created_at,
                    'updated_at' => $pat->updated_at,
                ]);
            }
            if ($patologias->isEmpty() && $cita->diagnostico_libre) {
                DB::table('diagnosticos')->insert([
                    'cita_id' => $cita->id,
                    'patologia_id' => null,
                    'diagnostico_libre' => $cita->diagnostico_libre,
                    'asistio' => true,
                    'user_id' => $cita->atendido_por ?? $cita->user_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::dropIfExists('cita_tratamiento');
        Schema::dropIfExists('cita_referencias');
        Schema::dropIfExists('cita_patologias');
        Schema::dropIfExists('medicamentos');

        Schema::table('citas', function (Blueprint $table) {
            $table->dropForeign(['atendido_por']);
            $table->dropColumn(['diagnostico_libre', 'atendido_por']);
        });
    }
}
