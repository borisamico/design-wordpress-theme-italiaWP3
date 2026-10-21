<?php
/*
 * ### LOOP ARCHIVIO CIRCOLARI ###
 *
 */
?>

<section id="sezione-notizie" class="pt64 pb32 bg-grigio">
    <div class="container">

<?php
    $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; ?>
        
        <div class="row">
            <div class="col-md-12">
                <div class="titolosezione">
                    <h2><?php echo __('Circulars','italiawp3'); ?>
                        <?php if($wp_query->max_num_pages != 0) { ?>
                        <br><small><?php echo __('Page','italiawp3'); ?> <?php echo $paged; ?> <?php echo __('of','italiawp3'); ?> <?php echo $wp_query->max_num_pages; ?></small>
                        <?php } ?>
                    </h2>
                </div>
            </div>
        </div>
        <div class="row row-eq-height">

<?php

$i = 0; if (have_posts()) :
    while (have_posts()) : the_post();

    $i++;

    $datapost = get_the_date('j F Y', '', '');
    
    $circolare_id = get_the_ID();
    $circolare_num = get_post_meta($circolare_id,"_numero",TRUE);
    $circolare_anno = get_post_meta($circolare_id,"_anno",TRUE);
    $circolare_gruppi = wp_get_object_terms($circolare_id,'gruppiutenti'); ?>
                    
            <div class="col-md-4 mb32-l">
                
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
                        
                        <h2>
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <?php the_title(); ?>
                            </a>
                        </h2>
                        <p><?php echo get_the_excerpt(); ?></p>
                    </div>
                    
                    <div class="scheda-argomenti scheda-icona-small">
                        <svg class="icon" aria-hidden="true" focusable="false"><use xlink:href="<?php echo esc_url( get_template_directory_uri() ); ?>/static/img/ponmetroca.svg#ca-account_circle"></use></svg>
                        <span class="badge badge-pill badge-argomenti"><?php echo get_the_author(); ?></span>
                    </div>
                    
                    <?php if (!empty($circolare_gruppi)) { ?>
                    <div class="scheda-argomenti">
                        <h2><?php echo __('Groups','italiawp3'); ?></h2>
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

<?php endwhile;
      else : get_template_part('template-parts/error');
      endif; ?>

        </div>
    </div>
</section>

<?php get_template_part('template-parts/pagination');
