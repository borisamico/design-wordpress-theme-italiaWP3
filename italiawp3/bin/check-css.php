<?php
/**
 * Controllo di sintassi minimo sui CSS del tema.
 *
 * Nasce da un errore reale: in static/css/bi1-compat.css un commento conteneva la
 * sequenza "m-*" seguita da "/", cioe' "*​/", che chiude il commento in anticipo.
 * Il file continuava a essere servito e nessuno segnalava nulla, ma il browser
 * scartava tutto quello che veniva dopo — 450 regole su 490 — e il tema si
 * ritrovava con meta' foglio silenziosamente inattivo.
 *
 * Verifica: commenti bilanciati, graffe bilanciate, nessun "*​/" orfano.
 *
 * Uso: php bin/check-css.php
 *
 * Porting 1:1 del precedente bin/check-css.js. E' in PHP perche' il tema non
 * richiede node: gli asset sono gia' compilati e non esiste alcun build step.
 */

$root = dirname(__DIR__);
$dirs = array('static/css', 'inc');

$errori = 0;
$controllati = 0;

/**
 * Controlla un singolo foglio di stile.
 *
 * @param string $root Cartella del tema.
 * @param string $rel  Percorso del file relativo alla cartella del tema.
 * @return int Numero di problemi trovati.
 */
function italiawp3_controlla_css($root, $rel) {
    $css = file_get_contents($root . '/' . $rel);
    $len = strlen($css);
    $errori = 0;

    // 1. Commenti: aperture non chiuse e — soprattutto — chiusure ORFANE.
    // Una "*/" fuori da un commento significa quasi sempre che un commento si e'
    // chiuso prima del previsto, perche' il testo conteneva un "*" seguito da "/".
    // E' il caso che ha motivato questo script.
    $i = 0;
    $in_comment = false;
    $apertura_riga = 0;
    $riga = 1;
    while ($i < $len - 1) {
        if ($css[$i] === "\n") {
            $riga++;
        }
        if (!$in_comment && $css[$i] === '/' && $css[$i + 1] === '*') {
            $in_comment = true;
            $apertura_riga = $riga;
            $i += 2;
            continue;
        }
        if ($in_comment && $css[$i] === '*' && $css[$i + 1] === '/') {
            $in_comment = false;
            $i += 2;
            continue;
        }
        if (!$in_comment && $css[$i] === '*' && $css[$i + 1] === '/') {
            fwrite(STDERR, sprintf(
                "  %s:%d  \"*/\" orfano: un commento si e' chiuso prima del previsto "
                . "(un \"*\" seguito da \"/\" dentro al testo?). Tutto cio' che segue viene scartato dal browser.\n",
                $rel,
                $riga
            ));
            $errori++;
            $i += 2;
            continue;
        }
        $i++;
    }
    if ($in_comment) {
        fwrite(STDERR, sprintf("  %s:%d  commento aperto e mai chiuso\n", $rel, $apertura_riga));
        $errori++;
    }

    // 2. Graffe bilanciate, ignorando quelle dentro commenti e stringhe.
    $depth = 0;
    $min_depth = 0;
    $in_comment = false;
    $quote = null;
    for ($j = 0; $j < $len; $j++) {
        $c = $css[$j];
        $next = ($j + 1 < $len) ? $css[$j + 1] : '';
        if ($in_comment) {
            if ($c === '*' && $next === '/') {
                $in_comment = false;
                $j++;
            }
            continue;
        }
        if ($quote !== null) {
            if ($c === '\\') {
                $j++;
            } elseif ($c === $quote) {
                $quote = null;
            }
            continue;
        }
        if ($c === '/' && $next === '*') {
            $in_comment = true;
            $j++;
            continue;
        }
        if ($c === '"' || $c === "'") {
            $quote = $c;
            continue;
        }
        if ($c === '{') {
            $depth++;
        }
        if ($c === '}') {
            $depth--;
            if ($depth < $min_depth) {
                $min_depth = $depth;
            }
        }
    }
    if ($depth !== 0 || $min_depth < 0) {
        fwrite(STDERR, sprintf(
            "  %s  graffe sbilanciate (saldo finale %d, minimo %d)\n",
            $rel,
            $depth,
            $min_depth
        ));
        $errori++;
    }

    return $errori;
}

foreach ($dirs as $d) {
    $abs = $root . '/' . $d;
    if (!is_dir($abs)) {
        continue;
    }
    $files = scandir($abs);
    sort($files);
    foreach ($files as $f) {
        if (substr($f, -4) !== '.css') {
            continue;
        }
        if (substr($f, 0, 2) === '__') {
            continue;                       // backup morti dell'autore
        }
        if ($f === 'bootstrap-italia.min.css') {
            continue;                       // vendorizzato
        }
        $errori += italiawp3_controlla_css($root, $d . '/' . $f);
        $controllati++;
    }
}

if ($errori) {
    fwrite(STDERR, sprintf("\n%d file con problemi di sintassi.\n", $errori));
    exit(1);
}
printf("OK: %d file CSS sintatticamente validi.\n", $controllati);
