<?php
/**
 * CardStudio — Professional Card Redesign Script
 * Upgrades all 200 designs with expert-level typography, color, effects, and composition.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';

$db = Database::getInstance()->getConnection();

// ─── CATEGORY DESIGN PROFILES ───────────────────────────────────────────────────
// Each profile defines professional design rules per category.
// We create 3 sub-profiles per category (for variation across the 20 designs).
$profiles = [

    // ═══════════════════════════════════════════════════════════════════════════════
    // 1. CUMPLEAÑOS (Birthdays) — Fun, vibrant, playful
    // ═══════════════════════════════════════════════════════════════════════════════
    1 => [
        'phrases' => [
            '¡Feliz Cumpleaños!', '¡Que lo pases genial!', '¡A Celebrar!',
            '¡Fiesta!', '¡Celebra con alegría!', 'Un año más de vida',
            '¡Disfruta!', '¡Brilla hoy!', '¡A reír y jugar!',
            '¡Hoy es tu día!', '¡Vamos a festejar!', 'Celebrando contigo',
            '¡Sorprise!', '¡Diversión total!', '¡Hoy todo es para ti!',
            '¡Que comience la fiesta!', '¡Risas y alegría!', '¡Momentos especiales!',
            '¡A disfrutar!', '¡La mejor fiesta!'
        ],
        'layouts' => [
            // Layout A: Bold playful — large title, medium name
            [
                'title' => ['size' => 52, 'family' => "'Fredoka One', cursive", 'angle' => 0],
                'name'  => ['size' => 38, 'family' => "'Nunito', sans-serif", 'angle' => 0],
            ],
            // Layout B: Curved script title, clean name
            [
                'title' => ['size' => 48, 'family' => "'Bubblegum Sans', cursive", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Quicksand', sans-serif", 'angle' => 0],
            ],
            // Layout C: Rounded fun font
            [
                'title' => ['size' => 46, 'family' => "'Baloo 2', cursive", 'angle' => 0],
                'name'  => ['size' => 40, 'family' => "'Poppins', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#FF6B6B', '#FFE66D', '#4ECDC4', '#A78BFA', '#FF9FF3', '#FF4757', '#FFA502', '#2ED573'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FFFACD'],
        'phrase_colors' => ['#FFE66D', '#FFF5EE', '#FFFACD', '#FFF8DC', '#E8E8E8'],
        'detail_colors' => ['#E8E8E8', '#F5F5F5', '#FFF5EE', '#FFFFFF', '#DCDCDC'],
        'shadow_dark' => true, // colorful shadows for fun
        'shadow_profiles' => [
            ['blur' => 8,  'color' => '#000000', 'ox' => 2, 'oy' => 3], // standard drop
            ['blur' => 12, 'color' => '#333333', 'ox' => 1, 'oy' => 2], // soft diffuse
            ['blur' => 6,  'color' => '#1a1a2e', 'ox' => 3, 'oy' => 3], // defined drop
        ],
        'border_profile' => ['color' => '#FFFFFF', 'width' => 1], // subtle white outline on titles
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 2. BAUTIZOS (Baptisms) — Delicate, elegant, pastel
    // ═══════════════════════════════════════════════════════════════════════════════
    2 => [
        'phrases' => [
            'Fe y esperanza', 'Un nuevo camino', 'Bendición divina',
            'Gracias a Dios', 'Dios te bendiga', 'Amor y fe',
            'Gracia divina', 'En sus brazos', 'Luz y amor',
            'Tu primer paso de fe', '天使 de Dios', 'Pureza y amor',
            'Bendecido/a', 'Fe, Esperanza y Amor', 'Un regalo de Dios',
            'Hijo/a de Dios', 'Creciendo en gracia', 'Amor infinito',
            'Un día especial', 'Tu día de gracia'
        ],
        'layouts' => [
            // Layout A: Elegant serif title, script name
            [
                'title' => ['size' => 38, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
                'name'  => ['size' => 42, 'family' => "'Great Vibes', cursive", 'angle' => 0],
            ],
            // Layout B: Script title, clean serif name
            [
                'title' => ['size' => 34, 'family' => "'Great Vibes', cursive", 'angle' => 0],
                'name'  => ['size' => 38, 'family' => "'Lora', serif", 'angle' => 0],
            ],
            // Layout C: Classic serif title, elegant sans name
            [
                'title' => ['size' => 36, 'family' => "'Cinzel', serif", 'angle' => 0],
                'name'  => ['size' => 40, 'family' => "'Raleway', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#D4AF37', '#C9B1A0', '#8B7D6B', '#4A6B8A', '#D4A5A5', '#A08060'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FAF0E6'],
        'phrase_colors' => ['#D4AF37', '#C9B1A0', '#8B7D6B', '#FFF8DC', '#E8DFD0'],
        'detail_colors' => ['#E8DFD0', '#C9B1A0', '#8B7D6B', '#F5F0E8', '#FFFFFF'],
        'shadow_dark' => false, // subtle, elegant shadows
        'shadow_profiles' => [
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.25)', 'ox' => 0, 'oy' => 2], // soft down
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 1, 'oy' => 1], // soft spread
            ['blur' => 6,  'color' => 'rgba(0,0,0,0.3)',  'ox' => 0, 'oy' => 3], // subtle depth
        ],
        'border_profile' => null, // no borders — elegance through shadow
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 3. BODAS (Weddings) — Elegant, romantic, sophisticated
    // ═══════════════════════════════════════════════════════════════════════════════
    3 => [
        'phrases' => [
            'Amor verdadero', 'Juntos para siempre', 'Nuestro día especial',
            'Amor eterno', 'Unidos por el amor', 'El comienzo de todo',
            'Te elegí a ti', 'Para toda la vida', 'Amor que perdura',
            'Nuestro amor', 'Dos almas, un corazón', 'Juntos en el amor',
            'Siempre tú y yo', 'Amanecer juntos', 'Amor sin fin',
            'Promesa eterna', 'Unidos para siempre', 'El día más hermoso',
            'Amor hecho realidad', 'Celebrando nuestro amor'
        ],
        'layouts' => [
            // Layout A: Script names + serif subtitle
            [
                'title' => ['size' => 32, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
                'name'  => ['size' => 44, 'family' => "'Great Vibes', cursive", 'angle' => 0],
            ],
            // Layout B: Elegant serif + light sans
            [
                'title' => ['size' => 30, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 42, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
            ],
            // Layout C: Decorative serif + script
            [
                'title' => ['size' => 28, 'family' => "'Cinzel', serif", 'angle' => 0],
                'name'  => ['size' => 46, 'family' => "'Great Vibes', cursive", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#C9A96E', '#D4AF37', '#E6BE8A', '#C9B1A0', '#8B7D6B', '#B8976A'],
        'name_colors'  => ['#FFFFFF', '#FFFFF0', '#FFF8DC', '#FFFAF0', '#FAF0E6', '#F5F0E8'],
        'phrase_colors' => ['#C9A96E', '#D4AF37', '#E6BE8A', '#FFF8DC', '#E8D5B7'],
        'detail_colors' => ['#E8D5B7', '#C9B1A0', '#FFFFFF', '#F5F0E8', '#DCDCDC'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 0, 'oy' => 2],
            ['blur' => 12, 'color' => 'rgba(0,0,0,0.15)', 'ox' => 1, 'oy' => 1],
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.25)', 'ox' => 0, 'oy' => 2],
        ],
        'border_profile' => null,
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 4. GRADUACIONES (Graduations) — Bold, formal, celebratory
    // ═══════════════════════════════════════════════════════════════════════════════
    4 => [
        'phrases' => [
            '¡Logrado!', 'El futuro es hoy', 'Orgullo y alegría',
            'Sueños cumplidos', '¡Sí se pudo!', 'Hacia las estrellas',
            'Un nuevo comienzo', '¡Felicidades!', 'Trabajo y dedicación',
            'El comienzo de algo grande', 'Brilla con luz propia',
            'Haz que pase', 'Con orgullo', 'Merecido éxito',
            '¡Meta alcanzada!', 'El esfuerzo rindió frutos', 'Dedicación y éxito',
            'Un logro más', 'Celebrando el éxito', 'Camino a la grandeza'
        ],
        'layouts' => [
            // Layout A: Bold caps display + clean sans
            [
                'title' => ['size' => 44, 'family' => "'Bebas Neue', sans-serif", 'angle' => 0],
                'name'  => ['size' => 38, 'family' => "'Montserrat', sans-serif", 'angle' => 0],
            ],
            // Layout B: Oswald bold + Roboto
            [
                'title' => ['size' => 42, 'family' => "'Oswald', sans-serif", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Roboto', sans-serif", 'angle' => 0],
            ],
            // Layout C: Montserrat bold + Lato light
            [
                'title' => ['size' => 46, 'family' => "'Montserrat', sans-serif", 'angle' => 0],
                'name'  => ['size' => 34, 'family' => "'Lato', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#FFFFFF', '#D4AF37', '#FFD700', '#F5F5F5', '#E8E8E8'],
        'name_colors'  => ['#D4AF37', '#FFD700', '#FFFFFF', '#FFFACD', '#F0E68C'],
        'phrase_colors' => ['#D4AF37', '#FFD700', '#FFFFFF', '#F5F5F5', '#FFFACD'],
        'detail_colors' => ['#E8E8E8', '#C0C0C0', '#FFFFFF', '#F5F5F5', '#DCDCDC'],
        'shadow_dark' => true,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => '#000000', 'ox' => 2, 'oy' => 3],
            ['blur' => 14, 'color' => '#111111', 'ox' => 1, 'oy' => 2],
            ['blur' => 8,  'color' => '#222222', 'ox' => 3, 'oy' => 3],
        ],
        'border_profile' => ['color' => '#D4AF37', 'width' => 1], // gold outline on titles
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 5. BABY SHOWER — Soft, tender, whimsical
    // ═══════════════════════════════════════════════════════════════════════════════
    5 => [
        'phrases' => [
            'Amor infinito', 'Un miracle está en camino', 'Bienvenido/a al mundo',
            'Nuestro pequeño tesoro', 'Llegando con amor', 'Esperando con ilusión',
            'Pequeño/a grande', 'Enchilada de amor', 'Dulce espera',
            'Un mundo de amor', 'Llegará pronto', 'Nuestra mayor aventura',
            'Recibiendo bendiciones', 'Amor en miniatura', 'Pequeño/a y perfecto/a',
            'Nueva vida, nuevo amor', 'Sueños por cumplir', 'Celebrando la vida',
            'Un nuevo comienzo', 'Corazón de melón'
        ],
        'layouts' => [
            // Layout A: Whimsical script + soft sans
            [
                'title' => ['size' => 44, 'family' => "'Dancing Script', cursive", 'angle' => 0],
                'name'  => ['size' => 34, 'family' => "'Quicksand', sans-serif", 'angle' => 0],
            ],
            // Layout B: Rounded display + clean body
            [
                'title' => ['size' => 42, 'family' => "'Pacifico', cursive", 'angle' => 0],
                'name'  => ['size' => 32, 'family' => "'Nunito', sans-serif", 'angle' => 0],
            ],
            // Layout C: Playful serif + soft sans
            [
                'title' => ['size' => 40, 'family' => "'Satisfy', cursive", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Poppins', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#FFB6C1', '#87CEEB', '#DDA0DD', '#F0E68C', '#98D8C8', '#F7CAC9'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FFF0F5'],
        'phrase_colors' => ['#FFB6C1', '#87CEEB', '#DDA0DD', '#FFF8DC', '#FFF0F5'],
        'detail_colors' => ['#E8DFD0', '#F5E6CA', '#D4C5A9', '#FFFFFF', '#F5F0E8'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.15)', 'ox' => 0, 'oy' => 2],
            ['blur' => 12, 'color' => 'rgba(0,0,0,0.1)',  'ox' => 1, 'oy' => 1],
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.12)', 'ox' => 0, 'oy' => 2],
        ],
        'border_profile' => null,
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 8. AGRADECIMIENTO (Thank You) — Warm, sincere, elegant
    // ═══════════════════════════════════════════════════════════════════════════════
    8 => [
        'phrases' => [
            'Gracias por todo', 'Con gratitud', 'De corazón',
            'Mil gracias', 'Gracias de corazón', 'Agradecido/a',
            'Gracias por estar', 'Tu cariño vale oro', 'Inolvidable',
            'Gracias infinitas', 'Con cariño y agradecimiento', 'Tus bondades',
            'Gracias por ser tú', 'Corazón agradecido', 'Gracias por siempre',
            'Un mundo de gracias', 'Gracias eternas', 'Agradecimiento eterno',
            'Gracias por cada momento', 'Con todo mi cariño'
        ],
        'layouts' => [
            // Layout A: Elegant script + serif
            [
                'title' => ['size' => 42, 'family' => "'Great Vibes', cursive", 'angle' => 0],
                'name'  => ['size' => 34, 'family' => "'Lora', serif", 'angle' => 0],
            ],
            // Layout B: Flowing script + light sans
            [
                'title' => ['size' => 40, 'family' => "'Dancing Script', cursive", 'angle' => 0],
                'name'  => ['size' => 32, 'family' => "'Nunito', sans-serif", 'angle' => 0],
            ],
            // Layout C: Classic serif italic + clean sans
            [
                'title' => ['size' => 38, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Montserrat', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#D4AF37', '#8B6F5E', '#C9B1A0', '#6B8F71', '#8B7D6B', '#B8976A'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FAF0E6'],
        'phrase_colors' => ['#D4AF37', '#8B6F5E', '#C9B1A0', '#FFF8DC', '#E8DFD0'],
        'detail_colors' => ['#E8DFD0', '#C9B1A0', '#8B6F5E', '#FFFFFF', '#F5F0E8'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 0, 'oy' => 2],
            ['blur' => 12, 'color' => 'rgba(0,0,0,0.15)', 'ox' => 1, 'oy' => 1],
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.25)', 'ox' => 0, 'oy' => 2],
        ],
        'border_profile' => null,
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 9. PRIMERA COMUNIÓN (First Communion) — Sacred, elegant, pure
    // ═══════════════════════════════════════════════════════════════════════════════
    9 => [
        'phrases' => [
            'Dios te bendiga', 'Fe y devoción', 'Un día de gracia',
            'Camino de fe', 'Luz divina', 'En las manos de Dios',
            'Amor sagrado', 'Gracia y amor', 'Mi primer gran paso',
            'Sagrada comunión', 'Paz y amor', 'Dios es amor',
            'Bendición divina', 'Fe que inspira', 'Corazón puro',
            'En camino a la luz', 'Santidad y alegría', 'Gracias por tu amor',
            'Un día inolvidable', 'En communion con Dios'
        ],
        'layouts' => [
            // Layout A: Serif title + elegant script name
            [
                'title' => ['size' => 36, 'family' => "'Cinzel', serif", 'angle' => 0],
                'name'  => ['size' => 40, 'family' => "'Great Vibes', cursive", 'angle' => 0],
            ],
            // Layout B: Cormorant title + serif name
            [
                'title' => ['size' => 34, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
                'name'  => ['size' => 38, 'family' => "'Lora', serif", 'angle' => 0],
            ],
            // Layout C: Playfair title + light sans name
            [
                'title' => ['size' => 32, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 42, 'family' => "'Raleway', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#D4AF37', '#FFFFFF', '#C9B1A0', '#8B7D6B', '#B8976A'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FAF0E6'],
        'phrase_colors' => ['#D4AF37', '#C9B1A0', '#FFF8DC', '#8B7D6B', '#E8DFD0'],
        'detail_colors' => ['#E8DFD0', '#C9B1A0', '#FFFFFF', '#8B7D6B', '#F5F0E8'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.25)', 'ox' => 0, 'oy' => 2],
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 1, 'oy' => 1],
            ['blur' => 6,  'color' => 'rgba(0,0,0,0.3)',  'ox' => 0, 'oy' => 3],
        ],
        'border_profile' => null,
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 10. DÍA DE LA MADRE (Mother's Day) — Warm, floral, romantic
    // ═══════════════════════════════════════════════════════════════════════════════
    10 => [
        'phrases' => [
            'Te queremos mamá', 'Gracias por todo', 'La mejor mamá del mundo',
            'Mamá te amo', 'Para mi madre', 'Eres mi héroe',
            'Gracias por tu amor', 'Mi angel de la vida', 'Mamá eres única',
            'Con amor infinito', 'Tu cariño es todo', 'Mamá y amiga',
            'Te amo con todo mi corazón', 'Mi refugio', 'La mujer más fuerte',
            'Gracias por cuidarme', 'Para siempre mamá', 'Mi inspiración',
            'Amor de madre', 'Bendita sea tu vida'
        ],
        'layouts' => [
            // Layout A: Flowing script title + soft serif name
            [
                'title' => ['size' => 38, 'family' => "'Great Vibes', cursive", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Lora', serif", 'angle' => 0],
            ],
            // Layout B: Elegant italic + light sans
            [
                'title' => ['size' => 40, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 34, 'family' => "'Montserrat', sans-serif", 'angle' => 0],
            ],
            // Layout C: Sacramento script + Nunito
            [
                'title' => ['size' => 42, 'family' => "'Sacramento', cursive", 'angle' => 0],
                'name'  => ['size' => 32, 'family' => "'Nunito', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#FF69B4', '#D4AF37', '#C4704B', '#E8B4B4', '#DDA0DD', '#FF1493'],
        'name_colors'  => ['#FFFFFF', '#FFF8DC', '#FFFFF0', '#FFFAF0', '#FFF0F5'],
        'phrase_colors' => ['#FF69B4', '#D4AF37', '#C4704B', '#FFF8DC', '#E8B4B4'],
        'detail_colors' => ['#FFF0F5', '#E8C8D4', '#FFFFFF', '#F5E6F0', '#F5F0E8'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 0, 'oy' => 2],
            ['blur' => 12, 'color' => 'rgba(0,0,0,0.15)', 'ox' => 1, 'oy' => 1],
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.2)',  'ox' => 0, 'oy' => 2],
        ],
        'border_profile' => null,
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 11. DÍA DEL PADRE (Father's Day) — Bold, masculine, strong
    // ═══════════════════════════════════════════════════════════════════════════════
    11 => [
        'phrases' => [
            'Con cariño', 'Mi héroe', 'Gracias papá',
            'Te quiero papá', 'Para mi padre', 'Eres mi ejemplo',
            'Papá eres el mejor', 'Gracias por todo', 'Fuerza y amor',
            'Mi pilar', 'Papá y amigo', 'Siempre juntos',
            'El hombre más fuerte', 'Mi guía en la vida', 'Gracias por ser papá',
            'Tu ejemplo me guía', 'Con orgullo y amor', 'Papá, te amo',
            'Mi mayor inspiración', 'Gracias por tu entrega'
        ],
        'layouts' => [
            // Layout A: Bold display + clean sans
            [
                'title' => ['size' => 42, 'family' => "'Bebas Neue', sans-serif", 'angle' => 0],
                'name'  => ['size' => 36, 'family' => "'Montserrat', sans-serif", 'angle' => 0],
            ],
            // Layout B: Oswald bold + Roboto
            [
                'title' => ['size' => 40, 'family' => "'Oswald', sans-serif", 'angle' => 0],
                'name'  => ['size' => 34, 'family' => "'Roboto', sans-serif", 'angle' => 0],
            ],
            // Layout C: Playfair serif + Lato
            [
                'title' => ['size' => 38, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 38, 'family' => "'Lato', sans-serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#D4AF37', '#FFFFFF', '#5B9BD5', '#C0C0C0', '#E8E8E8', '#FFD700'],
        'name_colors'  => ['#FFFFFF', '#D4AF37', '#FFD700', '#FFFACD', '#EBF5FB'],
        'phrase_colors' => ['#D4AF37', '#FFFFFF', '#C0C0C0', '#5B9BD5', '#F5F5F5'],
        'detail_colors' => ['#C0C0C0', '#E8E8E8', '#FFFFFF', '#F5F5F5', '#DCDCDC'],
        'shadow_dark' => true,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => '#000000', 'ox' => 2, 'oy' => 3],
            ['blur' => 14, 'color' => '#111111', 'ox' => 1, 'oy' => 2],
            ['blur' => 8,  'color' => '#222222', 'ox' => 3, 'oy' => 3],
        ],
        'border_profile' => ['color' => '#D4AF37', 'width' => 1],
    ],

    // ═══════════════════════════════════════════════════════════════════════════════
    // 12. MATRIMONIO (Marriage) — Timeless, sophisticated, formal
    // ═══════════════════════════════════════════════════════════════════════════════
    12 => [
        'phrases' => [
            'Amor eterno', 'Juntos para siempre', 'Nuestro día especial',
            'Promesa de amor', 'Amor verdadero', 'Unidos por el amor',
            'Día de nuestra boda', 'El amor más hermoso', 'Para siempre juntos',
            'Celebrando el amor', 'Nuestra historia de amor', 'Dos almas una',
            'Amor que perdura', 'Siempre tú y yo', 'Amanecer juntos',
            'Unión sagrada', 'El día que todo comenzó', 'Amor hecho realidad',
            'Compromiso eterno', 'Nos pertenecemos'
        ],
        'layouts' => [
            // Layout A: Elegant script + serif
            [
                'title' => ['size' => 36, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
                'name'  => ['size' => 44, 'family' => "'Great Vibes', cursive", 'angle' => 0],
            ],
            // Layout B: Decorative serif + script
            [
                'title' => ['size' => 32, 'family' => "'Cinzel', serif", 'angle' => 0],
                'name'  => ['size' => 42, 'family' => "'Sacramento', cursive", 'angle' => 0],
            ],
            // Layout C: Playfair + Cormorant
            [
                'title' => ['size' => 30, 'family' => "'Playfair Display', serif", 'angle' => 0],
                'name'  => ['size' => 46, 'family' => "'Cormorant Garamond', serif", 'angle' => 0],
            ],
        ],
        'title_colors' => ['#D4AF37', '#C9A96E', '#E6BE8A', '#C9B1A0', '#B8976A', '#FFFFFF'],
        'name_colors'  => ['#FFFFFF', '#FFFFF0', '#FFF8DC', '#FFFAF0', '#FAF0E6', '#F5F0E8'],
        'phrase_colors' => ['#D4AF37', '#C9A96E', '#E6BE8A', '#FFF8DC', '#E8D5B7'],
        'detail_colors' => ['#E8D5B7', '#C9B1A0', '#FFFFFF', '#F5F0E8', '#DCDCDC'],
        'shadow_dark' => false,
        'shadow_profiles' => [
            ['blur' => 10, 'color' => 'rgba(0,0,0,0.2)',  'ox' => 0, 'oy' => 2],
            ['blur' => 12, 'color' => 'rgba(0,0,0,0.15)', 'ox' => 1, 'oy' => 1],
            ['blur' => 8,  'color' => 'rgba(0,0,0,0.25)', 'ox' => 0, 'oy' => 2],
        ],
        'border_profile' => null,
    ],
];

// ─── DESIGN GENERATION ENGINE ─────────────────────────────────────────────────────

function deterministicRandom($seed, $index = 0) {
    $combined = $seed * 2654435761 + $index * 40503;
    $combined = $combined & 0x7FFFFFFF;
    return ($combined % 1000) / 1000.0;
}

function pickColor($colors, $seed, $index) {
    $idx = (int)(deterministicRandom($seed, $index) * count($colors));
    return $colors[$idx % count($colors)];
}

function buildDesign($designId, $catId, $profile, $designIndex) {
    $subProfileIdx = $designIndex % count($profile['layouts']);
    $layout = $profile['layouts'][$subProfileIdx];

    // Deterministic variation seed
    $seed = $designId * 7 + $catId * 13;

    // ── Layer 1: TITLE ──────────────────────────────────────────────────────
    $titleColor = pickColor($profile['title_colors'], $seed, 0);
    $titleShadowIdx = $designIndex % count($profile['shadow_profiles']);
    $titleShadow = $profile['shadow_profiles'][$titleShadowIdx];

    $titleLayer = [
        'contenido' => null, // preserved from DB
        'x' => 80 + (int)(deterministicRandom($seed, 10) * 40), // 80-120
        'y' => 40 + (int)(deterministicRandom($seed, 11) * 30), // 40-70
        'size' => $layout['title']['size'],
        'family' => $layout['title']['family'],
        'color' => $titleColor,
        'angle' => $layout['title']['angle'],
        'hasBorder' => $profile['border_profile'] !== null,
        'bColor' => $profile['border_profile'] ? $profile['border_profile']['color'] : '#000000',
        'bWidth' => $profile['border_profile'] ? $profile['border_profile']['width'] : 2,
        'hasShadow' => true,
        'sBlur' => $titleShadow['blur'],
        'sColor' => $titleShadow['color'],
        'sOffsetX' => $titleShadow['ox'],
        'sOffsetY' => $titleShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 280 + (int)(deterministicRandom($seed, 12) * 100), // 280-380
        'align' => 'center',
    ];

    // ── Layer 2: NAME ──────────────────────────────────────────────────────
    $nameColor = pickColor($profile['name_colors'], $seed, 1);
    $nameShadowIdx = ($designIndex + 1) % count($profile['shadow_profiles']);
    $nameShadow = $profile['shadow_profiles'][$nameShadowIdx];

    $nameLayer = [
        'contenido' => null,
        'x' => 60 + (int)(deterministicRandom($seed, 20) * 60), // 60-120
        'y' => 130 + (int)(deterministicRandom($seed, 21) * 40), // 130-170
        'size' => $layout['name']['size'],
        'family' => $layout['name']['family'],
        'color' => $nameColor,
        'angle' => $layout['name']['angle'],
        'hasBorder' => false,
        'bColor' => '#000000',
        'bWidth' => 2,
        'hasShadow' => true,
        'sBlur' => $nameShadow['blur'],
        'sColor' => $nameShadow['color'],
        'sOffsetX' => $nameShadow['ox'],
        'sOffsetY' => $nameShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 300 + (int)(deterministicRandom($seed, 22) * 120), // 300-420
        'align' => 'center',
    ];

    // ── Layer 3: PHRASE/SUBTITLE ──────────────────────────────────────────
    $phraseColor = pickColor($profile['phrase_colors'], $seed, 2);
    $phraseShadowIdx = ($designIndex + 2) % count($profile['shadow_profiles']);
    $phraseShadow = $profile['shadow_profiles'][$phraseShadowIdx];
    $phraseText = pickColor($profile['phrases'], $seed, 30);

    $phraseLayer = [
        'contenido' => $phraseText,
        'x' => 60 + (int)(deterministicRandom($seed, 30) * 60),
        'y' => 220 + (int)(deterministicRandom($seed, 31) * 40), // 220-260
        'size' => 20 + (int)(deterministicRandom($seed, 32) * 6), // 20-26
        'family' => $profile['layouts'][($subProfileIdx + 1) % count($profile['layouts'])]['title']['family'],
        'color' => $phraseColor,
        'angle' => 0,
        'hasBorder' => false,
        'bColor' => '#000000',
        'bWidth' => 2,
        'hasShadow' => true,
        'sBlur' => $phraseShadow['blur'],
        'sColor' => $phraseShadow['color'],
        'sOffsetX' => $phraseShadow['ox'],
        'sOffsetY' => $phraseShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 300 + (int)(deterministicRandom($seed, 33) * 100), // 300-400
        'align' => 'center',
    ];

    // ── Layer 4: DATE ──────────────────────────────────────────────────────
    $dateColor = pickColor($profile['detail_colors'], $seed, 4);
    $dateShadowIdx = ($designIndex + 3) % count($profile['shadow_profiles']);
    $dateShadow = $profile['shadow_profiles'][$dateShadowIdx];

    $dateLayer = [
        'contenido' => 'Escribe la fecha aquí',
        'x' => 100 + (int)(deterministicRandom($seed, 40) * 40),
        'y' => 440 + (int)(deterministicRandom($seed, 41) * 40), // 440-480
        'size' => 18 + (int)(deterministicRandom($seed, 42) * 4), // 18-22
        'family' => $layout['name']['family'], // body font
        'color' => $dateColor,
        'angle' => 0,
        'hasBorder' => false,
        'bColor' => '#000000',
        'bWidth' => 2,
        'hasShadow' => true,
        'sBlur' => $dateShadow['blur'],
        'sColor' => $dateShadow['color'],
        'sOffsetX' => $dateShadow['ox'],
        'sOffsetY' => $dateShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 280 + (int)(deterministicRandom($seed, 43) * 80), // 280-360
        'align' => 'center',
    ];

    // ── Layer 5: LOCATION ──────────────────────────────────────────────────
    $locColor = pickColor($profile['detail_colors'], $seed, 5);
    $locShadowIdx = ($designIndex + 4) % count($profile['shadow_profiles']);
    $locShadow = $profile['shadow_profiles'][$locShadowIdx];

    $locLayer = [
        'contenido' => 'Escribe el lugar aquí',
        'x' => 100 + (int)(deterministicRandom($seed, 50) * 40),
        'y' => 490 + (int)(deterministicRandom($seed, 51) * 40), // 490-530
        'size' => 16 + (int)(deterministicRandom($seed, 52) * 3), // 16-19
        'family' => $layout['name']['family'], // body font
        'color' => $locColor,
        'angle' => 0,
        'hasBorder' => false,
        'bColor' => '#000000',
        'bWidth' => 2,
        'hasShadow' => true,
        'sBlur' => $locShadow['blur'],
        'sColor' => $locShadow['color'],
        'sOffsetX' => $locShadow['ox'],
        'sOffsetY' => $locShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 280 + (int)(deterministicRandom($seed, 53) * 80),
        'align' => 'center',
    ];

    // ── Layer 6: TIME ──────────────────────────────────────────────────────
    $timeColor = pickColor($profile['detail_colors'], $seed, 6);
    $timeShadowIdx = ($designIndex + 5) % count($profile['shadow_profiles']);
    $timeShadow = $profile['shadow_profiles'][$timeShadowIdx];

    $timeLayer = [
        'contenido' => 'Escribe la hora aquí',
        'x' => 100 + (int)(deterministicRandom($seed, 60) * 40),
        'y' => 540 + (int)(deterministicRandom($seed, 61) * 40), // 540-580
        'size' => 16 + (int)(deterministicRandom($seed, 62) * 3), // 16-19
        'family' => $layout['name']['family'], // body font
        'color' => $timeColor,
        'angle' => 0,
        'hasBorder' => false,
        'bColor' => '#000000',
        'bWidth' => 2,
        'hasShadow' => true,
        'sBlur' => $timeShadow['blur'],
        'sColor' => $timeShadow['color'],
        'sOffsetX' => $timeShadow['ox'],
        'sOffsetY' => $timeShadow['oy'],
        'width' => 0,
        'height' => 0,
        'type' => 'paragraph',
        'maxWidth' => 280 + (int)(deterministicRandom($seed, 63) * 80),
        'align' => 'center',
    ];

    return [$titleLayer, $nameLayer, $phraseLayer, $dateLayer, $locLayer, $timeLayer];
}

// ─── MAIN EXECUTION ──────────────────────────────────────────────────────────────
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║     CardStudio — Professional Card Redesign Engine          ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Fetch all designs
$result = $db->query("SELECT id_diseno, nombre_diseno, id_categoria, configuracion_textos_json FROM disenos ORDER BY id_categoria, id_diseno");
$designs = $result->fetch_all(MYSQLI_ASSOC);

echo "Found " . count($designs) . " designs to redesign.\n\n";

$updated = 0;
$errors = 0;
$catCounters = [];

foreach ($designs as $i => $design) {
    $designId = $design['id_diseno'];
    $catId = $design['id_categoria'];

    if (!isset($profiles[$catId])) {
        echo "⚠ No profile for category $catId (design $designId). Skipping.\n";
        continue;
    }

    if (!isset($catCounters[$catId])) $catCounters[$catId] = 0;
    $designIndex = $catCounters[$catId]++;

    // Preserve existing title/name content from current config
    $currentConfig = json_decode($design['configuracion_textos_json'], true);
    if (!is_array($currentConfig) || count($currentConfig) < 6) {
        echo "⚠ Invalid config for design $designId. Skipping.\n";
        $errors++;
        continue;
    }

    $newConfig = buildDesign($designId, $catId, $profiles[$catId], $designIndex);

    // Preserve content from existing layers
    for ($layerIdx = 0; $layerIdx < 6; $layerIdx++) {
        if (isset($currentConfig[$layerIdx]['contenido'])) {
            $newConfig[$layerIdx]['contenido'] = $currentConfig[$layerIdx]['contenido'];
        }
    }

    // Encode and update
    $json = json_encode($newConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $db->prepare("UPDATE disenos SET configuracion_textos_json = ? WHERE id_diseno = ?");
    $stmt->bind_param("si", $json, $designId);

    if ($stmt->execute()) {
        $updated++;
        if ($updated % 20 === 0) {
            echo "  ✓ Updated $updated designs...\n";
        }
    } else {
        echo "✗ Error updating design $designId: " . $db->error . "\n";
        $errors++;
    }
    $stmt->close();
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "  RESULTS: $updated designs updated, $errors errors.\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Summary by category:\n";
foreach ($catCounters as $catId => $count) {
    $catName = '';
    $catRes = $db->query("SELECT nombre FROM categorias WHERE id = $catId");
    if ($catRow = $catRes->fetch_assoc()) $catName = $catRow['nombre'];
    echo "  Cat $catId ($catName): $count designs updated\n";
}

echo "\nDone. Each design now uses professional font pairings, category-appropriate\n";
echo "colors, varied shadows, and properly weighted text hierarchy.\n";
