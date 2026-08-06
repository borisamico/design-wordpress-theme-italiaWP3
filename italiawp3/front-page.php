<?php

get_header();

/*
 * La homepage non aveva alcun <h1>: il disegno non prevede un titolo di pagina e
 * l'unico h1 presente era quello del logo, che ora e' un <p> (menu.php).
 * Ogni pagina deve averne uno (WCAG 1.3.1, 2.4.6): qui e' riservato agli screen
 * reader, cosi' la struttura e' corretta senza toccare la grafica.
 */
printf(
    '<h1 class="sr-only">%s</h1>',
    esc_html(get_bloginfo('name'))
);

get_template_part('template-parts/notification');

if (get_theme_mod('active_section_last_one_news')):
    get_template_part('template-parts/section-last-one-news');
else:
    get_template_part('template-parts/section-hero');
endif;

if (get_theme_mod('active_section_last_news')):
    get_template_part('template-parts/section-last-news');
endif;

/* Inizio Sezione Ultime circolari (se è installato il plugin "Gestione Circolari") */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active('gestione-circolari/GestioneCircolari.php') && get_theme_mod('active_section_circolari') ):
    get_template_part('template-parts/section-last-circolari');
endif;
/* Fine Sezione Ultime circolari */

if( get_the_content() != "" ): ?>
<section id="home-contenuto" class="section">
    <div class="container">
        <div class="widget">
            <div class="row">
                <div class="col-md-12">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif;

if (get_theme_mod('active_section_services')):
    get_template_part('template-parts/section-services');
endif;

if (get_theme_mod('active_section_galleries')):
    get_template_part('template-parts/section-gallery-carousel');
endif;

get_sidebar();
get_footer();
