<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Patologia;
use App\Models\Cita;
use App\Models\User;
use Faker\Factory as Faker;

class PatologiaDiagnosticoSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('es_ES');
        $userAdmin = User::first();

        $especialidades = Especialidad::all();
        $patologiasPorEspecialidad = [];

        // Crear 40 patologías divididas entre las especialidades
        $nombresPatologias = [
            'Cirugia General' => ['Apendicitis aguda', 'Colelitiasis', 'Hernia inguinal', 'Peritonitis'],
            'Cirugia Plastica' => ['Cicatriz queloide', 'Contractura capsular', 'Asimetría mamaria', 'Necrosis grasa'],
            'Pre Operatorio' => ['Riesgo quirúrgico alto', 'Desnutrición preoperatoria', 'Anemia preoperatoria', 'Alteración electrolítica'],
            'Cirugia Mano' => ['Síndrome del túnel carpiano', 'Dupuytren', 'Tendinitis de Quervain', 'Ganglión'],
            'Cirugia de Torax' => ['Neumotórax espontáneo', 'Derrame pleural', 'Empiema', 'Tumor mediastínico'],
            'Cirugia Oncologica' => ['Tumor colorrectal', 'Sarcoma de partes blandas', 'Cáncer gástrico', 'Neoplasia hepática'],
            'NeuroCirugia' => ['Hematoma subdural', 'Hernia discal lumbar', 'Tumor cerebral', 'Hidrocefalia'],
            'Otorrino' => ['Rinitis alérgica', 'Sinusitis crónica', 'Amigdalitis', 'Hipoacusia neurosensorial'],
            'Oftalmologia' => ['Cataratas', 'Glaucoma', 'Conjuntivitis', 'Degeneración macular'],
            'Traumatologia' => ['Fractura de cadera', 'Esguince de tobillo', 'Artrosis de rodilla', 'Luxación de hombro'],
            'Urología' => ['Hiperplasia prostática', 'Infección urinaria', 'Litiasis renal', 'Cistitis'],
            'Cardiologia' => ['Hipertensión arterial', 'Insuficiencia cardíaca', 'Arritmia', 'Cardiopatía isquémica'],
            'Dermatologia' => ['Acné vulgar', 'Dermatitis atópica', 'Psoriasis', 'Melanoma'],
            'Diabetes' => ['Diabetes tipo 2', 'Diabetes tipo 1', 'Pie diabético', 'Retinopatía diabética'],
            'Audioprotesis' => ['Hipoacusia conductiva', 'Presbiacusia', 'Acúfenos', 'Hipoacusia mixta'],
            'Gastroenterologia' => ['Enfermedad por reflujo', 'Gastritis crónica', 'Síndrome de intestino irritable', 'Colitis ulcerosa'],
            'Ginecologia Oncologica' => ['Cáncer de cuello uterino', 'Cáncer de ovario', 'Cáncer de endometrio', 'Tumor trofoblástico'],
            'Fonoaudiologia' => ['Disfonía funcional', 'Afasia', 'Disfagia orofaríngea', 'Tartamudez'],
            'Medicina Interna' => ['Neumonía adquirida', 'Insuficiencia renal crónica', 'Sepsis', 'Descompensación metabólica'],
            'Medicina Fisica y Rehabilitación' => ['Lumbalgia crónica', 'Cervicalgia', 'Paraplejía', 'Amputación traumática'],
            'Menopausia' => ['Síndrome climatérico', 'Osteoporosis', 'Atrofia vaginal', 'Trastorno del sueño menopáusico'],
            'Nefrologia Adulto' => ['Enfermedad renal crónica', 'Glomerulonefritis', 'Nefropatía diabética', 'Hipertensión renovascular'],
            'Hepatobiliar' => ['Hígado graso', 'Cirrosis hepática', 'Hepatitis crónica', 'Colangitis'],
            'NeuroPediatria' => ['Parálisis cerebral', 'Retraso psicomotor', 'Epilepsia infantil', 'Autismo'],
            'Neumonologia Adulto' => ['Asma bronquial', 'EPOC', 'Fibrosis pulmonar', 'Neumonía intersticial'],
            'Oncologia' => ['Cáncer de mama', 'Cáncer de pulmón', 'Linfoma', 'Leucemia'],
            'Psiquiatria' => ['Trastorno depresivo mayor', 'Trastorno de ansiedad', 'Esquizofrenia', 'Trastorno bipolar'],
            'Patologia Medica del Embarazo' => ['Preeclampsia', 'Diabetes gestacional', 'Amenaza de parto pretérmino', 'Placenta previa'],
            'Infectologia' => ['VIH', 'Tuberculosis', 'Infección por VIH avanzada', 'Micosis sistémica'],
            'Aro (Embarazados)' => ['Embarazo de alto riesgo', 'Embarazo adolescente', 'Embarazo múltiple', 'Isoinmunización Rh'],
            'Pre Anestesia' => ['Valoración preanestésica', 'Vía aérea difícil', 'Alergia anestésica', 'Riesgo anestésico ASA'],
            'Patologia Vulva/Vaginal' => ['Vaginosis bacteriana', 'Candidiasis vulvovaginal', 'Liquen escleroso', 'Vulvodinia'],
            'Planificación Familiar' => ['Consejería anticonceptiva', 'DIU inserción', 'Implante subdérmico', 'Anticoncepción hormonal'],
            'Psicopedagogia' => ['Dificultad de aprendizaje', 'TDAH', 'Dislexia', 'Trastorno del lenguaje'],
            'CAINI' => ['Maltrato infantil', 'Abuso sexual infantil', 'Negligencia parental', 'Trastorno vinculación'],
            'Foniatria' => ['Disfonía orgánica', 'Parálisis de cuerda vocal', 'Nódulos vocales', 'Fístula traqueoesofágica'],
            'Nutricion Clinica' => ['Obesidad mórbida', 'Desnutrición proteico-calórica', 'Síndrome metabólico', 'Trastorno de la conducta alimentaria'],
            'Medico Ocupacional' => ['Lumbago ocupacional', 'Síndrome del túnel carpiano laboral', 'Neumoconiosis', 'Hipoacusia laboral'],
            'Odontologia' => ['Caries dental', 'Enfermedad periodontal', 'Absceso periapical', 'Maloclusión'],
            'Electroencefalograma' => ['Epilepsia', 'Encefalopatía', 'Muerte cerebral', 'Status epiléptico'],
        ];

        $contador = 0;
        $especialidadesShuffled = $especialidades->shuffle();

        foreach ($especialidadesShuffled as $especialidad) {
            if ($contador >= 40) break;

            $lista = $nombresPatologias[$especialidad->nombre] ?? [];
            if (empty($lista)) continue;

            $nombre = $lista[$contador % count($lista)];
            $patologia = Patologia::create([
                'especialidad_id' => $especialidad->id,
                'nombre' => $nombre,
                'descripcion' => $faker->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $patologiasPorEspecialidad[$especialidad->id][] = $patologia->id;
            $contador++;
        }

        // Obtener citas atendidas
        $citasAtendidas = Cita::where('estado', 'Atendida')->get();

        foreach ($citasAtendidas as $cita) {
            $especialidadId = $cita->medico->especialidad_id ?? null;
            if (!$especialidadId) continue;

            // Diagnóstico libre
            if ($faker->boolean(60)) {
                $cita->diagnostico_libre = $faker->sentence(6);
            }

            // Patologías
            $patologiaIds = $patologiasPorEspecialidad[$especialidadId] ?? [];
            if (!empty($patologiaIds)) {
                $numPatologias = $faker->numberBetween(1, min(3, count($patologiaIds)));
                $selectedPatologias = (array) $faker->randomElements($patologiaIds, $numPatologias);
                $cita->patologias()->sync($selectedPatologias);
            }

            $cita->atendido_por = $userAdmin->id ?? 1;
            $cita->save();
        }
    }


}