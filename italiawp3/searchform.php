<?php
/*
 * Il campo aveva solo title e placeholder: nessun <label for>, nessun id, quindi nessun
 * nome accessibile stabile (WCAG 1.3.1, 3.3.2, 4.1.2). Il placeholder non e' un'etichetta:
 * sparisce appena si digita e non tutti gli AT lo espongono.
 *
 * get_search_form() puo' essere chiamata piu' volte nella stessa pagina (header, sidebar,
 * widget): wp_unique_id() evita id duplicati, che invaliderebbero l'associazione.
 * Nota: una "static" a livello di file non funzionerebbe, perche' get_search_form()
 * include il template dentro una funzione e la variabile non sopravvive alle chiamate.
 */
$italiawp3_search_id = wp_unique_id('italiawp3-search-field-');
?>
<div class="cerca">
<form class="Form" method="get" role="search" data-element="search-modal" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="sr-only" for="<?php echo esc_attr($italiawp3_search_id); ?>"><?php echo __('Search','italiawp3'); ?></label>
    <input type="text" id="<?php echo esc_attr($italiawp3_search_id); ?>" value="" name="s" placeholder="<?php echo esc_attr__('Search','italiawp3'); ?>" required>
    <button class="btn btn-default btn-cerca pull-right" name="submit" aria-label="<?php echo esc_attr__('Search','italiawp3'); ?>">
        <svg class="icon" aria-hidden="true" focusable="false">
        <use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-search"></use>
        </svg>
    </button>
</form>
</div>
