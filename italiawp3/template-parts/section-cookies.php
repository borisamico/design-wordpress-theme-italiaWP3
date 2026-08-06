<?php
/*
 * ### SEZIONE COOKIES & PRIVACY ###
 *
 */
?>

<?php if (!get_theme_mod('disactive_section_cookies')) : ?>

<?php
$page_id_cookie_banner = get_option('dettagli-id-cookie');
if (!$page_id_cookie_banner) $page_id_cookie_banner = get_option('dettagli-id-privacy');
?>
    
<?php
/*
 * Niente aria-hidden="true": era statico nel markup e nessuno script lo rimuoveva, quindi
 * quando la barra compariva (bootstrap-italia aggiunge .show) il bottone "Accetto" e il
 * link restavano tabbabili ma invisibili agli screen reader — WCAG 4.1.2.
 * Nascondere e mostrare e' gia' compito di .cookiebar{display:none} / .cookiebar.show,
 * che toglie l'elemento anche dall'albero di accessibilita' e dall'ordine di tabulazione.
 */
?>
<div class="cookiebar hide u-background-80" role="region"
     aria-label="<?php echo esc_attr__('Cookie notice','italiawp3'); ?>">
    <p class="text-white">
        <?php echo __('This site uses technical, analytics and third-party cookies','italiawp3'); ?>.
        <?php echo __('By continuing to browse, you accept the use of cookies','italiawp3'); ?>.<br />
        <button data-bs-accept="cookiebar" class="btn btn-info mr-2 btn-verde">
            <?php echo __('I accept','italiawp3'); ?>
        </button>
        <a href="<?php echo get_permalink($page_id_cookie_banner); ?>" class="btn btn-outline-info btn-trasp"><?php echo __('Cookie policy','italiawp3'); ?></a>
    </p>
</div>

<?php endif; ?>
