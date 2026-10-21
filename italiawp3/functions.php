<?php

if (!function_exists('italiawp3_theme_setup')) :

    function italiawp3_theme_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         */
        load_theme_textdomain('italiawp3', get_template_directory() . '/languages');

        /*
         * WordPress define content width
         * https://codex.wordpress.org/Content_Width
         */
        if (!isset($content_width))
            $content_width = 1200;

        /*
         * Enable support for Post Thumbnails on posts and pages.
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        add_image_size('post-thumbnails', 512, 512, array('center', 'center'));
        add_image_size('news-image', 400, 220, array('center', 'center'));

        // This theme uses wp_nav_menu() in Primary Navigation.
        register_nav_menus(array(
            'menu-principale' => 'Menu Principale',
            'menu-alto' => 'Menu in alto',
            'box-servizi-1' => esc_html__('Box Servizi 1', 'italiawp3'),
            'box-servizi-2' => esc_html__('Box Servizi 2', 'italiawp3'),
            'box-servizi-3' => esc_html__('Box Servizi 3', 'italiawp3'),
            'box-servizi-4' => esc_html__('Box Servizi 4', 'italiawp3'),
            'box-servizi-5' => esc_html__('Box Servizi 5', 'italiawp3'),
            'box-servizi-6' => esc_html__('Box Servizi 6', 'italiawp3'),
            'box-servizi-7' => esc_html__('Box Servizi 7', 'italiawp3'),
            'box-servizi-8' => esc_html__('Box Servizi 8', 'italiawp3'),
            'box-servizi-9' => esc_html__('Box Servizi 9', 'italiawp3')
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
                'html5',
                array(
                    'search-form',
                    'comment-form',
                    'gallery',
                    'caption',
                )
        );

        // Add support for core custom logo, header text color, website background.
        add_theme_support('custom-logo', array(
            'height' => 512,
            'width' => 512,
            'flex-height' => true,
            'flex-width' => true,
            'header-text' => array('site-title', 'site-description'),
        ));

        add_theme_support('custom-header', apply_filters('italiawp3_custom_header_args', array(
            'default-image' => get_template_directory_uri() . '/images/2000x500.png',
            'default-text-color' => '000000',
            'header-text' => false,
            'width' => 2000,
            'height' => 500,
            'flex-height' => true,
            'uploads' => true
        )));

        add_theme_support('custom-background', array(
            'default-color' => "#ffffff",
            'default-image' => '',
            'default-preset' => 'default', // 'default', 'fill', 'fit', 'repeat', 'custom'
            'default-position-x' => 'left', // 'left', 'center', 'right'
            'default-position-y' => 'top', // 'top', 'center', 'bottom'
            'default-size' => 'auto', // 'auto', 'contain', 'cover'
            'default-repeat' => 'repeat', // 'repeat-x', 'repeat-y', 'repeat', 'no-repeat'
            'default-attachment' => 'scroll', // 'scroll', 'fixed'
            'admin-head-callback' => '',
            'admin-preview-callback' => '',
        ));

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        // Add excerpt support to pages
        add_post_type_support('page', 'excerpt');

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        // Add support for Block Styles.
        add_theme_support('wp-block-styles');

        // Add support for full and wide align images.
        add_theme_support('align-wide');

        // Add support for editor styles.
        add_theme_support('editor-styles');

        // Enqueue editor fonts.
        add_editor_style(get_template_directory_uri().'/inc/fonts.css');
        
        // Add support for responsive embedded content.
        add_theme_support('responsive-embeds');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        // Aggiungo dei colori da utilizzare con l'editor
        add_theme_support('editor-color-palette',
                array(
                    array(
                        'name' => __('Black', 'italiawp3'),
                        'slug' => 'colore-nero',
                        'color' => get_option('italiawp3_colore_nero'),
                    ),
                    array(
                        'name' => __('White', 'italiawp3'),
                        'slug' => 'colore-bianco',
                        'color' => get_option('italiawp3_colore_bianco'),
                    ),
                    array(
                        'name' => __('Primary Color', 'italiawp3'),
                        'slug' => 'colore-primario',
                        'color' => get_option('italiawp3_colore_primario'),
                    ),
                    array(
                        'name' => __('Light Primary Color', 'italiawp3'),
                        'slug' => 'colore-primario-chiaro',
                        'color' => get_option('italiawp3_colore_primario_chiaro'),
                    ),
                    array(
                        'name' => __('Dark Primary Color', 'italiawp3'),
                        'slug' => 'colore-primario-scuro',
                        'color' => get_option('italiawp3_colore_primario_scuro'),
                    ),
                    array(
                        'name' => __('Complementary Color', 'italiawp3'),
                        'slug' => 'colore-complementare',
                        'color' => get_option('italiawp3_colore_complementare'),
                    )
                )
        );
    }

endif;
add_action('after_setup_theme', 'italiawp3_theme_setup');

add_action( 'pre_get_posts', function( $q ) {
  if (array_key_exists('force_no_results', $q->query) && $q->query['force_no_results']) {
    $q->query = $q->query_vars = array();
    $added = array();
    $filters = array(
      'where', 'where_paged', 'join', 'join_paged', 'groupby', 'orderby', 'distinct',
      'limits', 'fields', 'request', 'clauses', 'where_request', 'groupby_request',
      'join_request', 'orderby_request', 'distinct_request','fields_request',
      'limits_request', 'clauses_request'
    );
    // remove all possible interfering filter and save for later restore
    foreach ( $filters as $f ) {
      if ( isset($GLOBALS['wp_filter']["posts_{$f}"]) ) {
        $added["posts_{$f}"] = $GLOBALS['wp_filter']["posts_{$f}"];
        unset($GLOBALS['wp_filter']["posts_{$f}"]);
      }
    }
    // be sure filters are not suppressed
    $q->set( 'suppress_filters', FALSE );
    $done = 0;
    // use a filter to return a non-sense request
    add_filter('posts_request', function( $r ) use( &$done ) {
      if ( $done === 0 ) { $done = 1;
        $r = "SELECT ID FROM {$GLOBALS['wpdb']->posts} WHERE 0 = 1";
      }
      return $r;
    });
    // restore any filter that was added and we removed
    add_filter('posts_results', function( $posts ) use( &$done, $added ) {
      if ( $done === 1 ) { $done = 2;
        foreach ( $added as $hook => $filters ) {
          $GLOBALS['wp_filter'][$hook] = $filters;
        }
      }
      return $posts;
    });
  }
}, PHP_INT_MAX );

// Widgets
function italiawp3_widgets_init() {
    register_sidebar(array(
        'name' => 'Footer Colonne',
        'id' => 'footer-colonne',
        'description' => 'Colonne del Footer',
        'before_widget' => '<div id="%1$s" class="footer-colonne col-12 col-md-6 col-lg-3 %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h2>',
        'after_title' => '</h2>'
    ));
    register_sidebar(array(
        'name' => 'Sidebar Pagine',
        'id' => 'sidebar-pagine',
        'description' => 'Sidebar nelle Pagine',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h2 class="dropdown">',
        'after_title' => '</h2><div class="menu-separatore"><div class="bg-oro"></div></div>',
        'class' => ''
    ));
    register_sidebar(array(
        'name' => 'Sidebar Articoli',
        'id' => 'sidebar-articoli',
        'description' => 'Sidebar negli Articoli',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<h2 class="dropdown">',
        'after_title' => '</h2><div class="menu-separatore"><div class="bg-oro"></div></div>',
        'class' => ''
    ));
    register_sidebar(array(
        'name' => 'Sezione Notifica',
        'id' => 'notification',
        'description' => 'Sezione della notifica',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
        'class' => ''
    ));
    register_sidebar(array(
        'name' => 'Sezione Credits',
        'id' => 'credits',
        'description' => 'Sezione dei credits',
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '',
        'after_title' => '',
        'class' => ''
    ));
}

add_action('widgets_init', 'italiawp3_widgets_init');

// Taxonomies
add_action('init', 'italiawp3_register_post_tag_page');

function italiawp3_register_post_tag_page() {
    register_taxonomy_for_object_type('post_tag', 'page');
}

// Theme customizations
// Customized excerpt length
add_filter('excerpt_length', function ($length) {
    return 300;
}, 999);

// Set a default header
register_default_headers(array(
    '2000x500' => array(
        'url' => get_template_directory_uri() . '/images/2000x500.png',
        'thumbnail_url' => get_template_directory_uri() . '/images/2000x500.png',
        'description' => 'Default header'
    )
));
add_filter('next_posts_link_attributes', 'posts_link_attributes');

// adds the archive metabox
add_action('admin_head-nav-menus.php', 'italiawp3_menu_pt_archive');

function italiawp3_menu_pt_archive() {
    add_meta_box('minimac-metabox-nav-menu-pt', 'Custom Posts Type', 'italiawp3_metabox_menu_pt_archive', 'nav-menus', 'side', 'default');
}

// Inserimento dei tags nelle queries
add_action('pre_get_posts', 'tags_support_query');

function tags_support_query($wp_query) {
    if (is_admin() || !$wp_query->is_main_query()) {
        return;
    }
    if ($wp_query->get('tag')) {
        $wp_query->set('post_type', 'any');
    }
}

// Disattiva il floating degli articoli in evidenza nella lista articoli
add_action('pre_get_posts', 'italiawp3_blog_ignore_sticky');

function italiawp3_blog_ignore_sticky($wp_query) {
    if (is_admin() || !$wp_query->is_main_query()) {
        return;
    }
    if ($wp_query->is_home()) {
        $wp_query->set('ignore_sticky_posts', true);
    }
}

// Require
require get_template_directory() . '/inc/class-wp-bootstrap-navwalker.php';

require get_template_directory() . '/inc/menu-item-custom-fields/menu-item-custom-fields.php';
require get_template_directory() . '/inc/menu-item-custom-fields/doc/menu-item-custom-fields-example.php';

require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/style.php';
require get_template_directory() . '/inc/gallery.php';
require get_template_directory() . '/inc/details.php';

// Controlli di manutenzione: solo backend, nessun costo per chi visita il sito.
// I file dei controlli veri e propri (checks-html, checks-content) si caricano piu'
// tardi, dentro le callback della pagina e delle azioni AJAX: un amministratore che
// sta su un'altra schermata non ne carica una riga.
if (is_admin()) {
    require get_template_directory() . '/inc/version-check.php';
    require get_template_directory() . '/inc/accessibilita/dashboard.php';
}

// Custom Functions
function italiawp3_custom_login_logo() {
    echo '<style type="text/css">';
    echo '.login h1 a { background-image:url(' . esc_url(get_site_icon_url()) . ') !important; }';
    echo '</style>';
}

add_action('login_head', 'italiawp3_custom_login_logo');

function italiawp3_custom_login_url() {
    return home_url();
}

add_filter('login_headerurl', 'italiawp3_custom_login_url');

add_filter('previous_posts_link_attributes', 'posts_link_attributes');

function posts_link_attributes() {
    return 'class="u-color-50"';
}

function italiawp3_create_breadcrumbs() {

    $home = 'Home';
    $before = '<li class="breadcrumb-item active">';
    $after = '</li>';

    if (!is_home() && !is_front_page()) {
        echo '<section id="briciole"><div class="container"><div class="row">';
        echo '<div class="offset-lg-1 col-lg-9 col-md-12">';
        echo '<nav class="breadcrumb-container" aria-label="breadcrumb"><ol class="breadcrumb">';

        global $post;
        $homeLink = get_bloginfo('url');
        echo '<li class="breadcrumb-item"><a href="' . $homeLink . '">' . $home . '</a><span class="separator">/</span></li>';

        if (is_category()) {
            global $wp_query;
            $cat_obj = $wp_query->get_queried_object();
            $thisCat = $cat_obj->term_id;
            $thisCat = get_category($thisCat);
            $parentCat = get_category($thisCat->parent);
            if ($thisCat->parent != 0)
                echo get_category_parents($parentCat, TRUE, ' ');
            echo $before . single_cat_title('', false) . $after;
        } elseif (is_day()) {
            echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a><span class="separator">/</span></li>';
            echo '<li class="breadcrumb-item"><a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a><span class="separator">/</span></li>';
            echo $before . get_the_time('d') . $after;
        } elseif (is_month()) {
            echo '<li class="breadcrumb-item"><a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a><span class="separator">/</span></li>';
            echo $before . get_the_time('F') . $after;
        } elseif (is_year()) {
            echo $before . get_the_time('Y') . $after;
        } elseif (is_single() && !is_attachment()) {
            if (get_post_type() != 'post') {
                $post_type = get_post_type_object(get_post_type());
                $slug = $post_type->rewrite;
                echo '<li class="breadcrumb-item"><a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a><span class="separator">/</span></li>';
                echo $before . get_the_title() . $after;
            } else {
                $cat = get_the_category();
                $cat = $cat[0];
                echo '<li class="breadcrumb-item"><a href="' . get_term_link($cat->cat_ID, false) . '">' . $cat->cat_name . '</a><span class="separator">/</span></li>';
                echo $before . get_the_title() . $after;
            }
        } elseif (!is_single() && !is_page() && get_post_type() != 'post' && !is_404() && !is_search()) {
            $post_type = get_post_type_object(get_post_type());
            echo $before . $post_type->labels->singular_name . $after;
        } elseif (is_attachment()) {
            $parent = get_post($post->post_parent);
            $cat = get_the_category($parent->ID);
            $cat = $cat[0];
            echo get_category_parents($cat, TRUE, ' ');
            echo '<li class="breadcrumb-item"><a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a><span class="separator">/</span></li>';
            echo $before . get_the_title() . $after;
        } elseif (is_page() && !$post->post_parent) {
            echo $before . get_the_title() . $after;
        } elseif (is_page() && $post->post_parent) {
            $parent_id = $post->post_parent;
            $breadcrumbs = array();
            while ($parent_id) {
                $page = get_page($parent_id);
                $breadcrumbs[] = '<li class="breadcrumb-item"><a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a><span class="separator">/</span></li>';
                $parent_id = $page->post_parent;
            }
            $breadcrumbs = array_reverse($breadcrumbs);
            foreach ($breadcrumbs as $crumb)
                echo $crumb . ' ';
            echo $before . get_the_title() . $after;
        } elseif (is_search()) {
            echo $before . get_search_query() . $after;
        } elseif (is_tag()) {
            echo $before . single_tag_title('', false) . $after;
        } elseif (is_author()) {
            global $author;
            $userdata = get_userdata($author);
            echo $before . $userdata->display_name . $after;
        } elseif (is_404()) {
            echo $before . esc_html__('Error 404', 'italiawp3') . $after;
        }

        if (get_query_var('paged')) {
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo '<li class="breadcrumb-item">&nbsp;(';
            echo ' ' . esc_html__('Page', 'italiawp3') . ' ' . get_query_var('paged');
            if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author())
                echo '&nbsp;) </li>';
        }

        echo '</ol></nav></div>';
        echo '</div></div></section>';
    }
}

function my_excerpt($excerpt = '') {
    $excerpt = strip_shortcodes($excerpt);
    if (has_excerpt()) {
        return $excerpt;
    } else {
        $pos1 = strpos($excerpt, '.');
        if ($pos1) {
            $pos2 = strpos($excerpt, '.', $pos1 + 1);
            if ($pos1 < 50 && $pos2)
                return substr($excerpt, 0, $pos2 + 1);
            else
                return substr($excerpt, 0, $pos1 + 1);
        }
        return $excerpt;
    }
}

add_filter('get_the_excerpt', 'my_excerpt');

/* UPDATER THEME VERSION */
require 'inc/theme-update-checker.php';
$update_checker = new ThemeUpdateChecker(
        'italiawp3',
        'https://raw.githubusercontent.com/borisamico/design-wordpress-theme-italiaWP3/main/italiawp3.json'
);

/* Per la ricerca manuale degli aggiornamenti, altrimenti avviene automaticamente ogni 12 ore */
//$update_checker->checkForUpdates();

require_once 'inc/tgm-plugin-activation/class-tgm-plugin-activation.php';
add_action('tgmpa_register', 'italiawp3_register_required_plugins');

function italiawp3_register_required_plugins() {
    $plugins = array();

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if (!is_plugin_active('attachments/index.php')) {
        $plugins += array(
            array(
                'name' => 'Attachments',
                'slug' => 'attachments',
                'required' => false,
            ),
        );
    }

    $config = array(
        'id' => 'italiawp3',
        'default_path' => '',
        'menu' => 'tgmpa-install-plugins',
        'parent_slug' => 'themes.php',
        'capability' => 'edit_theme_options',
        'has_notices' => true,
        'dismissable' => true,
        'dismiss_msg' => '',
        'is_automatic' => false,
        'message' => '',
        'strings' => array(
	        'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
	        'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
	        /* translators: %s: plugin name. */
	        'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ),
	        /* translators: %s: plugin name. */
	        'updating'                        => __( 'Updating Plugin: %s', 'tgmpa' ),
	        'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
	        'notice_can_install_required'     => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'This theme requires the following plugin: %1$s.',
		        'This theme requires the following plugins: %1$s.',
		        'tgmpa'
	        ),
	        'notice_can_install_recommended'  => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'This theme recommends the following plugin: %1$s.',
		        'This theme recommends the following plugins: %1$s.',
		        'tgmpa'
	        ),
	        'notice_ask_to_update'            => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
		        'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
		        'tgmpa'
	        ),
	        'notice_ask_to_update_maybe'      => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'There is an update available for: %1$s.',
		        'There are updates available for the following plugins: %1$s.',
		        'tgmpa'
	        ),
	        'notice_can_activate_required'    => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'The following required plugin is currently inactive: %1$s.',
		        'The following required plugins are currently inactive: %1$s.',
		        'tgmpa'
	        ),
	        'notice_can_activate_recommended' => _n_noop(
	        /* translators: 1: plugin name(s). */
		        'The following recommended plugin is currently inactive: %1$s.',
		        'The following recommended plugins are currently inactive: %1$s.',
		        'tgmpa'
	        ),
	        'install_link'                    => _n_noop(
		        'Begin installing plugin',
		        'Begin installing plugins',
		        'tgmpa'
	        ),
	        'update_link'                     => _n_noop(
		        'Begin updating plugin',
		        'Begin updating plugins',
		        'tgmpa'
	        ),
	        'activate_link'                   => _n_noop(
		        'Begin activating plugin',
		        'Begin activating plugins',
		        'tgmpa'
	        ),
	        'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
	        'dashboard'                       => __( 'Return to the Dashboard', 'tgmpa' ),
	        'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
	        'activated_successfully'          => __( 'The following plugin was activated successfully:', 'tgmpa' ),
	        /* translators: 1: plugin name. */
	        'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'tgmpa' ),
	        /* translators: 1: plugin name. */
	        'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'tgmpa' ),
	        /* translators: 1: dashboard link. */
	        'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'tgmpa' ),
	        'dismiss'                         => __( 'Dismiss this notice', 'tgmpa' ),
	        'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'tgmpa' ),
	        'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'tgmpa' ),
        ),
    );
    tgmpa($plugins, $config);
}

require get_template_directory() . '/inc/gallery_cpt.php';

function current_url() {
    return esc_url(home_url(add_query_arg(array())));
}

function is_custom_post_type($post = NULL) {
    $all_custom_post_types = get_post_types(array('_builtin' => FALSE));

    if (empty($all_custom_post_types))
        return FALSE;

    $custom_types = array_keys($all_custom_post_types);
    $current_post_type = get_post_type($post);

    if (!$current_post_type)
        return FALSE;

    return in_array($current_post_type, $custom_types);
}

function italiawp3_metabox_menu_pt_archive() {

    $post_types = get_post_types(array(
        'show_in_nav_menus' => true,
        'has_archive' => true
            ), 'object');

    if ($post_types) :
        $items = array();
        $loop_index = 999999;

        foreach ($post_types as $post_type) {
            $item = new stdClass();
            $loop_index++;

            $item->object_id = $loop_index;
            $item->db_id = 0;
            $item->object = 'post_type_' . $post_type->query_var;
            $item->menu_item_parent = 0;
            $item->type = 'custom';
            $item->title = $post_type->labels->name;
            $item->url = get_post_type_archive_link($post_type->query_var);
            $item->target = '';
            $item->attr_title = '';
            $item->classes = array();
            $item->xfn = '';

            $items[] = $item;
        }

        $walker = new Walker_Nav_Menu_Checklist(array());

        echo '<div id="posttype-archive" class="posttypediv">';
        echo '<div id="tabs-panel-posttype-archive" class="tabs-panel tabs-panel-active">';
        echo '<ul id="posttype-archive-checklist" class="categorychecklist form-no-clear">';
        echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $items), 0, (object) array('walker' => $walker));
        echo '</ul>';
        echo '</div>';
        echo '</div>';

        echo '<p class="button-controls">';
        echo '<span class="add-to-menu">';
        echo '<input type="submit"' . disabled(1, 0) . ' class="button-secondary submit-add-to-menu right" value="' . esc_html__('Add to Menu', 'italiawp3') . '" name="add-posttype-archive-menu-item" id="submit-posttype-archive" />';
        echo '<span class="spinner"></span>';
        echo '</span>';
        echo '</p>';

    endif;
}

add_filter('request', 'italiawp3_date_search');

function italiawp3_date_search($query_vars) {
    global $wp_locale;

    if (is_admin() || empty($query_vars['s']) || !is_string($query_vars['s'])) {
        return $query_vars;
    }

    $search = $query_vars['s'];
    if (!preg_match('/\b(19[0-9]{2}|20[0-9]{2})\b/', $search, $year)) {
        return $query_vars;
    }

    $month = 0;
    $position = false;
    for ($m = 1; $m <= 12; $m++) {
        $found = stripos($search, $wp_locale->get_month($m));
        if ($found !== false && ($position === false || $found < $position)) {
            $position = $found;
            $month = $m;
        }
    }
    if (!$month) {
        return $query_vars;
    }

    unset($query_vars['s']);
    $query_vars['year'] = $year[1];
    $query_vars['monthnum'] = $month;
    if (preg_match('/^([0-3]?[0-9]) /', $search, $day)) {
        $query_vars['day'] = (int) $day[1];
    }

    return $query_vars;
}

/**
 * Garantisce che ogni <img> abbia un attributo alt.
 *
 * Le immagini dei box servizi arrivano dai campi personalizzati delle voci di menu,
 * cioe' da contenuto redazionale: il tema non le genera e non puo' imporre un alt in
 * fase di inserimento. Senza alt uno screen reader legge il nome del file, che e'
 * rumore (WCAG 1.1.1).
 *
 * Qui vengono trattate come decorative: alt="" le esclude dall'albero di
 * accessibilita', ed e' corretto perche' il testo del link accanto nomina gia' il
 * servizio. Le immagini che un alt ce l'hanno restano intatte.
 *
 * @param string $html frammento di markup.
 * @return string lo stesso markup con alt="" sulle immagini che ne erano prive.
 */
function italiawp3_alt_di_riserva($html) {
    if (strpos($html, '<img') === false) {
        return $html;
    }

    return preg_replace(
        '/<img(?![^>]*\balt\s*=)([^>]*?)(\s*\/?)>/i',
        '<img$1 alt=""$2>',
        $html
    );
}

/**
 * Rende accessibile il pulsante del blocco "Ricerca" del core.
 *
 * Nella variante con sola icona, WordPress stampa un <svg> senza aria-hidden dentro il
 * <button>. Lo screen reader tenta di interpretarlo e il pulsante puo' restare senza un
 * nome chiaro (WCAG 4.1.2). L'icona e' puramente decorativa: va nascosta e il nome deve
 * arrivare da testo reale.
 *
 * Si interviene sull'output del blocco perche' il markup e' generato dal core: non e'
 * modificabile dai template del tema.
 *
 * @param string $block_content markup del blocco.
 * @param array  $block         dati del blocco.
 * @return string markup corretto.
 */
function italiawp3_ricerca_blocco_accessibile($block_content, $block) {
    if (empty($block['blockName']) || $block['blockName'] !== 'core/search') {
        return $block_content;
    }

    // L'icona e' decorativa.
    $block_content = str_replace(
        '<svg class="search-icon"',
        '<svg class="search-icon" aria-hidden="true" focusable="false"',
        $block_content
    );

    // Il pulsante di sola icona non ha testo: gli si da' un nome accessibile.
    if (strpos($block_content, 'has-icon') !== false && strpos($block_content, 'aria-label') === false) {
        $block_content = str_replace(
            'class="wp-block-search__button has-icon',
            'aria-label="' . esc_attr__('Search', 'italiawp3') . '" class="wp-block-search__button has-icon',
            $block_content
        );
    }

    return $block_content;
}
add_filter('render_block', 'italiawp3_ricerca_blocco_accessibile', 10, 2);

/**
 * Avvisa in bacheca se mancano i riferimenti obbligatori per un sito della PA.
 *
 * I link a dichiarazione di accessibilita', privacy e note legali sono stampati in
 * footer.php solo se le relative option sono compilate: finche' restano vuote il link
 * semplicemente non compare, senza che nessuno se ne accorga. Per un sito pubblico non
 * e' una preferenza ma un obbligo (Legge Stanca 4/2004 e D.Lgs. 33/2013), quindi
 * l'omissione va segnalata invece di passare in silenzio.
 *
 * La dichiarazione di accessibilita' va generata e pubblicata su form.agid.gov.it:
 * qui si registra soltanto il suo indirizzo.
 */
function italiawp3_avviso_riferimenti_obbligatori() {
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $obbligatori = array(
        'dettagli-link-accessibilita' => __('Accessibility statement (URL on form.agid.gov.it)', 'italiawp3'),
        'dettagli-id-privacy'         => __('Privacy policy page', 'italiawp3'),
        'dettagli-id-notelegali'      => __('Legal notices page', 'italiawp3'),
    );

    $mancanti = array();
    foreach ($obbligatori as $option => $etichetta) {
        if (trim((string) get_option($option)) === '') {
            $mancanti[] = $etichetta;
        }
    }

    if (empty($mancanti)) {
        return;
    }

    printf(
        '<div class="notice notice-warning"><p><strong>%s</strong><br>%s</p><p>%s</p></div>',
        esc_html__('italiawp3: mandatory references are missing', 'italiawp3'),
        esc_html(
            __('These links are required for a public administration website and are currently not shown in the footer:', 'italiawp3')
            . ' ' . implode(', ', $mancanti) . '.'
        ),
        esc_html__('Fill them in under Details in the admin menu.', 'italiawp3')
    );
}
add_action('admin_notices', 'italiawp3_avviso_riferimenti_obbligatori');
