<?php

/**
 * Versione di bootstrap-italia vendorizzata in static/ (css, js bundle, svg/sprite.svg).
 * Usata come cache buster negli enqueue: va aggiornata a mano insieme ai file, seguendo
 * la procedura in README.md. Finche' resta indietro, i browser continuano a servire dalla
 * cache il foglio della versione precedente.
 *
 * E' uno dei tre punti in cui la versione e' dichiarata, insieme ai marker dentro i due
 * asset: inc/version-check.php li confronta e segnala in bacheca se divergono, e
 * `php bin/check-bi-version.php` fa la stessa verifica da riga di comando.
 */
if (!defined('italiawp3_BOOTSTRAP_ITALIA_VERSION')) {
    define('italiawp3_BOOTSTRAP_ITALIA_VERSION', '2.18.3');
}

/**
 * Enqueue admin stylesheet
 */
function italiawp3_admin_style() {
    wp_register_style('italiawp3-admin-css', get_template_directory_uri() . '/inc/admin.css');
    wp_enqueue_style('italiawp3-admin-css');
}

add_action('admin_enqueue_scripts', 'italiawp3_admin_style');

/**
 * Enqueue editor stylesheet
 */
function brb_add_editor_styles() {
    // Enqueue editor styles and fonts.
    add_editor_style(get_template_directory_uri() . '/inc/editor.css');
}

add_action('admin_init', 'brb_add_editor_styles');

/**
 * Load fonts
 */
if (!function_exists('italiawp3_theme_fonts')) :

    function italiawp3_theme_fonts() {
        wp_enqueue_style('italiawp3_fonts', get_template_directory_uri() . '/inc/fonts.css', array());
    }

endif;

/*
 * I font vanno serviti anche a chi visita il sito, non solo a chi lo amministra.
 *
 * Fino alla 1.6.1 fonts.css era accodato al solo backend: bootstrap-italia 2.x non
 * dichiara alcun @font-face per Titillium Web, Lora e Roboto Mono (il suo unico
 * @font-face e' quello di VideoJS, in base64) e il tema non carica Google Fonts. Il
 * risultato era che un visitatore senza quei font installati nel sistema vedeva i
 * fallback — Tahoma al posto di Titillium, Georgia al posto di Lora — mentre in
 * bacheca la tipografia era quella giusta. Per un tema del Design System AgID, dove
 * Titillium Web fa parte dell'identita' visiva, non e' un dettaglio estetico.
 *
 * Il foglio contiene solo @font-face: non partecipa alla cascata dei ~380 !important
 * del tema e non ha vincoli di ordine rispetto a bootstrap-italia. Costa 192 KB di
 * woff2 a chi non ha i font, e nulla a chi li ha: fonts.css dichiara local() prima
 * degli url() e font-display:swap su tutti i blocchi.
 */
add_action('admin_enqueue_scripts', 'italiawp3_theme_fonts', 10);
add_action('wp_enqueue_scripts', 'italiawp3_theme_fonts', 10);

/**
 * Enqueue main style
 */
if (!function_exists('italiawp3_theme_style')) :

    function italiawp3_theme_style() {

        // main style
        wp_enqueue_style('italiawp3-style', get_stylesheet_uri(), array());

        // theme style
        wp_enqueue_style('italiawp3-bootstrap-italia.min', get_template_directory_uri() . '/static/css/bootstrap-italia.min.css', array(), italiawp3_BOOTSTRAP_ITALIA_VERSION);

        // Layer di compatibilita' con la resa di bootstrap-italia 1.6.4.
        // DEVE stare subito dopo bootstrap-italia e PRIMA dei fogli del tema: i ~380
        // !important di tema/interne/sezioni/home/adjustments sono scritti contro
        // l'output della 1.x, e questo foglio glielo ripristina. Vedi il file per il
        // dettaglio dei blocchi e il motivo per cui non usa !important.
        wp_enqueue_style('italiawp3-bi1-compat', get_template_directory_uri() . '/static/css/bi1-compat.css', array('italiawp3-bootstrap-italia.min'), italiawp3_BOOTSTRAP_ITALIA_VERSION);

        wp_enqueue_style('italiawp3-owl.carousel.min', get_template_directory_uri() . '/static/css/owl.carousel.min.css', array());
        wp_enqueue_style('italiawp3-owl.theme.default.min', get_template_directory_uri() . '/static/css/owl.theme.default.min.css', array());
        wp_enqueue_style('italiawp3-home', get_template_directory_uri() . '/static/css/home.css', array());
        wp_enqueue_style('italiawp3-sezioni', get_template_directory_uri() . '/static/css/sezioni.css', array());
        wp_enqueue_style('italiawp3-interne', get_template_directory_uri() . '/static/css/interne.css', array());
        wp_enqueue_style('italiawp3-tema', get_template_directory_uri() . '/static/css/tema.css', array());

        // vandors style
        wp_enqueue_style('italiawp3-magnific-popup/magnific-popup', get_template_directory_uri() . '/inc/magnific-popup/magnific-popup.css', array());
        wp_enqueue_style('italiawp3-gutenberg', get_template_directory_uri() . '/inc/gutenberg.css', array());
        wp_enqueue_style('italiawp3_adjustments_css', get_template_directory_uri() . '/inc/adjustments.css', array());

        // Il CSS della mappa e' autoconsistente e non viene sovrascritto dal tema,
        // quindi puo' essere accodato a richiesta senza alterare la cascata.
        wp_register_style('italiawp3-leaflet', get_template_directory_uri() . '/static/css/leaflet.css', array());
    }

endif;
add_action('wp_enqueue_scripts', 'italiawp3_theme_style', 11);

/**
 * Load scripts
 */
if (!function_exists('italiawp3_theme_scripts')) :

    function italiawp3_theme_scripts() {

        // L'handle 'jquery' e' gia' registrato dal core: passare un $src qui verrebbe
        // ignorato da WP_Dependencies::add(). Va solo accodato.
        wp_enqueue_script('jquery');

        wp_enqueue_script('italiawp3-pre-scripts', get_template_directory_uri() . "/inc/pre-scripts.js", array('jquery'), null, true);

        // Librerie caricate solo dove servono: vengono registrate qui e accodate dal
        // template che genera il markup relativo (galleria, carosello, mappa).
        // Vedi italiawp3_enqueue_gallery_assets() / italiawp3_enqueue_map_assets().
        wp_register_script('italiawp3-owl.carousel', get_template_directory_uri() . "/static/js/owl.carousel.min.js", array('jquery'), null, true);
        wp_register_script('italiawp3-jquery.magnific-popup', get_template_directory_uri() . "/inc/magnific-popup/jquery.magnific-popup.min.js", array('jquery'), null, true);
        wp_register_script('leaflet', get_template_directory_uri() . "/static/js/leaflet.js", array(), null, true);

        // Italia WP3 footer theme scripts
        //
        // Dalla 2.x si accoda il BUNDLE: include Popper 2 (e le altre dipendenze) e non
        // richiede ne' include jQuery. Popper 1 non serve piu' ed e' stato rimosso.
        wp_enqueue_script('italiawp3-bootstrap-italia', get_template_directory_uri() . "/static/js/bootstrap-italia.bundle.min.js", array(), italiawp3_BOOTSTRAP_ITALIA_VERSION, true);

        // jQuery resta: serve agli script del tema e a owl/magnific, non a bootstrap-italia.
        // Le dipendenze sono dichiarate per fissare l'ordine, che prima si reggeva
        // sull'ordine di chiamata e non era garantito da WP.
        wp_enqueue_script('italiawp3-scripts', get_template_directory_uri() . "/inc/scripts.js", array('jquery', 'italiawp3-bootstrap-italia'), null, true);
        wp_enqueue_script('italiawp3-tema', get_template_directory_uri() . "/static/js/tema.js", array('jquery', 'italiawp3-bootstrap-italia'), null, true);
        wp_localize_script('italiawp3-tema', 'itwp3', array(
            'siteurl' => get_template_directory_uri()
        ));

        // Stringhe che il JS inietta nel markup e che devono finire agli screen reader:
        // vanno tradotte, quindi non possono stare dentro il file .js.
        wp_localize_script('italiawp3-scripts', 'italiawp3_a11y', array(
            'linkEsterno' => __('(opens an external site)', 'italiawp3'),
            'nuovaFinestra' => __('(opens in a new window)', 'italiawp3'),
        ));

        // Risposte annidate ai commenti. Va valutato qui dentro.
        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }

endif;
add_action('wp_enqueue_scripts', 'italiawp3_theme_scripts'); // Add Theme admin scripts

/**
 * Accoda gli script della galleria (carosello + lightbox).
 *
 * Va chiamata dal codice che genera davvero il markup della galleria.
 * Lo shortcode [gallery] puo' comparire in qualunque
 * contenuto. Gli script sono in footer, quindi accodarli durante il rendering del
 * body e' ancora in tempo.
 */
function italiawp3_enqueue_gallery_assets() {
    wp_enqueue_script('italiawp3-owl.carousel');
    wp_enqueue_script('italiawp3-jquery.magnific-popup');
}

/**
 * Accoda gli asset della mappa. Chiamata dal solo ramo di section-map.php che
 * istanzia effettivamente L.map(); il ramo con <iframe> non ha bisogno di Leaflet.
 */
function italiawp3_enqueue_map_assets() {
    wp_enqueue_script('leaflet');
    wp_enqueue_style('italiawp3-leaflet');
}
