<?php
/*
 * Questo file non e' referenziato da nessun template: WordPress carica automaticamente
 * solo searchform.php. E' comunque corretto qui perche' e' incluso nella distribuzione e
 * un tema child potrebbe richiamarlo.
 *
 * Prima conteneva una <label class="sr-only"> senza attributo for e senza alcun campo a
 * cui riferirsi, e un <button type="submit"> con la sola icona SVG, quindi privo di nome
 * accessibile (WCAG 1.3.1, 4.1.2).
 */
$italiawp3_search_sidebar_id = wp_unique_id('italiawp3-search-sidebar-');
?>
<form class="Form" method="get" role="search" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="form-group">
        <label class="sr-only" for="<?php echo esc_attr($italiawp3_search_sidebar_id); ?>"><?php echo __('Search for Topics','italiawp3'); ?></label>
        <input type="text" id="<?php echo esc_attr($italiawp3_search_sidebar_id); ?>" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="<?php echo esc_attr__('Search for Topics','italiawp3'); ?>">
        <button type="submit" class="ico-sufix" aria-label="<?php echo esc_attr__('Search','italiawp3'); ?>">
            <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-search"></use></svg>
        </button>
    </div>
</form>
