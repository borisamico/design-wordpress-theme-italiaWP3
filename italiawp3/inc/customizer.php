<?php

// Serve per l'avviso di contrasto sul colore principale. functions.php richiede
// customizer.php prima di style.php, che e' l'altro punto in cui colors.php viene
// incluso: senza questa riga le funzioni di contrasto non sarebbero garantite qui.
include_once(__DIR__ . '/colors.php');

// Set the default favicon if none set into customizer
if( get_option( 'site_icon', false ) == false ) {
    add_action( 'wp_head', 'italiawp3_default_favicon' );
    add_action( 'admin_head', 'italiawp3_default_favicon' );
}
function italiawp3_default_favicon() {
    //code of the favicon logic
    printf('<link rel="icon" href="%s" type="image/png" sizes="32x32" />', get_template_directory_uri() . '/favicon.png');
}

/*
 * Avviso di contrasto per il colore principale.
 *
 * Il tema deriva tutta la tavolozza da questo singolo colore. Se la coppia
 * testo/sfondo che ne risulta non regge le soglie WCAG 2.1 AA, il tema la corregge
 * automaticamente (vedi italiawp3_ensure_contrast in inc/colors.php): il sito resta
 * conforme, ma la resa puo' allontanarsi dal colore scelto. Qui l'utente lo scopre
 * prima, non davanti al risultato.
 *
 * Il testo e' rigenerato al volo anche via JS (static/js/customizer-contrasto.js)
 * mentre si muove il selettore, perche' la descrizione del controllo e' statica e
 * rifletterebbe solo il colore gia' salvato.
 */
function italiawp3_main_color_description() {
    $colore = get_option('italiawp3_main_color', '#003882');
    $ratio  = getContrastRatio($colore, '#ffffff');

    $stato = ($ratio >= italiawp3_CONTRAST_TEXT)
        ? sprintf(
            'Contrasto su fondo bianco: %s:1. Sufficiente per il testo (minimo di legge 4.5:1).',
            number_format_i18n($ratio, 2)
        )
        : sprintf(
            'Contrasto su fondo bianco: %s:1, sotto il minimo di legge di 4.5:1. Per i testi il tema userà automaticamente una variante più scura di questo colore, quindi link e titoli appariranno diversi dalla tinta scelta.',
            number_format_i18n($ratio, 2)
        );

    return '<span class="italiawp3-contrasto-avviso" data-soglia="' . esc_attr(italiawp3_CONTRAST_TEXT) . '">'
         . esc_html($stato)
         . '</span>';
}

/*
 * Aggiorna l'avviso di contrasto mentre l'utente muove il selettore di colore.
 */
function italiawp3_customizer_controls_assets() {
    wp_enqueue_script(
        'italiawp3-customizer-contrasto',
        get_template_directory_uri() . '/static/js/customizer-contrasto.js',
        array('jquery', 'customize-controls'),
        italiawp3_BOOTSTRAP_ITALIA_VERSION,
        true
    );
}
add_action('customize_controls_enqueue_scripts', 'italiawp3_customizer_controls_assets');

function italiawp3_customize_register($wp_customize) {

	/* Sezione "Sezioni Homepage" nel customizer */
    $wp_customize->add_section('site_settings', array(
        'title' => 'Sito & Homepage',
        'priority' => 1,
    ));

	/* Settings e i controls per le sezioni */
    
	/* Ultimo articolo */
    $wp_customize->add_setting('active_section_last_one_news', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_last_one_news', array(
        'label' => 'Ultimo articolo (se ATTIVO) o Hero (se DISATTIVO)',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_last_one_news'
    ));

	/* Ultimi articoli */
    $wp_customize->add_setting('active_section_last_news', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_last_news', array(
        'label' => 'Ultimi articoli',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_last_news'
    ));
    
	/* Ultime circolari (se è installato il plugin) */
    $wp_customize->add_setting('active_section_circolari', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_circolari', array(
        'label' => 'Ultime circolari (se è installato il plugin "Gestione Circolari")',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_circolari'
    ));
    
	/* Lista Altre Categorie (home) */
    $wp_customize->add_setting('active_altre_categorie_home', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_altre_categorie_home', array(
        'label' => 'Lista Altre Categorie (home)',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_altre_categorie_home'
    ));
    
	/* Lista Altri Argomenti (home) */
    $wp_customize->add_setting('active_altri_argomenti_home', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_altri_argomenti_home', array(
        'label' => 'Lista Altri Argomenti (home)',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_altri_argomenti_home'
    ));

	/* Servizi */
    $wp_customize->add_setting('active_section_services', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_services', array(
        'label' => 'Servizi',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_services'
    ));
    
	/* Gallerie Fotografiche */
    $wp_customize->add_setting('active_section_galleries', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_galleries', array(
        'label' => 'Gallerie fotografiche',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_galleries'
    ));
    
	/* Map */
    $wp_customize->add_setting('active_section_map', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_section_map', array(
        'label' => 'Mappa',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'active_section_map'
    ));
    
	/* Cookies & Privacy */
    $wp_customize->add_setting('disactive_section_cookies', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('disactive_section_cookies', array(
        'label' => 'Disattiva barra Cookies & Privacy di default (preattivata)',
        'type' => 'checkbox', 'section' => 'site_settings', 'settings' => 'disactive_section_cookies'
    ));

	/* Sezione "Preferenze Pagine" nel customizer */
    $wp_customize->add_section('pages_settings', array(
        'title' => 'Preferenze Pagine & Articoli',
        'priority' => 2,
    ));
    
	/* Immagine in evidenza di default per gli articoli */
    $wp_customize->add_setting('active_immagine_evidenza_default', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_immagine_evidenza_default', array(
        'label' => 'Attiva l\'immagine in evidenza di default (per gli articoli che non ne hanno una)',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'active_immagine_evidenza_default'
    ));
    
	/* Immagine di default articoli */
    $wp_customize->add_setting('immagine_evidenza_default', array(
        'transport' => 'refresh',
        'capability' => 'edit_theme_options',
        'default' => esc_url( get_template_directory_uri() ).'/images/400x220.png'
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'immagine_evidenza_default', array(
        'label' => 'Immagine di default articoli',
        'section' => 'pages_settings',
        'settings' => 'immagine_evidenza_default'
    )));
    
	/* Lista Allegati in Contenuto o Sidebar */
    $wp_customize->add_setting('active_allegati_contenuto', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('active_allegati_contenuto', array(
        'label' => 'Lista allegati in Contenuto (in Sidebar se disattivato)',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'active_allegati_contenuto'
    ));

	/* Cornici per le Immagini singole in pagine e articoli */
    $wp_customize->add_setting('disactive_cornici_immagini', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('disactive_cornici_immagini', array(
        'label' => 'Disattiva le cornici per le immagini singole (non gallerie) in pagine e articoli',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'disactive_cornici_immagini'
    ));
    
	/* Sunto automatico sotto al titolo per le pagine */
    $wp_customize->add_setting('disactive_sunto_pagine', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('disactive_sunto_pagine', array(
        'label' => 'Disattiva la visualizzazione del sunto automatico (sotto al titolo) nella pagine',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'disactive_sunto_pagine'
    ));
    
	/* Sunto automatico sotto al titolo per gli articoli */
    $wp_customize->add_setting('disactive_sunto_articoli', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('disactive_sunto_articoli', array(
        'label' => 'Disattiva la visualizzazione del sunto automatico (sotto al titolo) negli articoli',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'disactive_sunto_articoli'
    ));
    
        /* Sunto automatico sotto al titolo per i Custon Type Post */
    $wp_customize->add_setting('disactive_sunto_ctp', array(
        'default' => false, 'capability' => 'edit_theme_options', 'sanitize_callback' => 'italiawp3_sanitize_checkbox'
    ));
    $wp_customize->add_control('disactive_sunto_ctp', array(
        'label' => 'Disattiva la visualizzazione del sunto automatico (sotto al titolo) nei Custom Type Post',
        'type' => 'checkbox', 'section' => 'pages_settings', 'settings' => 'disactive_sunto_ctp'
    ));
    
	/* Colore Principale */
    /*
     * Il setting era l'unico senza sanitize_callback: accettava qualsiasi stringa.
     * La descrizione spiega che cosa succede quando il colore scelto non regge le
     * soglie di contrasto di legge — il tema lo corregge in automatico
     * (italiawp3_ensure_contrast in inc/colors.php), quindi la resa puo' differire dal
     * colore selezionato. Senza questa nota la correzione sembrerebbe un bug.
     */
    $wp_customize->add_setting('italiawp3_main_color', array(
        'default' => '#003882',
        'type' => 'option',
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_hex_color'
    ));
    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'italiawp3_main_color', array(
                'label' => 'Colore principale',
                'description' => italiawp3_main_color_description(),
                'section' => 'colors',
                'settings' => 'italiawp3_main_color'
            )
    ));
}
add_action('customize_register', 'italiawp3_customize_register');

function italiawp3_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
