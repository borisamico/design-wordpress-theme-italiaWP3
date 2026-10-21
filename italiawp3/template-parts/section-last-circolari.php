<?php
/*
 * ### SEZIONE ULTIME CIRCOLARI ###
 * Mostra le ultime 6 circolari (C.P.T.) caricate.
 *
 */
?>

<section id="sezione-circolari" class="section">
    <div class="container">
        <div class="mt-88n">
            <div class="row">
                <div class="col-12">
                    <div class="titolosezione">
                        <h3><?php echo __('Latest circulars','italiawp3'); ?></h3>
                    </div>
                </div>
            </div>
            <div class="row row-eq-height">
                
<?php

    $args = array(
        'posts_per_page' => 6,
        'post_type' => 'circolari',
        'no_found_rows' => true,
    );

$the_query = new WP_Query($args);
if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post();

    $datapost = get_the_date('j F Y', '', '');

    $circolare_id = get_the_ID();
    $circolare_num = get_post_meta($circolare_id,"_numero",TRUE);
    $circolare_anno = get_post_meta($circolare_id,"_anno",TRUE);
    $circolare_gruppi = wp_get_object_terms($circolare_id,'gruppiutenti'); ?>
                
                <div class="col-md-4">
                    
                    <article class="scheda scheda-round scheda-news card">
                        
                        <div class="scheda-icona-small">
                        <?php if(is_sticky( $post->ID )) { ?>
                            <div class="flag-icon"></div>
                        <?php } ?>
                            
                            <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-today"></use></svg>
                            <?php echo $datapost; ?>
                            <?php echo '- '.__('No.','italiawp3').' '.$circolare_num.__(' of ','italiawp3').$circolare_anno; ?>
                        </div>

                        <div class="scheda-testo scheda-testo-nofoto">
                        
                            <h3>
                                <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h3>

                            <p><?php echo get_the_excerpt(); ?></p>
                        </div>
                        
                        <div class="scheda-argomenti scheda-icona-small">
                            <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-account_circle"></use></svg>
                            <span class="badge badge-pill badge-argomenti"><?php echo get_the_author(); ?></span>
                        </div>
                        
                        <?php if (!empty($circolare_gruppi)) { ?>
                        <div class="scheda-argomenti">
                            <h3><?php echo __('Groups','italiawp3'); ?></h3>
                            <div class="argomenti-sezione-elenco">
                                <?php
                                    foreach ($circolare_gruppi as $gruppo) {
                                        echo '<span class="badge badge-pill badge-argomenti">' . esc_html($gruppo->name) . '</span>';
                                    }
                                 ?>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <div class="scheda-footer">
                            <a href="<?php the_permalink(); ?>" class="tutte"
                           aria-label="<?php echo esc_attr__('Read more','italiawp3'); ?> - <?php echo esc_attr__('Go to the page','italiawp3'); ?>: <?php echo esc_attr(get_the_title()); ?>">
                                <?php echo __('Read more','italiawp3'); ?>
                                <svg class="icon" aria-hidden="true" focusable="false">
                                    <use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-arrow_forward"></use>
                                </svg>
                            </a>
                        </div>
                    </article>
                </div>
                
<?php
    
    endwhile;  endif;
    wp_reset_postdata();
    
    ?>
            </div>
            
            <div class="row mt32">
                <div class="col-md-12 veditutti">
                    <a href="<?php echo get_post_type_archive_link('circolari'); ?>" title="<?php echo __('Go to the page','italiawp3'); ?>: <?php echo __('Circulars','italiawp3'); ?>" class="btn btn-default btn-verde">
                        <?php echo __('See all','italiawp3'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
