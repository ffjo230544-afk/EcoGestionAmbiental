<?php
declare(strict_types=1);

/*
 | Contenido de los juegos.
 | Este archivo está dentro de config y no es accesible desde el navegador.
 | Las respuestas correctas nunca se imprimen en el HTML.
 */
return [
    'clasifica' => [
        'database_name' => 'Clasifica los residuos',
        'title' => 'Clasifica los residuos',
        'short_description' =>
            'Arrastra cada residuo al contenedor correcto antes de terminar.',
        'learning' =>
            'Aprende a separar residuos reciclables, orgánicos, especiales y peligrosos.',
        'icon' => 'bi-recycle',
        'theme' => 'green',
        'difficulty' => 'Básico',
        'route' => 'juegos/clasificar',
        'implemented' => true,
        'categories' => [
            'plastico' => [
                'label' => 'Plástico',
                'icon' => 'bi-cup-straw',
                'color' => 'blue',
            ],
            'papel_carton' => [
                'label' => 'Papel y cartón',
                'icon' => 'bi-box-seam',
                'color' => 'amber',
            ],
            'vidrio' => [
                'label' => 'Vidrio',
                'icon' => 'bi-cup',
                'color' => 'cyan',
            ],
            'metal' => [
                'label' => 'Metales',
                'icon' => 'bi-gear',
                'color' => 'gray',
            ],
            'organico' => [
                'label' => 'Orgánicos',
                'icon' => 'bi-flower1',
                'color' => 'green',
            ],
            'especial' => [
                'label' => 'Especiales y peligrosos',
                'icon' => 'bi-exclamation-triangle',
                'color' => 'red',
            ],
        ],
        'items' => [
            [
                'id' => 'botella_plastico',
                'name' => 'Botella de plástico',
                'emoji' => '🧴',
                'category' => 'plastico',
            ],
            [
                'id' => 'envase_yogur',
                'name' => 'Envase de yogur',
                'emoji' => '🥛',
                'category' => 'plastico',
            ],
            [
                'id' => 'caja_carton',
                'name' => 'Caja de cartón',
                'emoji' => '📦',
                'category' => 'papel_carton',
            ],
            [
                'id' => 'periodico',
                'name' => 'Periódico',
                'emoji' => '📰',
                'category' => 'papel_carton',
            ],
            [
                'id' => 'frasco_vidrio',
                'name' => 'Frasco de vidrio',
                'emoji' => '🫙',
                'category' => 'vidrio',
            ],
            [
                'id' => 'botella_vidrio',
                'name' => 'Botella de vidrio',
                'emoji' => '🍾',
                'category' => 'vidrio',
            ],
            [
                'id' => 'lata_aluminio',
                'name' => 'Lata de aluminio',
                'emoji' => '🥫',
                'category' => 'metal',
            ],
            [
                'id' => 'cubiertos_metal',
                'name' => 'Cubiertos de metal',
                'emoji' => '🍴',
                'category' => 'metal',
            ],
            [
                'id' => 'cascara_platano',
                'name' => 'Cáscara de plátano',
                'emoji' => '🍌',
                'category' => 'organico',
            ],
            [
                'id' => 'restos_comida',
                'name' => 'Restos de comida',
                'emoji' => '🥬',
                'category' => 'organico',
            ],
            [
                'id' => 'pila',
                'name' => 'Pila usada',
                'emoji' => '🔋',
                'category' => 'especial',
            ],
            [
                'id' => 'celular',
                'name' => 'Teléfono viejo',
                'emoji' => '📱',
                'category' => 'especial',
            ],
        ],
    ],

    'trivia' => [
        'database_name' => 'Trivia ambiental',
        'title' => 'EcoTrivia',
        'short_description' =>
            'Responde preguntas y descubre datos útiles para cuidar el ambiente.',
        'learning' =>
            'Refuerza conocimientos sobre agua, energía, reciclaje y consumo responsable.',
        'icon' => 'bi-patch-question-fill',
        'theme' => 'blue',
        'difficulty' => 'Básico e intermedio',
        'route' => 'juegos/trivia',
        'implemented' => true,
        'questions' => [
            [
                'id' => 'q1',
                'question' => '¿Dónde debe llevarse una pila usada?',
                'options' => [
                    'a' => 'Al contenedor de residuos orgánicos',
                    'b' => 'A un centro de recolección autorizado',
                    'c' => 'A cualquier bote de basura',
                ],
                'correct' => 'b',
                'explanation' =>
                    'Las pilas contienen sustancias que pueden contaminar el suelo y el agua; deben entregarse en centros autorizados.',
            ],
            [
                'id' => 'q2',
                'question' => '¿Cuál de estas acciones ayuda más a ahorrar agua?',
                'options' => [
                    'a' => 'Cerrar la llave mientras te cepillas los dientes',
                    'b' => 'Dejar correr el agua para que se enfríe',
                    'c' => 'Lavar la banqueta con manguera',
                ],
                'correct' => 'a',
                'explanation' =>
                    'Cerrar la llave evita desperdiciar varios litros de agua durante cada cepillado.',
            ],
            [
                'id' => 'q3',
                'question' => '¿Qué significa reutilizar?',
                'options' => [
                    'a' => 'Usar un objeto nuevamente o darle otra función',
                    'b' => 'Comprar el mismo producto varias veces',
                    'c' => 'Quemar los residuos para eliminarlos',
                ],
                'correct' => 'a',
                'explanation' =>
                    'Reutilizar prolonga la vida de los objetos y evita generar residuos innecesarios.',
            ],
            [
                'id' => 'q4',
                'question' => '¿Cuál es un residuo orgánico?',
                'options' => [
                    'a' => 'Una botella de vidrio',
                    'b' => 'Una lata de aluminio',
                    'c' => 'La cáscara de una fruta',
                ],
                'correct' => 'c',
                'explanation' =>
                    'Los restos de origen vegetal o animal pueden descomponerse y, en muchos casos, convertirse en composta.',
            ],
            [
                'id' => 'q5',
                'question' => '¿Qué conviene hacer antes de reciclar un envase?',
                'options' => [
                    'a' => 'Vaciarlo y limpiarlo ligeramente',
                    'b' => 'Llenarlo con otros residuos',
                    'c' => 'Romperlo siempre',
                ],
                'correct' => 'a',
                'explanation' =>
                    'Un envase vacío y limpio evita malos olores y facilita su aprovechamiento.',
            ],
            [
                'id' => 'q6',
                'question' => '¿Cuál opción reduce el uso de plásticos desechables?',
                'options' => [
                    'a' => 'Usar una botella rellenable',
                    'b' => 'Comprar vasos desechables diariamente',
                    'c' => 'Pedir bolsas adicionales',
                ],
                'correct' => 'a',
                'explanation' =>
                    'Los recipientes reutilizables sustituyen muchos productos de un solo uso.',
            ],
            [
                'id' => 'q7',
                'question' => '¿Qué se debe hacer con un teléfono que ya no funciona?',
                'options' => [
                    'a' => 'Abandonarlo en la calle',
                    'b' => 'Entregarlo en un centro que reciba electrónicos',
                    'c' => 'Mezclarlo con residuos de comida',
                ],
                'correct' => 'b',
                'explanation' =>
                    'Los electrónicos contienen materiales recuperables y componentes que requieren manejo especial.',
            ],
            [
                'id' => 'q8',
                'question' => '¿Cuál fuente de energía es renovable?',
                'options' => [
                    'a' => 'Carbón',
                    'b' => 'Petróleo',
                    'c' => 'Energía solar',
                ],
                'correct' => 'c',
                'explanation' =>
                    'La energía solar aprovecha una fuente natural que se renueva continuamente.',
            ],
            [
                'id' => 'q9',
                'question' => '¿Qué es la composta?',
                'options' => [
                    'a' => 'Abono producido con residuos orgánicos',
                    'b' => 'Una mezcla de plástico y metal',
                    'c' => 'Agua almacenada para beber',
                ],
                'correct' => 'a',
                'explanation' =>
                    'La composta transforma residuos orgánicos en un material útil para mejorar el suelo.',
            ],
            [
                'id' => 'q10',
                'question' => '¿Cuál es el orden más recomendable de las tres R?',
                'options' => [
                    'a' => 'Reciclar, reducir y reutilizar',
                    'b' => 'Reducir, reutilizar y reciclar',
                    'c' => 'Reutilizar, desechar y comprar',
                ],
                'correct' => 'b',
                'explanation' =>
                    'Primero se busca reducir el consumo, después reutilizar y finalmente reciclar lo que ya no puede aprovecharse directamente.',
            ],
        ],
    ],

    'memorama' => [
        'database_name' => 'Memorama ecológico',
        'title' => 'Memorama ecológico',
        'short_description' =>
            'Encuentra parejas entre residuos y su destino correcto.',
        'learning' =>
            'Relaciona cada material con su forma adecuada de manejo.',
        'icon' => 'bi-grid-3x3-gap-fill',
        'theme' => 'purple',
        'difficulty' => 'Intermedio',
        'route' => 'juegos/memorama',
        'implemented' => true,
        'pairs' => [
            [
                'id' => 'plastico',
                'first' => [
                    'id' => 'botella_plastica',
                    'label' => 'Botella de plástico',
                    'emoji' => '🧴',
                ],
                'second' => [
                    'id' => 'destino_plastico',
                    'label' => 'Contenedor de plástico',
                    'emoji' => '♻️',
                ],
                'explanation' =>
                    'Las botellas limpias y vacías pueden separarse con los plásticos reciclables.',
            ],
            [
                'id' => 'papel_carton',
                'first' => [
                    'id' => 'caja_carton',
                    'label' => 'Caja de cartón',
                    'emoji' => '📦',
                ],
                'second' => [
                    'id' => 'destino_carton',
                    'label' => 'Papel y cartón',
                    'emoji' => '📰',
                ],
                'explanation' =>
                    'El cartón seco y sin restos de comida puede aprovecharse nuevamente.',
            ],
            [
                'id' => 'vidrio',
                'first' => [
                    'id' => 'frasco_vidrio',
                    'label' => 'Frasco de vidrio',
                    'emoji' => '🫙',
                ],
                'second' => [
                    'id' => 'destino_vidrio',
                    'label' => 'Contenedor de vidrio',
                    'emoji' => '🍾',
                ],
                'explanation' =>
                    'Los envases de vidrio deben vaciarse y separarse sin mezclarlos con cerámica.',
            ],
            [
                'id' => 'metal',
                'first' => [
                    'id' => 'lata_aluminio',
                    'label' => 'Lata de aluminio',
                    'emoji' => '🥫',
                ],
                'second' => [
                    'id' => 'destino_metal',
                    'label' => 'Contenedor de metales',
                    'emoji' => '⚙️',
                ],
                'explanation' =>
                    'Las latas pueden reciclarse muchas veces cuando se separan correctamente.',
            ],
            [
                'id' => 'organico',
                'first' => [
                    'id' => 'cascara_platano',
                    'label' => 'Cáscara de plátano',
                    'emoji' => '🍌',
                ],
                'second' => [
                    'id' => 'destino_composta',
                    'label' => 'Composta',
                    'emoji' => '🌱',
                ],
                'explanation' =>
                    'Muchos restos vegetales pueden transformarse en composta para nutrir el suelo.',
            ],
            [
                'id' => 'especial',
                'first' => [
                    'id' => 'pila_usada',
                    'label' => 'Pila usada',
                    'emoji' => '🔋',
                ],
                'second' => [
                    'id' => 'destino_especial',
                    'label' => 'Acopio especial',
                    'emoji' => '⚠️',
                ],
                'explanation' =>
                    'Las pilas requieren un punto de acopio autorizado y nunca deben mezclarse con basura común.',
            ],
        ],
    ],
    'limpia-parque' => [
        'database_name' => 'Misión: limpia el parque',
        'title' => 'Misión: limpia el parque',
        'short_description' =>
            'Recoge los residuos y recupera un espacio natural.',
        'learning' =>
            'Identifica residuos mientras observas el impacto de mantener limpios los espacios públicos.',
        'icon' => 'bi-tree-fill',
        'theme' => 'lime',
        'difficulty' => 'Intermedio',
        'route' => 'juegos/limpia-parque',
        'implemented' => true,
        'objects' => [
            [
                'id' => 'botella_parque',
                'label' => 'Botella de plástico',
                'emoji' => '🧴',
                'type' => 'waste',
                'x' => 10,
                'y' => 68,
            ],
            [
                'id' => 'lata_parque',
                'label' => 'Lata',
                'emoji' => '🥫',
                'type' => 'waste',
                'x' => 25,
                'y' => 45,
            ],
            [
                'id' => 'bolsa_parque',
                'label' => 'Bolsa de plástico',
                'emoji' => '🛍️',
                'type' => 'waste',
                'x' => 42,
                'y' => 75,
            ],
            [
                'id' => 'vaso_parque',
                'label' => 'Vaso desechable',
                'emoji' => '🥤',
                'type' => 'waste',
                'x' => 58,
                'y' => 52,
            ],
            [
                'id' => 'papel_parque',
                'label' => 'Papel tirado',
                'emoji' => '📄',
                'type' => 'waste',
                'x' => 74,
                'y' => 74,
            ],
            [
                'id' => 'pila_parque',
                'label' => 'Pila usada',
                'emoji' => '🔋',
                'type' => 'waste',
                'x' => 88,
                'y' => 48,
            ],
            [
                'id' => 'carton_parque',
                'label' => 'Caja de cartón',
                'emoji' => '📦',
                'type' => 'waste',
                'x' => 17,
                'y' => 84,
            ],
            [
                'id' => 'envoltura_parque',
                'label' => 'Envoltura',
                'emoji' => '🍬',
                'type' => 'waste',
                'x' => 52,
                'y' => 88,
            ],
            [
                'id' => 'periodico_parque',
                'label' => 'Periódico',
                'emoji' => '📰',
                'type' => 'waste',
                'x' => 68,
                'y' => 38,
            ],
            [
                'id' => 'botella_vidrio_parque',
                'label' => 'Botella de vidrio',
                'emoji' => '🍾',
                'type' => 'waste',
                'x' => 83,
                'y' => 82,
            ],
            [
                'id' => 'flor_parque',
                'label' => 'Flor',
                'emoji' => '🌼',
                'type' => 'nature',
                'x' => 33,
                'y' => 61,
                'tip' => 'Las flores forman parte del parque; obsérvalas sin arrancarlas.',
            ],
            [
                'id' => 'mariposa_parque',
                'label' => 'Mariposa',
                'emoji' => '🦋',
                'type' => 'nature',
                'x' => 47,
                'y' => 28,
                'tip' => 'Las mariposas son polinizadoras y debemos proteger su hábitat.',
            ],
            [
                'id' => 'ave_parque',
                'label' => 'Ave',
                'emoji' => '🐦',
                'type' => 'nature',
                'x' => 77,
                'y' => 20,
                'tip' => 'Las aves no son residuos; evita molestarlas o alimentarlas con basura.',
            ],
            [
                'id' => 'hongo_parque',
                'label' => 'Hongo',
                'emoji' => '🍄',
                'type' => 'nature',
                'x' => 91,
                'y' => 69,
                'tip' => 'Los hongos participan en la descomposición natural del ecosistema.',
            ],
        ],
    ],
    'ruta-reciclaje' => [
        'database_name' => 'La ruta del reciclaje',
        'title' => 'La ruta del reciclaje',
        'short_description' =>
            'Ordena las etapas que transforman un residuo en un nuevo producto.',
        'learning' =>
            'Comprende el proceso completo de recuperación de distintos materiales.',
        'icon' => 'bi-signpost-split-fill',
        'theme' => 'orange',
        'difficulty' => 'Intermedio',
        'route' => 'juegos/ruta-reciclaje',
        'implemented' => true,
        'routes' => [
            [
                'id' => 'plastico',
                'title' => 'Ruta del plástico PET',
                'icon' => 'bi-cup-straw',
                'color' => 'blue',
                'description' =>
                    'Desde una botella usada hasta una nueva materia prima.',
                'stages' => [
                    [
                        'id' => 'pet_separar',
                        'title' => 'Separación en casa',
                        'description' => 'Vaciar, enjuagar y separar la botella.',
                        'emoji' => '🏠',
                    ],
                    [
                        'id' => 'pet_recolectar',
                        'title' => 'Recolección',
                        'description' => 'Llevar el PET a un punto de acopio.',
                        'emoji' => '🚛',
                    ],
                    [
                        'id' => 'pet_clasificar',
                        'title' => 'Clasificación y lavado',
                        'description' => 'Separar por tipo y retirar impurezas.',
                        'emoji' => '🧼',
                    ],
                    [
                        'id' => 'pet_triturar',
                        'title' => 'Triturado',
                        'description' => 'Convertir las botellas en pequeñas hojuelas.',
                        'emoji' => '⚙️',
                    ],
                    [
                        'id' => 'pet_fabricar',
                        'title' => 'Nuevo producto',
                        'description' => 'Usar el material recuperado para fabricar.',
                        'emoji' => '👕',
                    ],
                ],
            ],
            [
                'id' => 'vidrio',
                'title' => 'Ruta del vidrio',
                'icon' => 'bi-cup',
                'color' => 'cyan',
                'description' =>
                    'El vidrio puede reciclarse repetidamente sin perder calidad.',
                'stages' => [
                    [
                        'id' => 'vidrio_separar',
                        'title' => 'Separación segura',
                        'description' => 'Vaciar el envase y separar tapas o corchos.',
                        'emoji' => '🫙',
                    ],
                    [
                        'id' => 'vidrio_recolectar',
                        'title' => 'Traslado al acopio',
                        'description' => 'Depositarlo sin mezclarlo con cerámica.',
                        'emoji' => '♻️',
                    ],
                    [
                        'id' => 'vidrio_clasificar',
                        'title' => 'Clasificación por color',
                        'description' => 'Separar vidrio transparente, verde y ámbar.',
                        'emoji' => '🔎',
                    ],
                    [
                        'id' => 'vidrio_triturar',
                        'title' => 'Triturado en calcín',
                        'description' => 'Reducir el vidrio limpio a fragmentos.',
                        'emoji' => '✨',
                    ],
                    [
                        'id' => 'vidrio_fundir',
                        'title' => 'Fundición y moldeado',
                        'description' => 'Fundir el calcín y crear nuevos envases.',
                        'emoji' => '🔥',
                    ],
                ],
            ],
            [
                'id' => 'papel',
                'title' => 'Ruta del papel y cartón',
                'icon' => 'bi-file-earmark-text-fill',
                'color' => 'amber',
                'description' =>
                    'El papel seco puede regresar al ciclo como una hoja nueva.',
                'stages' => [
                    [
                        'id' => 'papel_separar',
                        'title' => 'Separación en seco',
                        'description' => 'Evitar papel mojado, sucio o con grasa.',
                        'emoji' => '📄',
                    ],
                    [
                        'id' => 'papel_recolectar',
                        'title' => 'Recolección',
                        'description' => 'Compactar y trasladar el material.',
                        'emoji' => '📦',
                    ],
                    [
                        'id' => 'papel_clasificar',
                        'title' => 'Clasificación',
                        'description' => 'Separar cartón, periódico y papel blanco.',
                        'emoji' => '🗂️',
                    ],
                    [
                        'id' => 'papel_pulpa',
                        'title' => 'Producción de pulpa',
                        'description' => 'Mezclar las fibras con agua y limpiarlas.',
                        'emoji' => '💧',
                    ],
                    [
                        'id' => 'papel_fabricar',
                        'title' => 'Nueva hoja',
                        'description' => 'Prensar y secar la pulpa recuperada.',
                        'emoji' => '📘',
                    ],
                ],
            ],
        ],
    ],
    'decisiones' => [
        'database_name' => 'Decisiones ecológicas en casa',
        'title' => 'Decisiones ecológicas en casa',
        'short_description' =>
            'Toma decisiones cotidianas y descubre su impacto ambiental.',
        'learning' =>
            'Practica hábitos responsables relacionados con agua, energía y consumo.',
        'icon' => 'bi-house-heart-fill',
        'theme' => 'teal',
        'difficulty' => 'Avanzado',
        'route' => 'juegos/decisiones',
        'implemented' => true,
        'scenarios' => [
            [
                'id' => 'agua_dientes',
                'room' => 'Baño',
                'icon' => 'bi-droplet-fill',
                'question' =>
                    'Mientras te cepillas los dientes, ¿qué haces con la llave?',
                'options' => [
                    [
                        'id' => 'abierta',
                        'label' => 'La dejo abierta todo el tiempo',
                        'impact' => 0,
                        'feedback' =>
                            'Una llave abierta desperdicia varios litros en pocos minutos.',
                    ],
                    [
                        'id' => 'poca_agua',
                        'label' => 'La dejo abierta con poca presión',
                        'impact' => 1,
                        'feedback' =>
                            'Reducir el flujo ayuda, pero todavía se desperdicia agua.',
                    ],
                    [
                        'id' => 'cerrada',
                        'label' => 'La cierro hasta que necesito enjuagarme',
                        'impact' => 2,
                        'feedback' =>
                            'Cerrar la llave durante el cepillado evita un consumo innecesario.',
                    ],
                ],
            ],
            [
                'id' => 'energia_luz',
                'room' => 'Sala',
                'icon' => 'bi-lightbulb-fill',
                'question' =>
                    'Sales de una habitación que quedará vacía. ¿Qué haces?',
                'options' => [
                    [
                        'id' => 'todo_encendido',
                        'label' => 'Dejo luces y televisión encendidas',
                        'impact' => 0,
                        'feedback' =>
                            'Los aparatos encendidos consumen energía aunque nadie los use.',
                    ],
                    [
                        'id' => 'solo_luz',
                        'label' => 'Apago la luz, pero dejo la televisión',
                        'impact' => 1,
                        'feedback' =>
                            'Apagar la luz ayuda, pero también conviene apagar los demás aparatos.',
                    ],
                    [
                        'id' => 'apagar_todo',
                        'label' => 'Apago todo lo que no se está usando',
                        'impact' => 2,
                        'feedback' =>
                            'Evitar consumos innecesarios reduce energía y emisiones asociadas.',
                    ],
                ],
            ],
            [
                'id' => 'lavado_ropa',
                'room' => 'Lavandería',
                'icon' => 'bi-basket2-fill',
                'question' =>
                    'Tienes poca ropa sucia y no es urgente lavarla. ¿Qué eliges?',
                'options' => [
                    [
                        'id' => 'carga_pequena',
                        'label' => 'Uso la lavadora con esa carga pequeña',
                        'impact' => 0,
                        'feedback' =>
                            'Una carga pequeña utiliza casi la misma agua y energía que una completa.',
                    ],
                    [
                        'id' => 'esperar_caliente',
                        'label' => 'Espero una carga completa y uso agua caliente',
                        'impact' => 1,
                        'feedback' =>
                            'La carga completa es mejor, aunque calentar agua incrementa el consumo.',
                    ],
                    [
                        'id' => 'esperar_fria',
                        'label' => 'Espero una carga completa y uso agua fría',
                        'impact' => 2,
                        'feedback' =>
                            'Aprovechar la capacidad y lavar en frío ahorra agua y energía.',
                    ],
                ],
            ],
            [
                'id' => 'compras_bolsa',
                'room' => 'Compras',
                'icon' => 'bi-bag-heart-fill',
                'question' =>
                    'Vas al mercado por varios productos. ¿Cómo los transportas?',
                'options' => [
                    [
                        'id' => 'bolsas_nuevas',
                        'label' => 'Pido una bolsa desechable para cada producto',
                        'impact' => 0,
                        'feedback' =>
                            'Las bolsas de un solo uso generan residuos evitables.',
                    ],
                    [
                        'id' => 'pocas_bolsas',
                        'label' => 'Pido pocas bolsas y las lleno bien',
                        'impact' => 1,
                        'feedback' =>
                            'Usar menos bolsas reduce residuos, pero todavía requiere material nuevo.',
                    ],
                    [
                        'id' => 'bolsa_reutilizable',
                        'label' => 'Llevo bolsas reutilizables',
                        'impact' => 2,
                        'feedback' =>
                            'Reutilizar una bolsa muchas veces evita numerosos desechables.',
                    ],
                ],
            ],
            [
                'id' => 'residuos_comida',
                'room' => 'Cocina',
                'icon' => 'bi-egg-fried',
                'question' =>
                    'Quedan cáscaras de frutas y verduras después de cocinar. ¿Qué haces?',
                'options' => [
                    [
                        'id' => 'drenaje',
                        'label' => 'Las arrojo al drenaje',
                        'impact' => 0,
                        'feedback' =>
                            'Los restos sólidos pueden obstruir tuberías y contaminar el agua.',
                    ],
                    [
                        'id' => 'basura_mezclada',
                        'label' => 'Las mezclo con toda la basura',
                        'impact' => 1,
                        'feedback' =>
                            'Se evita el drenaje, pero se pierde la oportunidad de aprovecharlas.',
                    ],
                    [
                        'id' => 'composta',
                        'label' => 'Las separo para composta',
                        'impact' => 2,
                        'feedback' =>
                            'Los residuos vegetales pueden transformarse en abono para el suelo.',
                    ],
                ],
            ],
            [
                'id' => 'aparatos_standby',
                'room' => 'Recámara',
                'icon' => 'bi-plug-fill',
                'question' =>
                    'No usarás durante varios días una consola y su pantalla. ¿Qué haces?',
                'options' => [
                    [
                        'id' => 'encendidos',
                        'label' => 'Los dejo encendidos para regresar rápido',
                        'impact' => 0,
                        'feedback' =>
                            'Mantener equipos encendidos consume energía y reduce su vida útil.',
                    ],
                    [
                        'id' => 'standby',
                        'label' => 'Los dejo conectados en modo de espera',
                        'impact' => 1,
                        'feedback' =>
                            'El modo de espera consume menos, pero mantiene un consumo fantasma.',
                    ],
                    [
                        'id' => 'desconectar',
                        'label' => 'Los apago y desconecto de forma segura',
                        'impact' => 2,
                        'feedback' =>
                            'Desconectar equipos sin uso evita el consumo eléctrico en espera.',
                    ],
                ],
            ],
            [
                'id' => 'fuga_agua',
                'room' => 'Patio',
                'icon' => 'bi-wrench-adjustable-circle-fill',
                'question' =>
                    'Detectas una llave que gotea constantemente. ¿Qué haces?',
                'options' => [
                    [
                        'id' => 'ignorar',
                        'label' => 'La ignoro porque cada gota parece pequeña',
                        'impact' => 0,
                        'feedback' =>
                            'Muchas gotas durante el día representan una cantidad importante de agua.',
                    ],
                    [
                        'id' => 'recipiente',
                        'label' => 'Coloco un recipiente y pospongo la reparación',
                        'impact' => 1,
                        'feedback' =>
                            'Recuperar agua ayuda temporalmente, pero la fuga continúa.',
                    ],
                    [
                        'id' => 'reparar',
                        'label' => 'Cierro el paso y solicito o realizo la reparación',
                        'impact' => 2,
                        'feedback' =>
                            'Reparar la causa detiene el desperdicio de manera permanente.',
                    ],
                ],
            ],
            [
                'id' => 'temperatura_casa',
                'room' => 'Climatización',
                'icon' => 'bi-thermometer-sun',
                'question' =>
                    'La casa está caliente, pero afuera corre aire fresco. ¿Qué haces primero?',
                'options' => [
                    [
                        'id' => 'clima_maximo',
                        'label' => 'Enciendo el aire acondicionado a la temperatura mínima',
                        'impact' => 0,
                        'feedback' =>
                            'Una temperatura extrema aumenta considerablemente el consumo eléctrico.',
                    ],
                    [
                        'id' => 'ventilador_cerrado',
                        'label' => 'Uso ventilador con todas las ventanas cerradas',
                        'impact' => 1,
                        'feedback' =>
                            'El ventilador consume menos, aunque no se aprovecha el aire exterior.',
                    ],
                    [
                        'id' => 'ventilar',
                        'label' => 'Abro ventanas y favorezco la ventilación cruzada',
                        'impact' => 2,
                        'feedback' =>
                            'Aprovechar la ventilación natural puede refrescar sin consumir electricidad.',
                    ],
                ],
            ],
        ],
    ],
];