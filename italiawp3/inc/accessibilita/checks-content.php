<?php
/**
 * Scansione dei contenuti dal database.
 *
 * E' la parte che una dashboard dentro WordPress fa meglio di qualunque scanner
 * esterno: ha il database, quindi non deve indovinare l'elenco delle pagine ne'
 * limitarsi a un campione. Copre le voci della metodologia AgID che dipendono da chi
 * redige, non dal tema: alt delle immagini caricate, tabelle senza intestazioni,
 * secondo <h1> scritto nell'editor, PDF allegati.
 *
 * VINCOLO: si lavora a lotti. Un sito con qualche migliaio di contenuti va in timeout
 * se lo si scandisce in una richiesta sola, e il tema ha gia' pagato una volta il
 * prezzo di una query non indicizzata su wp_postmeta ripetuta per ogni immagine.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Quanti elementi per lotto. Tenuto basso di proposito: meglio piu' giri brevi che
 * una richiesta che muore a meta'.
 */
if (!defined('italiawp3_A11Y_LOTTO')) {
    define('italiawp3_A11Y_LOTTO', 20);
}

/**
 * Un rilievo su un contenuto.
 *
 * @param int    $post_id
 * @param string $tipo
 * @param string $criterio
 * @param string $gravita
 * @param string $messaggio
 * @return array
 */
function italiawp3_a11y_rilievo($post_id, $tipo, $criterio, $gravita, $messaggio) {
    return array(
        'post_id'   => (int) $post_id,
        'titolo'    => get_the_title($post_id),
        'modifica'  => get_edit_post_link($post_id, 'raw'),
        'tipo'      => $tipo,
        'criterio'  => $criterio,
        'gravita'   => $gravita,
        'messaggio' => $messaggio,
    );
}

/**
 * Analizza il contenuto di un singolo post.
 *
 * @param WP_Post $post
 * @return array Rilievi.
 */
function italiawp3_a11y_analizza_contenuto($post) {
    $rilievi = array();
    $contenuto = (string) $post->post_content;

    if (trim($contenuto) === '') {
        return $rilievi;
    }

    // Un <h1> nel contenuto: il titolo della pagina e' gia' l'h1, questo e' il secondo.
    if (preg_match('/<h1[\s>]/i', $contenuto)) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $post->ID, 'h1-nel-contenuto', '1.3.1', 'errore',
            __('The content contains an <h1>. The page title is already the h1: use <h2> for sections inside the text.', 'italiawp3')
        );
    }

    // Immagini senza attributo alt scritte a mano nell'editor.
    if (preg_match_all('/<img\b[^>]*>/i', $contenuto, $m)) {
        $senza = 0;
        foreach ($m[0] as $tag) {
            if (!preg_match('/\salt\s*=/i', $tag)) {
                $senza++;
            }
        }
        if ($senza) {
            $rilievi[] = italiawp3_a11y_rilievo(
                $post->ID, 'img-senza-alt', '1.1.1', 'errore',
                sprintf(
                    /* translators: %d: number of images. */
                    _n(
                        '%d image in the content has no alt attribute.',
                        '%d images in the content have no alt attribute.',
                        $senza,
                        'italiawp3'
                    ),
                    $senza
                )
            );
        }
    }

    // Tabelle di dati senza intestazioni.
    if (preg_match_all('/<table\b.*?<\/table>/is', $contenuto, $m)) {
        $senza = 0;
        foreach ($m[0] as $tab) {
            $righe = preg_match_all('/<tr[\s>]/i', $tab);
            if ($righe > 1 && !preg_match('/<th[\s>]/i', $tab)) {
                $senza++;
            }
        }
        if ($senza) {
            $rilievi[] = italiawp3_a11y_rilievo(
                $post->ID, 'tabella-senza-th', '1.3.1', 'errore',
                sprintf(
                    /* translators: %d: number of tables. */
                    _n(
                        '%d data table has no header cells (<th>).',
                        '%d data tables have no header cells (<th>).',
                        $senza,
                        'italiawp3'
                    ),
                    $senza
                )
            );
        }
    }

    // Testo del link che non dice dove porta.
    if (preg_match_all('/<a\b[^>]*>(.*?)<\/a>/is', $contenuto, $m)) {
        $generici = array('clicca qui', 'click here', 'qui', 'here', 'leggi tutto', 'read more', 'continua', 'link', 'scopri di più', 'scarica');
        $vaghi = 0;
        foreach ($m[1] as $i => $inner) {
            if (preg_match('/aria-label\s*=/i', $m[0][$i])) {
                continue;
            }
            $testo = mb_strtolower(trim(preg_replace('/\s+/', ' ', wp_strip_all_tags($inner))));
            $testo = rtrim($testo, " \t\n\r.:!?→>»");
            if ($testo !== '' && in_array($testo, $generici, true)) {
                $vaghi++;
            }
        }
        if ($vaghi) {
            $rilievi[] = italiawp3_a11y_rilievo(
                $post->ID, 'link-generico', '2.4.4', 'avviso',
                sprintf(
                    /* translators: %d: number of links. */
                    _n(
                        '%d link whose text does not say where it leads.',
                        '%d links whose text does not say where they lead.',
                        $vaghi,
                        'italiawp3'
                    ),
                    $vaghi
                )
            );
        }
    }

    return $rilievi;
}

/**
 * Esamina un PDF allegato.
 *
 * Sono indizi, non una verifica: la conformita' di un PDF si accerta con PAC o
 * Acrobat Pro. Qui si intercetta il caso frequente e grossolano — il documento non
 * taggato, la scansione senza testo, il titolo mancante — che da solo copre la
 * maggior parte dei PDF pubblicati male.
 *
 * @param WP_Post $allegato
 * @return array Rilievi.
 */
function italiawp3_a11y_analizza_pdf($allegato) {
    $rilievi = array();
    $percorso = get_attached_file($allegato->ID);

    if (!$percorso || !is_readable($percorso)) {
        return $rilievi;
    }

    $dimensione = filesize($percorso);
    if ($dimensione === false || $dimensione > 20 * MB_IN_BYTES) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $allegato->ID, 'pdf-non-esaminato', '—', 'avviso',
            __('PDF too large to be inspected here: check it with PAC or Acrobat Pro.', 'italiawp3')
        );
        return $rilievi;
    }

    $dati = file_get_contents($percorso);
    if ($dati === false) {
        return $rilievi;
    }

    // Documento taggato: senza StructTreeRoot non esiste struttura da leggere.
    if (strpos($dati, '/StructTreeRoot') === false) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $allegato->ID, 'pdf-non-taggato', '1.3.1', 'errore',
            __('PDF with no structure tags: headings, lists and reading order do not exist for a screen reader.', 'italiawp3')
        );
    }

    // Nessun font e almeno un'immagine: quasi certamente una scansione.
    if (strpos($dati, '/Font') === false && strpos($dati, '/Image') !== false) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $allegato->ID, 'pdf-scansione', '1.4.5', 'errore',
            __('PDF that looks like a scan: no text, only images. It cannot be read, searched or enlarged.', 'italiawp3')
        );
    }

    // Titolo del documento: e' cio' che l'utente sente all'apertura.
    if (!preg_match('/\/Title\s*\(\s*\S/', $dati) && strpos($dati, '<dc:title>') === false) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $allegato->ID, 'pdf-senza-titolo', '2.4.2', 'avviso',
            __('PDF with no document title in its properties.', 'italiawp3')
        );
    }

    // Lingua del documento.
    if (strpos($dati, '/Lang') === false) {
        $rilievi[] = italiawp3_a11y_rilievo(
            $allegato->ID, 'pdf-senza-lingua', '3.1.1', 'avviso',
            __('PDF with no declared language.', 'italiawp3')
        );
    }

    return $rilievi;
}

/**
 * Esegue un lotto della scansione.
 *
 * @param string $fase   'contenuti' | 'immagini' | 'pdf'
 * @param int    $offset
 * @return array{fase:string,offset:int,totale:int,finita:bool,rilievi:array}
 */
function italiawp3_a11y_lotto($fase, $offset) {
    $lotto = italiawp3_A11Y_LOTTO;
    $rilievi = array();

    if ($fase === 'contenuti') {
        $q = new WP_Query(array(
            'post_type'      => 'any',
            'post_status'    => 'publish',
            'posts_per_page' => $lotto,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'no_found_rows'  => false,        // qui il totale serve per la barra
            'cache_results'  => false,
        ));
        foreach ($q->posts as $post) {
            if ($post->post_type === 'attachment') {
                continue;
            }
            $rilievi = array_merge($rilievi, italiawp3_a11y_analizza_contenuto($post));
        }
        $totale = (int) $q->found_posts;
        $nuovo  = $offset + $lotto;

        return array(
            'fase' => 'contenuti', 'offset' => $nuovo, 'totale' => $totale,
            'finita' => $nuovo >= $totale, 'rilievi' => $rilievi,
        );
    }

    if ($fase === 'immagini') {
        $q = new WP_Query(array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => $lotto,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'cache_results'  => false,
        ));
        foreach ($q->posts as $img) {
            $alt = get_post_meta($img->ID, '_wp_attachment_image_alt', true);
            if (trim((string) $alt) === '') {
                $rilievi[] = italiawp3_a11y_rilievo(
                    $img->ID, 'immagine-senza-alt', '1.1.1', 'avviso',
                    __('Image in the library with no alternative text. If it is decorative it is fine; if it carries information, it must be described.', 'italiawp3')
                );
            }
        }
        $totale = (int) $q->found_posts;
        $nuovo  = $offset + $lotto;

        return array(
            'fase' => 'immagini', 'offset' => $nuovo, 'totale' => $totale,
            'finita' => $nuovo >= $totale, 'rilievi' => $rilievi,
        );
    }

    // PDF: piu' pesanti, si leggono da disco. Lotto ridotto.
    $lotto_pdf = max(3, (int) ($lotto / 4));
    $q = new WP_Query(array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => $lotto_pdf,
        'offset'         => $offset,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'cache_results'  => false,
    ));
    foreach ($q->posts as $pdf) {
        $rilievi = array_merge($rilievi, italiawp3_a11y_analizza_pdf($pdf));
    }
    $totale = (int) $q->found_posts;
    $nuovo  = $offset + $lotto_pdf;

    return array(
        'fase' => 'pdf', 'offset' => $nuovo, 'totale' => $totale,
        'finita' => $nuovo >= $totale, 'rilievi' => $rilievi,
    );
}
