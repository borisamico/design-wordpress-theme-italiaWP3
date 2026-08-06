<?php
/**
 * Pannello «Accessibilita» nel backoffice.
 *
 * Chi gestisce il sito non deve aprire il codice del tema per sapere come sta messo.
 *
 * IL VINCOLO CHE GOVERNA TUTTO IL PANNELLO: gli strumenti automatici intercettano
 * circa un terzo dei problemi. Tastiera e screen reader, che sono gli altri due terzi,
 * non sono automatizzabili — nemmeno da axe. Quindi questa pagina non mostra mai
 * "tutto verde": mostra che cosa ha guardato e che cosa non puo' guardare. Una
 * dashboard che segna conforme cio' che non ha misurato e' peggio di nessuna
 * dashboard, perche' l'ente firma la dichiarazione su quella base e ci mette una
 * responsabilita' legale.
 *
 * Costo per il front-end: zero. Questo file e i controlli si caricano solo dentro
 * is_admin(), e i controlli veri e propri solo dentro le callback della pagina e
 * delle azioni AJAX.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('italiawp3_A11Y_SLUG', 'italiawp3-accessibilita');
define('italiawp3_A11Y_OPT_CAMPIONE', 'italiawp3_a11y_campione');
define('italiawp3_A11Y_OPT_ESITI_HTML', 'italiawp3_a11y_esiti_html');
define('italiawp3_A11Y_OPT_ESITI_CONTENUTI', 'italiawp3_a11y_esiti_contenuti');
define('italiawp3_A11Y_OPT_ESITI_RESA', 'italiawp3_a11y_esiti_resa');

require_once __DIR__ . '/checklist.php';

/**
 * Registra la voce di menu.
 */
function italiawp3_a11y_menu() {
    add_menu_page(
        __('Accessibility', 'italiawp3'),
        __('Accessibility', 'italiawp3'),
        'manage_options',
        italiawp3_A11Y_SLUG,
        'italiawp3_a11y_pagina',
        'dashicons-universal-access',
        59
    );
}
add_action('admin_menu', 'italiawp3_a11y_menu');

/**
 * Il campione di pagine da esaminare.
 *
 * La metodologia AgID chiede almeno una pagina per ogni template. I sette tipi
 * proposti qui sono quelli che il tema effettivamente distingue; i template che
 * l'ente aggiunge il tema non li conosce, per questo l'elenco e' modificabile.
 *
 * @return string[]
 */
function italiawp3_a11y_campione() {
    $salvato = get_option(italiawp3_A11Y_OPT_CAMPIONE, null);
    if (is_array($salvato) && $salvato) {
        return $salvato;
    }

    return italiawp3_a11y_campione_predefinito();
}

/**
 * Costruisce un campione ragionevole guardando che cosa c'e' davvero sul sito.
 *
 * @return string[]
 */
function italiawp3_a11y_campione_predefinito() {
    $urls = array(home_url('/'));

    /*
     * Si usa reset() e non [0]: get_categories() restituisce l'array indicizzato per
     * term_id, non da zero. Con [0] usciva un Warning a ogni apertura della pagina —
     * proprio nel pannello che serve a segnalare i difetti. Anche dove l'indice
     * numerico e' garantito (get_posts, get_pages) si usa la stessa forma, cosi' la
     * distrazione non si ripete.
     */

    // Un articolo con commenti, se c'e'; altrimenti l'ultimo articolo.
    $con_commenti = get_posts(array(
        'numberposts' => 1,
        'post_type'   => 'any',
        'orderby'     => 'comment_count',
        'order'       => 'DESC',
    ));
    $primo = $con_commenti ? reset($con_commenti) : null;
    if ($primo instanceof WP_Post) {
        $urls[] = get_permalink($primo);
    }

    // Un archivio per ogni tassonomia principale.
    $categorie = get_categories(array('number' => 1, 'hide_empty' => true));
    $categoria = (is_array($categorie) && $categorie) ? reset($categorie) : null;
    if ($categoria instanceof WP_Term) {
        $urls[] = get_category_link($categoria->term_id);
    }

    // Una pagina con figli: e' il template piu' diverso dagli altri.
    $pagine = get_pages(array('parent' => 0, 'number' => 20, 'sort_column' => 'menu_order'));
    if (is_array($pagine)) {
        foreach ($pagine as $pagina) {
            $figli = get_pages(array('child_of' => $pagina->ID, 'number' => 1));
            if (is_array($figli) && $figli) {
                $urls[] = get_permalink($pagina->ID);
                break;
            }
        }
    }

    // Una voce per ogni custom post type pubblico (gallerie comprese).
    foreach (get_post_types(array('public' => true, '_builtin' => false), 'objects') as $cpt) {
        $voci = get_posts(array('numberposts' => 1, 'post_type' => $cpt->name));
        $voce = $voci ? reset($voci) : null;
        if ($voce instanceof WP_Post) {
            $urls[] = get_permalink($voce);
        }
    }

    // La ricerca: ha un template proprio e una live region.
    $urls[] = home_url('/?s=' . rawurlencode(__('municipality', 'italiawp3')));

    return array_values(array_unique(array_filter($urls)));
}

/**
 * Accoda il JS del pannello solo sulla propria schermata.
 *
 * @param string $hook
 */
function italiawp3_a11y_assets($hook) {
    if ($hook !== 'toplevel_page_' . italiawp3_A11Y_SLUG) {
        return;
    }

    wp_enqueue_script(
        'italiawp3-a11y-panel',
        get_template_directory_uri() . '/static/js/a11y-panel.js',
        array(),
        wp_get_theme()->get('Version'),
        true
    );

    wp_localize_script('italiawp3-a11y-panel', 'italiawp3A11y', array(
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('italiawp3_a11y'),
        'campione' => array_map('italiawp3_a11y_url_pulito', italiawp3_a11y_campione()),
        'i18n'     => array(
            'avvio'      => __('Starting…', 'italiawp3'),
            'pagina'     => __('Page', 'italiawp3'),
            'di'         => __('of', 'italiawp3'),
            'contrasto'  => __('Contrast', 'italiawp3'),
            'focus'      => __('Focus', 'italiawp3'),
            'reflow'     => __('Reflow at 320px', 'italiawp3'),
            'nessuno'    => __('no problems found', 'italiawp3'),
            'completato' => __('Check completed.', 'italiawp3'),
            'errore'     => __('The page could not be loaded in the frame.', 'italiawp3'),
        ),
    ));
}
add_action('admin_enqueue_scripts', 'italiawp3_a11y_assets');

/**
 * Aggiunge il parametro che spegne la barra di amministrazione dentro l'iframe.
 *
 * Senza, l'iframe carica il sito da amministratore loggato: compare la adminbar,
 * il layout si sposta e i contrasti misurati non sono quelli che vede il pubblico.
 *
 * @param string $url
 * @return string
 */
function italiawp3_a11y_url_pulito($url) {
    return add_query_arg('iwp_a11y', '1', $url);
}

/**
 * Spegne la barra di amministrazione quando la pagina e' caricata dal pannello.
 *
 * @param bool $mostra
 * @return bool
 */
function italiawp3_a11y_nascondi_adminbar($mostra) {
    if (isset($_GET['iwp_a11y']) && current_user_can('manage_options')) {
        return false;
    }

    return $mostra;
}
add_filter('show_admin_bar', 'italiawp3_a11y_nascondi_adminbar');

/* -------------------------------------------------------------------------
 * Azioni AJAX. Ogni scansione parte solo da un pulsante: niente lavoro a
 * sorpresa all'apertura della pagina, niente loopback che parte da solo.
 * ---------------------------------------------------------------------- */

/**
 * Controlla permessi e nonce, o interrompe.
 */
function italiawp3_a11y_verifica_richiesta() {
    check_ajax_referer('italiawp3_a11y', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('messaggio' => __('Not allowed.', 'italiawp3')), 403);
    }
}

/**
 * Scansione statica dell'HTML, una pagina per chiamata.
 */
function italiawp3_a11y_ajax_html() {
    italiawp3_a11y_verifica_richiesta();
    require_once __DIR__ . '/checks-html.php';

    $indice = isset($_POST['indice']) ? absint($_POST['indice']) : 0;
    $campione = italiawp3_a11y_campione();

    if (!isset($campione[$indice])) {
        wp_send_json_success(array('finita' => true, 'indice' => $indice, 'totale' => count($campione)));
    }

    if ($indice === 0) {
        update_option(italiawp3_A11Y_OPT_ESITI_HTML, array('data' => current_time('mysql'), 'pagine' => array()), false);
    }

    $url = $campione[$indice];
    $pagina = italiawp3_a11y_scarica($url);
    $esito = $pagina['ok']
        ? array('ok' => true, 'errore' => '', 'problemi' => italiawp3_a11y_analizza($pagina['html']))
        : array('ok' => false, 'errore' => $pagina['errore'], 'problemi' => array());

    $salvati = get_option(italiawp3_A11Y_OPT_ESITI_HTML, array('data' => current_time('mysql'), 'pagine' => array()));
    $salvati['pagine'][$url] = $esito;
    $salvati['data'] = current_time('mysql');
    update_option(italiawp3_A11Y_OPT_ESITI_HTML, $salvati, false);

    wp_send_json_success(array(
        'finita'  => ($indice + 1) >= count($campione),
        'indice'  => $indice + 1,
        'totale'  => count($campione),
        'url'     => $url,
        'esito'   => $esito,
    ));
}
add_action('wp_ajax_italiawp3_a11y_html', 'italiawp3_a11y_ajax_html');

/**
 * Scansione dei contenuti, un lotto per chiamata.
 */
function italiawp3_a11y_ajax_contenuti() {
    italiawp3_a11y_verifica_richiesta();
    require_once __DIR__ . '/checks-content.php';

    $fase = isset($_POST['fase']) ? sanitize_key($_POST['fase']) : 'contenuti';
    $offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;

    if (!in_array($fase, array('contenuti', 'immagini', 'pdf'), true)) {
        $fase = 'contenuti';
    }

    if ($fase === 'contenuti' && $offset === 0) {
        update_option(italiawp3_A11Y_OPT_ESITI_CONTENUTI, array('data' => current_time('mysql'), 'rilievi' => array()), false);
    }

    $lotto = italiawp3_a11y_lotto($fase, $offset);

    $salvati = get_option(italiawp3_A11Y_OPT_ESITI_CONTENUTI, array('data' => current_time('mysql'), 'rilievi' => array()));
    $salvati['rilievi'] = array_merge($salvati['rilievi'], $lotto['rilievi']);
    $salvati['data'] = current_time('mysql');
    update_option(italiawp3_A11Y_OPT_ESITI_CONTENUTI, $salvati, false);

    // Passaggio alla fase successiva.
    $prossima = $fase;
    $prossimo_offset = $lotto['offset'];
    if ($lotto['finita']) {
        $ordine = array('contenuti' => 'immagini', 'immagini' => 'pdf', 'pdf' => '');
        $prossima = $ordine[$fase];
        $prossimo_offset = 0;
    }

    wp_send_json_success(array(
        'fase'            => $fase,
        'prossima'        => $prossima,
        'offset'          => $prossimo_offset,
        'totale'          => $lotto['totale'],
        'esaminati'       => min($lotto['offset'], $lotto['totale']),
        'nuovi'           => count($lotto['rilievi']),
        'finita'          => $prossima === '',
    ));
}
add_action('wp_ajax_italiawp3_a11y_contenuti', 'italiawp3_a11y_ajax_contenuti');

/**
 * Riceve gli esiti dei controlli sul reso, misurati dal browser nell'iframe.
 */
function italiawp3_a11y_ajax_resa() {
    italiawp3_a11y_verifica_richiesta();

    $grezzo = isset($_POST['esiti']) ? wp_unslash($_POST['esiti']) : '';
    $esiti = json_decode($grezzo, true);
    if (!is_array($esiti)) {
        wp_send_json_error(array('messaggio' => __('Malformed results.', 'italiawp3')), 400);
    }

    // Si conserva solo cio' che serve, gia' ripulito.
    $puliti = array();
    foreach ($esiti as $url => $dati) {
        $puliti[esc_url_raw($url)] = array(
            'contrasto' => array_map('sanitize_text_field', (array) ($dati['contrasto'] ?? array())),
            'focus'     => array_map('sanitize_text_field', (array) ($dati['focus'] ?? array())),
            'reflow'    => sanitize_text_field($dati['reflow'] ?? ''),
        );
    }

    update_option(italiawp3_A11Y_OPT_ESITI_RESA, array(
        'data'   => current_time('mysql'),
        'pagine' => $puliti,
    ), false);

    wp_send_json_success();
}
add_action('wp_ajax_italiawp3_a11y_resa', 'italiawp3_a11y_ajax_resa');

/**
 * Salva una voce della checklist manuale.
 */
function italiawp3_a11y_ajax_checklist() {
    italiawp3_a11y_verifica_richiesta();

    $voce = isset($_POST['voce']) ? sanitize_key($_POST['voce']) : '';
    $stato = isset($_POST['stato']) ? sanitize_key($_POST['stato']) : '';
    $note = isset($_POST['note']) ? wp_unslash($_POST['note']) : '';

    $valide = array();
    foreach (italiawp3_a11y_voci_manuali() as $gruppo) {
        $valide = array_merge($valide, array_keys($gruppo['voci']));
    }
    if (!in_array($voce, $valide, true)) {
        wp_send_json_error(array('messaggio' => __('Unknown item.', 'italiawp3')), 400);
    }

    wp_send_json_success(italiawp3_a11y_checklist_salva($voce, $stato, $note));
}
add_action('wp_ajax_italiawp3_a11y_checklist', 'italiawp3_a11y_ajax_checklist');

/**
 * Salva il campione di pagine.
 */
function italiawp3_a11y_salva_campione() {
    if (!isset($_POST['italiawp3_a11y_campione_nonce'])) {
        return;
    }
    check_admin_referer('italiawp3_a11y_campione', 'italiawp3_a11y_campione_nonce');
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Not allowed.', 'italiawp3'));
    }

    $grezzo = isset($_POST['campione']) ? wp_unslash($_POST['campione']) : '';
    $urls = array();
    foreach (preg_split('/[\r\n]+/', $grezzo) as $riga) {
        $riga = trim($riga);
        if ($riga === '') {
            continue;
        }
        // Solo URL di questo sito: il pannello non e' uno scanner per il web.
        $url = esc_url_raw($riga);
        if ($url && strpos($url, home_url()) === 0) {
            $urls[] = $url;
        }
    }

    update_option(italiawp3_A11Y_OPT_CAMPIONE, array_values(array_unique($urls)), false);

    wp_safe_redirect(add_query_arg(
        array('page' => italiawp3_A11Y_SLUG, 'campione' => 'salvato'),
        admin_url('admin.php')
    ));
    exit;
}
add_action('admin_init', 'italiawp3_a11y_salva_campione');

/**
 * Versione di Bootstrap Italia: sola lettura.
 *
 * Nessuna scrittura di file. In produzione la cartella del tema spesso non e'
 * scrivibile; scaricare ed eseguire codice remoto senza verifica di checksum e' una
 * via d'ingresso; un download interrotto lascia il sito col CSS a meta'; e comunque
 * il primo aggiornamento del tema sovrascrive tutto, perche' gli asset viaggiano
 * dentro le release. Il canale giusto e' una release del tema.
 *
 * @return array
 */
function italiawp3_a11y_stato_bootstrap_italia() {
    require_once get_template_directory() . '/inc/version-check.php';

    $versioni = italiawp3_bi_versioni();
    $allineate = italiawp3_bi_version_mismatch() === false;

    $ultima = get_transient('italiawp3_bi_ultima_release');
    if ($ultima === false) {
        $ultima = '';
        /*
         * Si scorre l'elenco delle release invece di usare /releases/latest: quel
         * percorso ha restituito 3.0.0-beta.4, perche' esclude solo cio' che e'
         * marcato come prerelease su GitHub e non chi si limita a mettere -beta nel
         * tag. Proporre una beta a un ente pubblico e' un invito a rompere il sito.
         * Si prende quindi la prima release stabile: niente flag prerelease, niente
         * bozza, e nessun suffisso dopo il numero di versione.
         */
        $risposta = wp_remote_get(
            'https://api.github.com/repos/italia/bootstrap-italia/releases?per_page=20',
            array('timeout' => 8, 'headers' => array('Accept' => 'application/vnd.github+json'))
        );
        if (!is_wp_error($risposta) && (int) wp_remote_retrieve_response_code($risposta) === 200) {
            $release = json_decode(wp_remote_retrieve_body($risposta), true);
            if (is_array($release)) {
                foreach ($release as $r) {
                    if (!empty($r['prerelease']) || !empty($r['draft']) || empty($r['tag_name'])) {
                        continue;
                    }
                    $tag = ltrim((string) $r['tag_name'], 'v');
                    if (preg_match('/^\d+\.\d+\.\d+$/', $tag)) {
                        $ultima = $tag;
                        break;
                    }
                }
            }
        }
        // Anche il fallimento va in cache: se la rete non c'e', non si riprova a ogni giro.
        set_transient('italiawp3_bi_ultima_release', $ultima, 12 * HOUR_IN_SECONDS);
    }

    return array(
        'versioni'  => $versioni,
        'allineate' => $allineate,
        'ultima'    => $ultima,
    );
}

require_once __DIR__ . '/pagina.php';
