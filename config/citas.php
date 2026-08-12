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
];
