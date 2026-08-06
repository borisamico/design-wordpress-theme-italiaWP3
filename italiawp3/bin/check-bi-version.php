<?php
/**
 * Verifica da riga di comando che la versione di bootstrap-italia sia la stessa nei tre
 * punti in cui e' dichiarata: i due asset vendorizzati in static/ e la costante
 * italiawp3_BOOTSTRAP_ITALIA_VERSION in inc/enqueue.php.
 *
 * Uso: php bin/check-bi-version.php
 *
 * Va eseguito dopo aver sostituito a mano gli asset di bootstrap-italia: la procedura e'
 * in README.md. La stessa verifica gira anche sotto WordPress e, se qualcosa non torna,
 * compare come avviso in bacheca — vedi inc/version-check.php.
 */

require_once dirname(__DIR__) . '/inc/version-check.php';

$versioni = italiawp3_bi_versioni();

foreach ($versioni as $dove => $versione) {
    printf("  %-10s %s\n", $versione === null ? '?' : $versione, $dove);
}

if (italiawp3_bi_version_mismatch() !== false) {
    fwrite(STDERR, "\nDISALLINEAMENTO: allinea la costante italiawp3_BOOTSTRAP_ITALIA_VERSION "
        . "in inc/enqueue.php agli asset vendorizzati in static/.\n");
    exit(1);
}

$distinte = array_values(array_unique(array_values($versioni)));
printf("\nOK: bootstrap-italia %s allineato ovunque.\n", $distinte[0]);
