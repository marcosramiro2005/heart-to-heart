<?php

namespace Database\Seeders;

use App\Models\Resource;
use Illuminate\Database\Seeder;

class ResourceLibrarySeeder extends Seeder
{
    public function run(): void
    {
        $recursos = [
            // ── Ansiedad ──
            [
                'title'      => '¿Qué es la ansiedad y cómo reconocerla?',
                'summary'    => 'Aprende a identificar los síntomas de la ansiedad y entiende por qué ocurre.',
                'content'    => 'La ansiedad es una respuesta natural del cuerpo ante situaciones de estrés o peligro percibido. Sus síntomas incluyen taquicardia, tensión muscular, pensamientos acelerados y dificultad para concentrarse. Es importante diferenciar la ansiedad normal de la patológica. La ansiedad se convierte en un problema cuando interfiere con el día a día. Técnicas como la respiración diafragmática, la meditación y el ejercicio regular han demostrado ser eficaces para reducirla. Si los síntomas persisten, consultar con un profesional de salud mental es fundamental.',
                'category'   => 'ansiedad',
                'type'       => 'article',
                'read_time'  => 5,
                'is_featured'=> true,
            ],
            [
                'title'      => 'Técnica 4-7-8 para calmar la ansiedad inmediatamente',
                'summary'    => 'Una técnica de respiración respaldada por la ciencia que reduce la ansiedad en minutos.',
                'content'    => 'La técnica de respiración 4-7-8, desarrollada por el Dr. Andrew Weil, es una de las formas más efectivas de activar el sistema nervioso parasimpático y reducir la respuesta de estrés. Consiste en inhalar durante 4 segundos, mantener la respiración durante 7 segundos y exhalar lentamente durante 8 segundos. Repetir este ciclo 4 veces. Esta técnica funciona porque la exhalación prolongada activa el nervio vago, responsable de la respuesta de relajación del cuerpo.',
                'category'   => 'ansiedad',
                'type'       => 'exercise',
                'read_time'  => 3,
                'is_featured'=> false,
            ],
            [
                'title'      => 'Cómo gestionar los pensamientos negativos automáticos',
                'summary'    => 'Aprende a identificar y desafiar los pensamientos que alimentan la ansiedad.',
                'content'    => 'Los pensamientos negativos automáticos son interpretaciones distorsionadas de la realidad que generan ansiedad y malestar. La terapia cognitivo-conductual (TCC) propone identificarlos, cuestionar su veracidad y sustituirlos por pensamientos más realistas. El primer paso es tomar conciencia de ellos mediante un diario de pensamientos. Luego, preguntarse: ¿Tengo evidencia real de esto? ¿Estoy catastrofizando? ¿Qué le diría a un amigo en esta situación? Con práctica, este proceso se vuelve automático y reduce significativamente la ansiedad.',
                'category'   => 'ansiedad',
                'type'       => 'article',
                'read_time'  => 6,
                'is_featured'=> false,
            ],

            // ── Depresión ──
            [
                'title'      => 'Diferencia entre tristeza y depresión: ¿cuándo pedir ayuda?',
                'summary'    => 'Entender la diferencia es el primer paso para buscar el apoyo adecuado.',
                'content'    => 'La tristeza es una emoción normal y temporal ante pérdidas o dificultades. La depresión, en cambio, es un trastorno que persiste durante semanas o meses, afectando la capacidad de funcionar en el día a día. Sus síntomas incluyen pérdida de interés en actividades antes placenteras, cambios en el sueño y el apetito, fatiga extrema, dificultad para concentrarse y, en casos graves, pensamientos de muerte. Si estos síntomas persisten más de dos semanas, es importante buscar apoyo profesional. La depresión tiene tratamiento efectivo y recuperarse es posible.',
                'category'   => 'depresion',
                'type'       => 'article',
                'read_time'  => 7,
                'is_featured'=> true,
            ],
            [
                'title'      => 'Activación conductual: pequeños pasos para salir de la depresión',
                'summary'    => 'Una estrategia basada en la acción para romper el ciclo de la depresión.',
                'content'    => 'La activación conductual es una de las estrategias más efectivas contra la depresión. Consiste en programar actividades placenteras o significativas aunque no tengamos ganas de hacerlas. La depresión crea un ciclo vicioso: nos sentimos mal, dejamos de hacer cosas, nos sentimos peor. La activación rompe ese ciclo. Empieza por actividades pequeñas: dar un paseo de 10 minutos, llamar a un amigo, preparar tu comida favorita. No esperes tener ganas para actuar; la motivación suele venir después de la acción.',
                'category'   => 'depresion',
                'type'       => 'exercise',
                'read_time'  => 5,
                'is_featured'=> false,
            ],

            // ── Mindfulness ──
            [
                'title'      => 'Introducción al mindfulness: vivir en el presente',
                'summary'    => 'Descubre cómo la atención plena puede transformar tu relación con el estrés.',
                'content'    => 'El mindfulness o atención plena es la práctica de prestar atención al momento presente sin juzgar. Tiene sus raíces en la meditación budista y ha sido ampliamente estudiado por la ciencia occidental. Practicar mindfulness regularmente reduce el estrés, mejora la concentración y aumenta el bienestar emocional. No requiere equipamiento especial ni experiencia previa. Puedes empezar con 5 minutos al día prestando atención a tu respiración, a las sensaciones del cuerpo o a los sonidos del entorno. La clave es observar sin juzgar ni intentar cambiar lo que surge.',
                'category'   => 'mindfulness',
                'type'       => 'article',
                'read_time'  => 6,
                'is_featured'=> true,
            ],
            [
                'title'      => 'Meditación body scan para principiantes',
                'summary'    => 'Un ejercicio guiado para conectar con tu cuerpo y liberar la tensión acumulada.',
                'content'    => 'El body scan o escáner corporal es una técnica de meditación que consiste en llevar la atención progresivamente por distintas partes del cuerpo. Empieza tumbado o sentado cómodamente. Cierra los ojos y lleva la atención a los pies. Observa las sensaciones sin intentar cambiarlas. Sube lentamente la atención hacia las piernas, el abdomen, el pecho, los brazos, el cuello y la cabeza. Si la mente se distrae, vuelve suavemente al cuerpo. Este ejercicio practicado 10-15 minutos al día reduce la tensión física y mejora la conciencia corporal.',
                'category'   => 'mindfulness',
                'type'       => 'exercise',
                'read_time'  => 4,
                'is_featured'=> false,
            ],

            // ── Sueño ──
            [
                'title'      => 'La ciencia del sueño: por qué dormir bien lo cambia todo',
                'summary'    => 'Descubre cómo el sueño afecta a tu salud mental y cómo mejorarlo.',
                'content'    => 'El sueño es una función biológica esencial para la salud física y mental. Durante el sueño el cerebro consolida recuerdos, elimina toxinas y regula las emociones. La privación crónica de sueño aumenta el riesgo de ansiedad, depresión, problemas cardiovasculares y deterioro cognitivo. Los adultos necesitan entre 7 y 9 horas de sueño por noche. La higiene del sueño incluye mantener horarios regulares, evitar pantallas antes de dormir, crear un ambiente fresco y oscuro, y evitar cafeína después de las 14:00.',
                'category'   => 'sueno',
                'type'       => 'article',
                'read_time'  => 8,
                'is_featured'=> true,
            ],
            [
                'title'      => 'Rutina nocturna para dormir mejor en 7 pasos',
                'summary'    => 'Crea un ritual de descanso que le indique a tu cuerpo que es hora de dormir.',
                'content'    => 'Una rutina nocturna consistente entrena al cerebro para anticipar el sueño. Paso 1: Establece una hora fija para acostarte. Paso 2: Apaga las pantallas 60 minutos antes. Paso 3: Reduce la iluminación del hogar. Paso 4: Date una ducha tibia (baja la temperatura corporal al salir, facilitando el sueño). Paso 5: Practica 10 minutos de respiración o meditación. Paso 6: Escribe en un diario lo que te preocupa para sacarlo de tu cabeza. Paso 7: Lee algo tranquilo (no de trabajo). La consistencia es clave: sigue la rutina aunque no tengas sueño.',
                'category'   => 'sueno',
                'type'       => 'exercise',
                'read_time'  => 4,
                'is_featured'=> false,
            ],

            // ── Autoestima ──
            [
                'title'      => 'Cómo construir una autoestima sólida y duradera',
                'summary'    => 'La autoestima no es algo que se tiene o no se tiene, se entrena.',
                'content'    => 'La autoestima es la valoración que hacemos de nosotros mismos. No es fija: se puede trabajar y fortalecer. Los pilares de una autoestima sólida son el autoconocimiento (conocer mis virtudes y límites), la autocompasión (tratarme con la misma amabilidad que a un amigo), el establecimiento de límites saludables y la persecución de metas alineadas con mis valores. Las comparaciones constantes con otros erosionan la autoestima. Practica el diálogo interno positivo: observa cómo te hablas a ti mismo y cuestiona si lo harías con un ser querido.',
                'category'   => 'autoestima',
                'type'       => 'article',
                'read_time'  => 7,
                'is_featured'=> true,
            ],
            [
                'title'      => 'El diario de logros: celebra tus pequeños avances',
                'summary'    => 'Una práctica sencilla para contrarrestar el sesgo negativo de la mente.',
                'content'    => 'El cerebro humano tiene un sesgo hacia lo negativo: recuerda mejor los fracasos que los éxitos. El diario de logros contrarresta esto. Cada noche escribe 3 cosas que hayas hecho bien ese día, por pequeñas que sean. No tienen que ser grandes hazañas: haber llamado a alguien que lo necesitaba, haber cocinado algo sano, haber terminado una tarea pendiente. Con el tiempo, este hábito entrena al cerebro a notar y valorar sus capacidades, fortaleciendo la autoestima.',
                'category'   => 'autoestima',
                'type'       => 'exercise',
                'read_time'  => 3,
                'is_featured'=> false,
            ],

            // ── Relaciones ──
            [
                'title'      => 'Comunicación asertiva: expresa lo que sientes sin herir',
                'summary'    => 'Aprende a comunicar tus necesidades de forma clara, honesta y respetuosa.',
                'content'    => 'La comunicación asertiva es la capacidad de expresar pensamientos, sentimientos y necesidades de forma directa y respetuosa, sin agresividad ni pasividad. Se basa en tres pilares: el respeto hacia uno mismo, el respeto hacia el otro y la claridad en el mensaje. Una fórmula práctica es el mensaje "Yo": "Cuando ocurre X, yo me siento Y, y necesito Z". Esta estructura evita el lenguaje acusatorio y facilita la comprensión mutua. Practicar la asertividad mejora las relaciones personales y reduce el resentimiento.',
                'category'   => 'relaciones',
                'type'       => 'article',
                'read_time'  => 6,
                'is_featured'=> false,
            ],
            [
                'title'      => 'Establecer límites saludables en tus relaciones',
                'summary'    => 'Los límites no son muros, son la base de las relaciones sanas.',
                'content'    => 'Los límites saludables son acuerdos que establecemos con los demás sobre qué comportamientos son aceptables y cuáles no. Ponerlos no es egoísta, es necesario para el bienestar de ambas partes. Identificar los propios límites requiere autoconocimiento: ¿qué situaciones me agotan? ¿qué comportamientos me hacen sentir mal? Una vez identificados, comunicarlos con calma y firmeza. Es normal que al principio genere incomodidad, tanto en nosotros como en los demás. Con el tiempo, las relaciones con límites claros son más sanas y satisfactorias.',
                'category'   => 'relaciones',
                'type'       => 'article',
                'read_time'  => 5,
                'is_featured'=> false,
            ],

            // ── Alimentación ──
            [
                'title'      => 'El eje intestino-cerebro: cómo la alimentación afecta al estado de ánimo',
                'summary'    => 'Descubre la conexión científica entre lo que comes y cómo te sientes.',
                'content'    => 'El intestino y el cerebro están conectados a través del nervio vago en lo que se conoce como el eje intestino-cerebro. El 90% de la serotonina, el neurotransmisor del bienestar, se produce en el intestino. Una dieta rica en fibra, probióticos y alimentos fermentados favorece la microbiota intestinal y, con ella, el estado de ánimo. Los alimentos ultraprocesados, el azúcar y el alcohol se asocian con mayor riesgo de depresión y ansiedad. Incluir en la dieta pescado azul, nueces, legumbres, frutas y verduras variadas tiene un impacto positivo demostrado en la salud mental.',
                'category'   => 'alimentacion',
                'type'       => 'article',
                'read_time'  => 7,
                'is_featured'=> true,
            ],

            // ── Ejercicio ──
            [
                'title'      => 'El ejercicio como antidepresivo natural: qué dice la ciencia',
                'summary'    => 'El movimiento es una de las herramientas más potentes para la salud mental.',
                'content'    => 'Múltiples estudios han demostrado que el ejercicio aeróbico regular tiene efectos comparables a los antidepresivos en casos de depresión leve y moderada. El mecanismo incluye la liberación de endorfinas, serotonina y dopamina, la reducción del cortisol (hormona del estrés) y la neurogénesis en el hipocampo (zona del cerebro afectada por la depresión). No hace falta correr una maratón: 30 minutos de caminata a paso ligero cinco días a la semana producen mejoras significativas. El ejercicio también mejora la calidad del sueño y la autoestima.',
                'category'   => 'ejercicio',
                'type'       => 'article',
                'read_time'  => 6,
                'is_featured'=> false,
            ],
        ];

        foreach ($recursos as $recurso) {
            Resource::updateOrCreate(
                ['title' => $recurso['title']],
                $recurso
            );
        }
    }
}