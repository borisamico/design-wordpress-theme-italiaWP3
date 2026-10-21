<?php
/*
 * Utilizzare i Widget per le 4 colonne e la pagina "Dettagli" del backend per i recapiti e url mappa
 *
 */
?>

    </main>
    
    <footer id="footer" class="u-background-80">
        <div class="container">
            <section>
                <div class="row clearfix">
                    <div class="col-sm-12 intestazione">
                        <div class="logoimg">
                            <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>">
                                <?php
                                $custom_logo_id = get_theme_mod('custom_logo');
                                $custom_logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                                if (has_custom_logo()) {
                                    $custom_logo = esc_url($custom_logo[0]);
                                } else {
                                    $custom_logo = get_stylesheet_directory_uri() . '/images/stemma-default.png';
                                } ?>
                                <img class="custom-logo" alt="<?php bloginfo('name'); ?>" src="<?php echo $custom_logo; ?>">
                            </a>
                        </div>
                        <div class="logotxt">
                            <h2>
                                <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php bloginfo('name'); ?>">
                                    <?php bloginfo('name'); ?>
                                </a>
                            </h2>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <?php   if (is_active_sidebar('footer-colonne')) { dynamic_sidebar('footer-colonne'); } ?>
                </div>
            </section>
            <section>
                <div class="row">
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <h2><?php echo __('Contacts','italiawp3'); ?></h2>
                        <ul class="footer-list clearfix">
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-pa"></use></svg>
                                <?php echo __('Address','italiawp3'); ?>
                                <br>
                                <span><?php echo esc_html(get_option('dettagli-indirizzo')); ?><br>
                                <?php echo esc_html(get_option('dettagli-cap')); ?>, <?php echo esc_html(get_option('dettagli-citta')); ?></span>
                            </li>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-telephone"></use></svg>
                                <?php echo __('Phone','italiawp3'); ?><br>
                                <a href="tel:+39<?php echo esc_attr(get_option('dettagli-telefono')); ?>" title="<?php echo __('Phone','italiawp3'); ?>">
                                    (+39) <?php echo esc_html(get_option('dettagli-telefono')); ?>
                                </a>
                            </li>
                            <?php if(get_option('dettagli-fax')!=""): ?>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-print"></use></svg>
                                <?php echo __('Fax','italiawp3'); ?><br>
                                <a href="tel:+39<?php echo esc_attr(get_option('dettagli-fax')); ?>" title="<?php echo __('Fax','italiawp3'); ?>">
                                    (+39) <?php echo esc_html(get_option('dettagli-fax')); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <h2><?php echo __('Information','italiawp3'); ?></h2>
                        <ul class="footer-list clearfix">
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-check-circle"></use></svg>
                                <?php echo __('Tax code / VAT number','italiawp3'); ?><br>
                                <span><?php echo esc_html(get_option('dettagli-cfpiva')); ?></span>
                            </li>
                            <?php if(get_option('dettagli-codunivoco')!=""): ?>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-exchange-circle"></use></svg>
                                <?php echo __('Unique Code','italiawp3'); ?><br>
                                <span><?php echo esc_html(get_option('dettagli-codunivoco')); ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if(get_option('dettagli-iban')!=""): ?>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-card"></use></svg>
                                <?php echo __('IBAN','italiawp3'); ?><br>
                                <span><?php echo esc_html(get_option('dettagli-iban')); ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <div class="col-12 col-md-6 col-lg-3">
                        <h2><?php echo __('Email contacts','italiawp3'); ?></h2>
                        <ul class="footer-list clearfix">
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-mail"></use></svg>
                                <?php echo __('PEC','italiawp3'); ?><br>
                                <a href="mailto:<?php echo esc_attr(get_option('dettagli-pec')); ?>" title="<?php echo __('Certified mail','italiawp3'); ?>">
                                    <?php echo esc_html(get_option('dettagli-pec')); ?>
                                </a>
                            </li>
                            <?php if(get_option('dettagli-email')!=""): ?>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-mail"></use></svg>
                                <?php echo __('Email','italiawp3'); ?><br>
                                <a href="mailto:<?php echo esc_attr(get_option('dettagli-email')); ?>" title="<?php echo __('Email','italiawp3'); ?>">
                                    <?php echo esc_html(get_option('dettagli-email')); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if(get_option('dettagli-email2')!=""): ?>
                            <li>
                                <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/svg/sprite.svg#it-mail"></use></svg>
                                <?php echo __('Email','italiawp3'); ?><br>
                                <a href="mailto:<?php echo esc_attr(get_option('dettagli-email2')); ?>" title="<?php echo __('Email','italiawp3'); ?>">
                                    <?php echo esc_html(get_option('dettagli-email2')); ?>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="col-12 col-md-6 col-lg-3">
                        <h2><?php echo __('Follow us on','italiawp3'); ?></h2>
                        
                    <?php get_template_part('template-parts/social-links', null, array(
                        'ul_class' => 'list-inline text-left social',
                    )); ?>
                    </div>
                </div>
            </section>

            <section class="postFooter clearfix">
                <h2 class="sr-only"><?php echo __('Useful Links Section','italiawp3'); ?></h2>
                
                <?php if(get_option('dettagli-id-privacy')!=""): ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('dettagli-id-privacy'))); ?>" data-element="privacy-policy-link" title="<?php echo __('Privacy policy','italiawp3'); ?>"><?php echo __('Privacy','italiawp3'); ?></a> |
                <?php endif; ?>
                <?php if(get_option('dettagli-id-cookie')!=""): ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('dettagli-id-cookie'))); ?>" data-element="cookie-policy-link" title="<?php echo __('Cookie policy','italiawp3'); ?>"><?php echo __('Cookie policy','italiawp3'); ?></a> |
                <?php endif; ?>
                <?php if(get_option('dettagli-id-notelegali')!=""): ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('dettagli-id-notelegali'))); ?>" data-element="legal-notes-link" title="<?php echo __('Legal notices','italiawp3'); ?>"><?php echo __('Legal notices','italiawp3'); ?></a> |
                <?php endif; ?>
                <?php if(get_option('dettagli-id-contatti')!=""): ?>
                    <a href="<?php echo esc_url(get_permalink(get_option('dettagli-id-contatti'))); ?>" data-element="contacts-link" title="<?php echo __('Contacts','italiawp3'); ?>"><?php echo __('Contacts','italiawp3'); ?></a> |
                <?php endif; ?>
                <?php if(get_option('dettagli-link-accessibilita')!=""): ?>
                    <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url(get_option('dettagli-link-accessibilita') ); ?>" data-element="accessibility-link" title="<?php echo __('Accessibility','italiawp3'); ?>"><?php echo __('Accessibility','italiawp3'); ?></a> |
                <?php endif; ?>
                <?php echo __('Made with','italiawp3'); ?> <a target="_blank" rel="noopener noreferrer" href="https://it.wordpress.org">WordPress</a> |
                
                <!-- Per favore, non rimuoverlo! -->
                <?php $main_theme = wp_get_theme(get_template()); ?>
                <?php echo __('Graphic theme','italiawp3'); ?> <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($main_theme->get('ThemeURI')); ?>"><?php echo esc_html($main_theme->get('Name')); ?></a> |
                <?php echo __('Based on the','italiawp3'); ?> <a target="_blank" rel="noopener noreferrer" href="https://italia.github.io/design-comuni-prototipi/"><?php echo __('AgID Prototype for PA sites','italiawp3'); ?></a>
                <!-- Grazie :) -->

                <br>
                <?php if(is_active_sidebar('credits')) dynamic_sidebar('credits'); ?>

            </section>
        </div>
    </footer>
    
</div>

<?php
/*
 * Era un <div> con solo l'attributo title: non focalizzabile, non azionabile da tastiera
 * (WCAG 2.1.1) e senza nome accessibile, perche' title su un div non ne produce uno.
 * La regola #topcontrol:focus in tema.css non poteva quindi attivarsi mai.
 * Resta fuori dall'ordine di tabulazione finche' e' nascosto: #topcontrol e'
 * visibility:hidden fino alla classe .visualizza (tema.css).
 */
?>
<button type="button" id="topcontrol" class="topcontrol u-background-80">
  <svg class="icon" aria-hidden="true" focusable="false">
    <use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/bootstrap-italia.svg#it-collapse"></use>
  </svg>
  <span class="sr-only"><?php echo __('Go up','italiawp3'); ?></span>
</button>

<script>window.__PUBLIC_PATH__ = '<?php echo bloginfo('template_directory'); ?>/static/fonts';</script>
<?php wp_footer(); ?>

</body>
</html>
