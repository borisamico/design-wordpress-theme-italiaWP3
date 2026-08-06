<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge; <?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <?php if(get_option('custom-meta-keywords')!=""): ?>
    <meta name="keywords" content="<?php echo get_option('custom-meta-keywords'); ?>">
    <?php endif; ?>
    
    <?php if(get_option('custom-meta-description')!=""): ?>
    <meta name="description" content="<?php echo get_option('custom-meta-description'); ?>">
    <?php else: ?>
    <meta name="description" content="<?php echo get_bloginfo('description'); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>

    <?php
    /*
     * Google Analytics. L'ID va impostato in Dettagli > Measurement ID GA4; se il campo
     * e' vuoto non viene caricato alcuno script e non parte alcuna richiesta esterna.
     */
    $italiawp3_ga_id = trim((string) get_option('dettagli-ga-id'));
    if ($italiawp3_ga_id !== ''):
    ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr(rawurlencode($italiawp3_ga_id)); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js($italiawp3_ga_id); ?>', {'anonymize_ip': true});
    </script>
    <?php endif; ?>

</head>

<body class="t-Pac">

<?php get_template_part('template-parts/section-cookies'); ?>
    
<div class="body_wrapper push_container clearfix" id="page_top">
    <?php
    /*
     * Skiplink (WCAG 2.4.1). Non usano piu' .sr-only: quella classe e' permanente e li
     * rendeva irraggiungibili anche al focus. Lo stile e' in static/css/tema.css
     * (sezione SKIPLINK): fuori schermo di default, in evidenza al focus.
     *
     * Il link al menu esiste in due varianti perche' il tema ha due navigazioni
     * distinte: #menup e' l'off-canvas mobile (nascosto sopra i 991px), #main-navigation
     * e' la barra desktop dentro #sub_nav (.hidden-xs, nascosta sotto i 992px).
     * Puntarne una sola lasciava meta' degli utenti con uno skiplink verso il nulla.
     * La variante non pertinente e' display:none, quindi fuori dall'ordine di tabulazione.
     *
     * Gli accesskey 2/3/4 sono stati rimossi: confliggono con le scorciatoie di browser
     * e screen reader e non erano comunicati da nessuna parte.
     */
    ?>
    <div class="skiplink">
        <ul>
            <li>
                <a href="#main_container"><?php echo __('Go to content','italiawp3'); ?></a>
            </li>
            <li class="skiplink-mobile">
                <a href="#menup"><?php echo __('Go to the navigation menu','italiawp3'); ?></a>
            </li>
            <li class="skiplink-desktop">
                <a href="#main-navigation"><?php echo __('Go to the navigation menu','italiawp3'); ?></a>
            </li>
            <li><a href="#footer"><?php echo __('Go to the footer','italiawp3'); ?></a></li>
        </ul>
    </div>
        
    <header id="mainheader" class="u-background-50" data-element="header">
    <?php get_template_part('menu'); ?>
    </header>

    <?php // tabindex="-1": senza, in diversi browser lo skiplink sposta la vista ma non il focus. ?>
    <main id="main_container" tabindex="-1">
        
    <?php if(!is_attachment()) italiawp3_create_breadcrumbs(); ?>
