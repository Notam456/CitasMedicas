<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Especialidades exclusivas para pacientes de sexo femenino
    |--------------------------------------------------------------------------
    |
    | Un paciente de sexo masculino no puede agendar ni atender una cita
    | en ninguna de estas especialidades.
    |
    */
    'especialidades_solo_femenino' => [
        'Ginecologia Oncologica',
        'Menopausia',
        'Patologia Vulva/Vaginal',
        'Patologia Medica del Embarazo',
        'Aro (Embarazados)',
        'Pre Operatorio Emb',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reportes
    |--------------------------------------------------------------------------
    |
    | distrito_otros_estados: nombre del distrito que agrupa pacientes de
    | otros estados. Solo se usa para marcar distritos.es_agrupador durante
    | la migracion; el reporte ya no compara nombres.
    |
    | especialidad_aro: solo se usa como respaldo si la bandera
    | especialidades.es_aro no esta marcada.
    |
    */

    'distrito_otros_estados' => 'Otros Estados',

    'especialidad_aro' => 'Aro (Embarazados)',
];
