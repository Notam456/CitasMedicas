<?php

return [
    'crud_lectura' => [
        'attempts' => 120,
        'decay' => 1,
    ],
    'crud_escritura' => [
        'attempts' => 30,
        'decay' => 1,
    ],
    'citas_flujo' => [
        'attempts' => 60,
        'decay' => 1,
    ],
    'atender_citas' => [
        'attempts' => 120,
        'decay' => 1,
    ],
    'gestion_citas' => [
        'attempts' => 120,
        'decay' => 1,
    ],
    'detalle_cita' => [
        'attempts' => 60,
        'decay' => 1,
    ],
    'exportaciones' => [
        'attempts' => 10,
        'decay' => 1,
    ],
    'reportes_index' => [
        'attempts' => 60,
        'decay' => 1,
    ],
    'login' => [
        'attempts' => 10,
        'decay' => 1,
    ],
];
