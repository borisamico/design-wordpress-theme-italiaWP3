<?php
/**
 * Verifica che la versione di bootstrap-italia sia la stessa nei tre punti in cui e'
 * dichiarata: i due asset vendorizzati in static/ e la costante
 * italiawp3_BOOTSTRAP_ITALIA_VERSION, che negli enqueue funge da cache buster.
 *
 * Se la costante resta indietro rispetto ai file, i browser continuano a servire dalla
 * cache il CSS della versione precedente dopo un aggiornamento: il sito si vede rotto e
 * la causa non e' evidente. Per questo la divergenza viene segnalata in bacheca invece di
 * restare una cosa che si scopre per caso.
 *
 * I due asset pesano 598 KB e 996 KB, ma i marker di versione stanno entrambi nei primi
 * mille byte: si legge solo l'inizio del file, mai il file intero.
 *
 * Questo file e' includibile anche fuori da WordPress (lo fa bin/check-bi-version.php):
 * le funzioni di estrazione non chiamano alcuna API di WordPress, e l'aggancio
 * dell'avviso e' condizionato alla presenza di ABSPATH.
 */

/**
 * Quanti byte leggere dalla testa degli asset per cercarvi il marker di versione.
 * I marker stanno a 526 (CSS) e 940 (JS): 4 KB e' abbondante e resta a costo trascurabile.
 */
if (!defined('italiawp3_VERSION_MARKER_BYTES')) {
    define('italiawp3_VERSION_MARKER_BYTES', 4096);
}

/**
 * Estrae la versione dalla testa di un file, senza caricarlo tutto.
 *
 * @param string $percorso Percorso assoluto del file.
 * @param string $regex    Espressione regolare con un gruppo di cattura.
 * @return string|null La versione trovata, oppure null.
 */
function italiawp3_bi_version_da_file($percorso, $regex) {
    if (!is_readable($percorso)) {
        return null;
    }

    $handle = fopen($percorso, 'rb');
    if (!$handle) {
        return null;
    }
    $testa = fread($handle, italiawp3_VERSION_MARKER_BYTES);
    fclose($handle);

    if ($testa === false || !preg_match($regex, $testa, $m)) {
        return null;
    }

    return $m[1];
}

/**
 * Cartella del tema che contiene questo file.
 *
 * Non usa get_template_directory() cosi' da funzionare anche da riga di comando; il
 * risultato coincide, perche' inc/ sta nella radice del tema padre.
 *
 * @return string
 */
function italiawp3_bi_dir_tema() {
    return dirname(__DIR__);
}

/**
 * La versione dichiarata dalla costante usata come cache buster.
 *
 * Sotto WordPress la costante e' gia' definita da inc/enqueue.php. Da riga di comando
 * enqueue.php non e' caricabile (chiama add_action), quindi il valore si legge dal
 * sorgente con la stessa espressione regolare.
 *
 * @return string|null
 */
function italiawp3_bi_version_costante() {
    if (defined('italiawp3_BOOTSTRAP_ITALIA_VERSION')) {
        return italiawp3_BOOTSTRAP_ITALIA_VERSION;
    }

    return italiawp3_bi_version_da_file(
        italiawp3_bi_dir_tema() . '/inc/enqueue.php',
        "/italiawp3_BOOTSTRAP_ITALIA_VERSION',\s*'([^']+)'/"
    );
}

/**
 * Le versioni dichiarate nei tre punti di verita'.
 *
 * @return array<string,string|null> Percorso relativo => versione (null se non trovata).
 */
function italiawp3_bi_versioni() {
    $dir = italiawp3_bi_dir_tema();

    return array(
        // Dalla 2.x il marker nel CSS e' una custom property a :root
        // (--bootstrap-italia-version: '2.18.3'); nella 1.x era bootstrap-italia-version:"1.6.4".
        'static/css/bootstrap-italia.min.css' => italiawp3_bi_version_da_file(
            $dir . '/static/css/bootstrap-italia.min.css',
            '/--bootstrap-italia-version:\s*[\'"]([^\'"]+)[\'"]/'
        ),
        // Nel JS il marker e' invariato fra 1.x e 2.x. Cambia il file: si vendorizza il
        // bundle, perche' bootstrap-italia.min.js 2.x ha dipendenze esterne non
        // risolvibili senza bundler.
        'static/js/bootstrap-italia.bundle.min.js' => italiawp3_bi_version_da_file(
            $dir . '/static/js/bootstrap-italia.bundle.min.js',
            '/BOOTSTRAP_ITALIA_VERSION="([^"]+)"/'
        ),
        'inc/enqueue.php' => italiawp3_bi_version_costante(),
    );
}

/**
 * Le versioni divergono?
 *
 * @return array<string,string|null>|false L'elenco completo se divergono, false se
 *                                         sono tutte uguali e trovate.
 */
function italiawp3_bi_version_mismatch() {
    $versioni = italiawp3_bi_versioni();
    $distinte = array_unique(array_values($versioni), SORT_REGULAR);

    if (count($distinte) === 1 && $distinte[array_key_first($distinte)] !== null) {
        return false;
    }

    return $versioni;
}

/*
 * Da qui in poi serve WordPress. Il file resta includibile da riga di comando.
 */
if (!defined('ABSPATH')) {
    return;
}

/**
 * Esito del controllo, memorizzato per non rileggere i file a ogni schermata.
 *
 * La chiave del transient contiene la versione dichiarata e la data di modifica dei due
 * asset: appena uno dei tre cambia, la chiave cambia con esso e il vecchio esito non
 * viene piu' letto. Cosi' l'avviso sparisce nell'istante in cui si allinea la versione,
 * senza aspettare la scadenza e senza doverlo invalidare a mano.
 *
 * @return array<string,string|null>|false
 */
function italiawp3_bi_version_mismatch_cached() {
    $dir = italiawp3_bi_dir_tema();

    $chiave = 'italiawp3_bi_ver_' . md5(implode('|', array(
        (string) italiawp3_bi_version_costante(),
        (string) @filemtime($dir . '/static/css/bootstrap-italia.min.css'),
        (string) @filemtime($dir . '/static/js/bootstrap-italia.bundle.min.js'),
    )));

    $esito = get_transient($chiave);
    if ($esito !== false) {
        return $esito === 'ok' ? false : $esito;
    }

    $mismatch = italiawp3_bi_version_mismatch();
    set_transient($chiave, $mismatch === false ? 'ok' : $mismatch, 12 * HOUR_IN_SECONDS);

    return $mismatch;
}

/**
 * Avvisa in bacheca se le versioni di bootstrap-italia non sono allineate.
 */
function italiawp3_avviso_versione_bootstrap_italia() {
    if (!current_user_can('switch_themes')) {
        return;
    }

    $mismatch = italiawp3_bi_version_mismatch_cached();
    if ($mismatch === false) {
        return;
    }

    $righe = array();
    foreach ($mismatch as $dove => $versione) {
        $righe[] = sprintf(
            '<code>%s</code> &rarr; %s',
            esc_html($dove),
            $versione === null
                ? '<em>' . esc_html__('version not found', 'italiawp3') . '</em>'
                : esc_html($versione)
        );
    }

    printf(
        '<div class="notice notice-warning"><p><strong>%s</strong></p><p>%s</p><p>%s</p></div>',
        esc_html__('italiawp3: Bootstrap Italia versions are not aligned', 'italiawp3'),
        implode('<br>', $righe),
        esc_html__(
            'Align the italiawp3_BOOTSTRAP_ITALIA_VERSION constant in inc/enqueue.php with the vendored assets: it is the cache buster, and while it lags browsers keep serving the previous stylesheet from cache.',
            'italiawp3'
        )
    );
}
add_action('admin_notices', 'italiawp3_avviso_versione_bootstrap_italia');
