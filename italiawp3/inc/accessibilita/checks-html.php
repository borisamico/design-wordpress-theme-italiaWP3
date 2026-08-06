<?php
/**
 * Controlli statici sull'HTML delle pagine pubbliche.
 *
 * Corrisponde al primo livello della metodologia di valutazione AgID: la scansione
 * automatica, che intercetta circa un terzo dei problemi. Gli altri due terzi —
 * navigazione da tastiera e screen reader — non sono automatizzabili da nessuno
 * strumento, e stanno nella checklist manuale (checklist.php).
 *
 * Le pagine si scaricano con una richiesta di loopback verso il sito stesso, come fa
 * la Salute del sito di WordPress, e si analizzano con DOMDocument. La richiesta parte
 * SENZA cookie di sessione: si guarda quello che vede il pubblico, non l'amministratore.
 *
 * Ogni controllo dichiara il criterio WCAG che copre. Cio' che non e' misurabile qui
 * non viene dichiarato conforme: viene semplicemente omesso.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scarica una pagina del sito con una richiesta di loopback.
 *
 * @param string $url URL assoluto, sullo stesso sito.
 * @return array{ok:bool,html:string,errore:string,codice:int}
 */
function italiawp3_a11y_scarica($url) {
    $risposta = wp_remote_get($url, array(
        'timeout'     => 20,
        'redirection' => 3,
        // Niente cookie: la pagina va vista da sloggati.
        'cookies'     => array(),
        'sslverify'   => apply_filters('https_local_ssl_verify', false),
        'user-agent'  => 'italiawp3-accessibilita/1.0; ' . home_url('/'),
    ));

    if (is_wp_error($risposta)) {
        return array('ok' => false, 'html' => '', 'codice' => 0, 'errore' => $risposta->get_error_message());
    }

    $codice = (int) wp_remote_retrieve_response_code($risposta);
    $html   = (string) wp_remote_retrieve_body($risposta);

    if ($codice !== 200) {
        return array('ok' => false, 'html' => '', 'codice' => $codice, 'errore' => sprintf(
            /* translators: %d: HTTP status code. */
            __('the server answered with HTTP %d', 'italiawp3'),
            $codice
        ));
    }

    if ($html === '') {
        return array('ok' => false, 'html' => '', 'codice' => $codice, 'errore' => __('empty response', 'italiawp3'));
    }

    return array('ok' => true, 'html' => $html, 'codice' => $codice, 'errore' => '');
}

/**
 * Costruisce un DOMDocument da una stringa HTML.
 *
 * loadHTML() presume latin-1: senza la dichiarazione di codifica gli accenti si
 * rompono e i messaggi diventano illeggibili. Non si usa mb_convert_encoding con
 * HTML-ENTITIES, deprecato da PHP 8.2.
 *
 * @param string $html
 * @return DOMDocument|null
 */
function italiawp3_a11y_dom($html) {
    if (!class_exists('DOMDocument')) {
        return null;
    }

    $precedente = libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $ok = $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($precedente);

    return $ok ? $dom : null;
}

/**
 * Un problema trovato.
 *
 * @param string   $id        Identificativo del controllo.
 * @param string   $criterio  Criterio WCAG coperto.
 * @param string   $gravita   'errore' oppure 'avviso'.
 * @param string   $messaggio Descrizione sintetica.
 * @param string[] $dettagli  Elementi coinvolti, gia' accorciati.
 * @return array
 */
function italiawp3_a11y_esito($id, $criterio, $gravita, $messaggio, $dettagli = array()) {
    return array(
        'id'        => $id,
        'criterio'  => $criterio,
        'gravita'   => $gravita,
        'messaggio' => $messaggio,
        'dettagli'  => array_values(array_slice($dettagli, 0, 12)),
        'totale'    => count($dettagli),
    );
}

/**
 * Rappresentazione breve di un elemento, per far capire dove intervenire.
 *
 * @param DOMElement $el
 * @return string
 */
function italiawp3_a11y_descrivi($el) {
    $s = '<' . $el->tagName;
    foreach (array('id', 'class', 'name', 'href', 'src', 'type') as $attr) {
        if ($el->hasAttribute($attr)) {
            $v = $el->getAttribute($attr);
            if (strlen($v) > 40) {
                $v = substr($v, 0, 37) . '...';
            }
            $s .= ' ' . $attr . '="' . $v . '"';
        }
    }
    $s .= '>';

    $testo = trim(preg_replace('/\s+/', ' ', $el->textContent));
    if ($testo !== '') {
        $s .= ' ' . (strlen($testo) > 40 ? substr($testo, 0, 37) . '...' : $testo);
    }

    return $s;
}

/**
 * Nome accessibile di un elemento, nella misura in cui e' calcolabile staticamente.
 *
 * Non e' l'algoritmo completo di accname: non risolve i CSS generated content ne'
 * gli elementi nascosti. Basta pero' a distinguere un controllo senza alcun nome
 * da uno che ce l'ha, che e' cio' che interessa qui.
 *
 * @param DOMElement $el
 * @param DOMXPath   $xpath
 * @return string
 */
function italiawp3_a11y_nome_accessibile($el, $xpath) {
    if ($el->hasAttribute('aria-labelledby')) {
        $nome = '';
        foreach (preg_split('/\s+/', trim($el->getAttribute('aria-labelledby'))) as $rif) {
            if ($rif === '') {
                continue;
            }
            $bersaglio = $xpath->query('//*[@id="' . addslashes($rif) . '"]');
            if ($bersaglio->length) {
                $nome .= ' ' . $bersaglio->item(0)->textContent;
            }
        }
        if (trim($nome) !== '') {
            return trim($nome);
        }
    }

    if (trim($el->getAttribute('aria-label')) !== '') {
        return trim($el->getAttribute('aria-label'));
    }

    $testo = trim($el->textContent);
    if ($testo !== '') {
        return $testo;
    }

    // Un'icona con alt, o un <img> dentro il link, danno il nome.
    foreach ($xpath->query('.//img[@alt]', $el) as $img) {
        if (trim($img->getAttribute('alt')) !== '') {
            return trim($img->getAttribute('alt'));
        }
    }

    // <title> dentro un <svg> non nascosto.
    foreach ($xpath->query('.//*[local-name()="title"]', $el) as $t) {
        if (trim($t->textContent) !== '') {
            return trim($t->textContent);
        }
    }

    if (trim($el->getAttribute('title')) !== '') {
        return trim($el->getAttribute('title'));
    }

    return '';
}

/**
 * Esegue tutti i controlli statici su una pagina.
 *
 * @param string $html HTML completo della pagina.
 * @return array Elenco di esiti (solo i problemi trovati).
 */
function italiawp3_a11y_analizza($html) {
    $dom = italiawp3_a11y_dom($html);
    if (!$dom) {
        return array(italiawp3_a11y_esito(
            'parsing', '—', 'errore',
            __('The page could not be parsed (DOMDocument unavailable or malformed markup).', 'italiawp3')
        ));
    }

    $xpath = new DOMXPath($dom);
    $problemi = array();

    // --- 3.1.1 Lingua della pagina -----------------------------------------
    $html_el = $xpath->query('//html')->item(0);
    if (!$html_el || trim($html_el->getAttribute('lang')) === '') {
        $problemi[] = italiawp3_a11y_esito(
            'lang', '3.1.1', 'errore',
            __('The <html> element has no lang attribute: screen readers cannot choose the right pronunciation.', 'italiawp3')
        );
    }

    // --- 1.3.1 Un solo <h1> -------------------------------------------------
    $h1 = $xpath->query('//h1');
    if ($h1->length === 0) {
        $problemi[] = italiawp3_a11y_esito(
            'h1-assente', '1.3.1', 'errore',
            __('The page has no <h1>: the main content has no title.', 'italiawp3')
        );
    } elseif ($h1->length > 1) {
        $dettagli = array();
        foreach ($h1 as $t) {
            $dettagli[] = trim(preg_replace('/\s+/', ' ', $t->textContent));
        }
        $problemi[] = italiawp3_a11y_esito(
            'h1-multipli', '1.3.1', 'errore',
            sprintf(
                /* translators: %d: number of h1 elements found. */
                __('The page has %d <h1> elements: there must be exactly one, and it is the content title.', 'italiawp3'),
                $h1->length
            ),
            $dettagli
        );
    }

    // --- 1.3.1 Gerarchia dei titoli senza salti -----------------------------
    $salti = array();
    $precedente = 0;
    foreach ($xpath->query('//h1|//h2|//h3|//h4|//h5|//h6') as $t) {
        $livello = (int) substr($t->tagName, 1);
        if ($precedente > 0 && $livello > $precedente + 1) {
            $salti[] = sprintf(
                'h%d → h%d: %s',
                $precedente,
                $livello,
                trim(preg_replace('/\s+/', ' ', mb_substr($t->textContent, 0, 40)))
            );
        }
        $precedente = $livello;
    }
    if ($salti) {
        $problemi[] = italiawp3_a11y_esito(
            'titoli-salti', '1.3.1', 'errore',
            __('The heading hierarchy skips levels: the outline of the page becomes wrong for anyone browsing by headings.', 'italiawp3'),
            $salti
        );
    }

    // --- 1.1.1 alt sulle immagini ------------------------------------------
    $senza_alt = array();
    foreach ($xpath->query('//img[not(@alt)]') as $img) {
        $senza_alt[] = italiawp3_a11y_descrivi($img);
    }
    if ($senza_alt) {
        $problemi[] = italiawp3_a11y_esito(
            'img-alt', '1.1.1', 'errore',
            __('Images without an alt attribute. A decorative image needs alt="" (empty); an informative one needs a description.', 'italiawp3'),
            $senza_alt
        );
    }

    // --- 4.1.2 title sugli iframe ------------------------------------------
    $iframe = array();
    foreach ($xpath->query('//iframe') as $f) {
        if (trim($f->getAttribute('title')) === '') {
            $iframe[] = italiawp3_a11y_descrivi($f);
        }
    }
    if ($iframe) {
        $problemi[] = italiawp3_a11y_esito(
            'iframe-title', '4.1.2', 'errore',
            __('Frames without a title: a screen reader announces them as "frame" with no clue about the content.', 'italiawp3'),
            $iframe
        );
    }

    // --- 1.3.1 / 4.1.2 Campi di form con un nome ----------------------------
    $campi = array();
    foreach ($xpath->query('//input|//select|//textarea') as $campo) {
        $tipo = strtolower($campo->getAttribute('type'));
        if ($campo->tagName === 'input' && in_array($tipo, array('hidden', 'submit', 'button', 'reset', 'image'), true)) {
            continue;
        }
        if (trim($campo->getAttribute('aria-label')) !== '' || trim($campo->getAttribute('aria-labelledby')) !== '') {
            continue;
        }
        $id = trim($campo->getAttribute('id'));
        if ($id !== '' && $xpath->query('//label[@for="' . addslashes($id) . '"]')->length) {
            continue;
        }
        // <label><input> ...</label>: la label avvolge il campo.
        $antenato = $campo->parentNode;
        $avvolto = false;
        while ($antenato && $antenato->nodeType === XML_ELEMENT_NODE) {
            if (strtolower($antenato->nodeName) === 'label') {
                $avvolto = true;
                break;
            }
            $antenato = $antenato->parentNode;
        }
        if ($avvolto) {
            continue;
        }
        $campi[] = italiawp3_a11y_descrivi($campo);
    }
    if ($campi) {
        $problemi[] = italiawp3_a11y_esito(
            'campi-senza-label', '1.3.1', 'errore',
            __('Form fields with no associated label: they are announced without saying what to type in.', 'italiawp3'),
            $campi
        );
    }

    // --- 4.1.1 id duplicati -------------------------------------------------
    $visti = array();
    $duplicati = array();
    foreach ($xpath->query('//*[@id]') as $el) {
        $id = $el->getAttribute('id');
        if ($id === '') {
            continue;
        }
        if (isset($visti[$id])) {
            $duplicati[$id] = isset($duplicati[$id]) ? $duplicati[$id] + 1 : 2;
        } else {
            $visti[$id] = true;
        }
    }
    if ($duplicati) {
        $dettagli = array();
        foreach ($duplicati as $id => $n) {
            $dettagli[] = sprintf('id="%s" × %d', $id, $n);
        }
        $problemi[] = italiawp3_a11y_esito(
            'id-duplicati', '4.1.1', 'errore',
            __('Duplicate id attributes: label associations and aria references point to the wrong element.', 'italiawp3'),
            $dettagli
        );
    }

    // --- 1.3.1 Riferimenti aria che puntano nel vuoto ------------------------
    $orfani = array();
    foreach (array('aria-labelledby', 'aria-describedby', 'aria-controls', 'aria-owns') as $attr) {
        foreach ($xpath->query('//*[@' . $attr . ']') as $el) {
            foreach (preg_split('/\s+/', trim($el->getAttribute($attr))) as $rif) {
                if ($rif === '') {
                    continue;
                }
                if (!isset($visti[$rif])) {
                    $orfani[] = sprintf('%s="%s" → %s', $attr, $rif, italiawp3_a11y_descrivi($el));
                }
            }
        }
    }
    if ($orfani) {
        $problemi[] = italiawp3_a11y_esito(
            'aria-orfani', '1.3.1', 'errore',
            __('ARIA attributes referencing ids that do not exist in the page: the relationship is simply lost.', 'italiawp3'),
            $orfani
        );
    }

    // --- 2.4.4 / 4.1.2 Link e pulsanti con un nome --------------------------
    $senza_nome = array();
    foreach ($xpath->query('//a[@href]|//button') as $el) {
        if ($el->getAttribute('aria-hidden') === 'true' || $el->hasAttribute('hidden')) {
            continue;
        }
        if (italiawp3_a11y_nome_accessibile($el, $xpath) === '') {
            $senza_nome[] = italiawp3_a11y_descrivi($el);
        }
    }
    if ($senza_nome) {
        $problemi[] = italiawp3_a11y_esito(
            'controlli-senza-nome', '2.4.4', 'errore',
            __('Links or buttons with no accessible name: they are announced as "link" and nothing else.', 'italiawp3'),
            $senza_nome
        );
    }

    // --- 1.3.1 Tabelle dati senza intestazioni ------------------------------
    $tabelle = array();
    foreach ($xpath->query('//table') as $tab) {
        if ($xpath->query('.//tr', $tab)->length <= 1) {
            continue;                       // una riga sola: non e' una tabella di dati
        }
        if ($tab->getAttribute('role') === 'presentation' || $tab->getAttribute('role') === 'none') {
            continue;
        }
        if ($xpath->query('.//th', $tab)->length === 0) {
            $tabelle[] = italiawp3_a11y_descrivi($tab);
        }
    }
    if ($tabelle) {
        $problemi[] = italiawp3_a11y_esito(
            'tabelle-senza-th', '1.3.1', 'errore',
            __('Data tables with no <th>: a screen reader cannot tie each cell to its column or row. Usually these come from the editor, not from the theme.', 'italiawp3'),
            $tabelle
        );
    }

    // --- 1.1.1 Icone SVG esposte --------------------------------------------
    $svg = array();
    foreach ($xpath->query('//*[local-name()="svg"]') as $el) {
        if ($el->getAttribute('aria-hidden') === 'true') {
            continue;
        }
        if ($el->hasAttribute('role') && in_array($el->getAttribute('role'), array('img', 'graphics-document'), true)) {
            if (trim($el->getAttribute('aria-label')) !== '' || $xpath->query('.//*[local-name()="title"]', $el)->length) {
                continue;
            }
        }
        if ($xpath->query('.//*[local-name()="title"]', $el)->length) {
            continue;
        }
        if (trim($el->getAttribute('aria-label')) !== '') {
            continue;
        }
        $svg[] = italiawp3_a11y_descrivi($el);
    }
    if ($svg) {
        $problemi[] = italiawp3_a11y_esito(
            'svg-esposti', '1.1.1', 'errore',
            __('SVG icons exposed to assistive technology without a name: a decorative icon needs aria-hidden="true" focusable="false".', 'italiawp3'),
            $svg
        );
    }

    // --- 2.4.4 Testo del link generico (avviso, non errore) -----------------
    $generici = array('clicca qui', 'click here', 'qui', 'here', 'leggi tutto', 'read more', 'continua', 'link', 'scopri di piu', 'scopri di più');
    $vaghi = array();
    foreach ($xpath->query('//a[@href]') as $a) {
        if (trim($a->getAttribute('aria-label')) !== '' || trim($a->getAttribute('aria-labelledby')) !== '') {
            continue;                       // il nome accessibile e' gia' piu' esplicito del testo
        }
        $testo = mb_strtolower(trim(preg_replace('/\s+/', ' ', $a->textContent)));
        $testo = rtrim($testo, " \t\n\r\0\x0B.:!?→>»");
        if ($testo !== '' && in_array($testo, $generici, true)) {
            $vaghi[] = italiawp3_a11y_descrivi($a);
        }
    }
    if ($vaghi) {
        $problemi[] = italiawp3_a11y_esito(
            'link-generici', '2.4.4', 'avviso',
            __('Links whose text does not say where they lead. Read out of context — as a screen reader user listing the links does — they mean nothing. An aria-label with the destination title is enough.', 'italiawp3'),
            $vaghi
        );
    }

    return $problemi;
}

/**
 * Analizza un elenco di URL.
 *
 * @param string[] $urls
 * @return array Mappa url => array{ok, errore, problemi}
 */
function italiawp3_a11y_analizza_urls($urls) {
    $risultati = array();

    foreach ($urls as $url) {
        $pagina = italiawp3_a11y_scarica($url);
        if (!$pagina['ok']) {
            $risultati[$url] = array('ok' => false, 'errore' => $pagina['errore'], 'problemi' => array());
            continue;
        }
        $risultati[$url] = array('ok' => true, 'errore' => '', 'problemi' => italiawp3_a11y_analizza($pagina['html']));
    }

    return $risultati;
}
