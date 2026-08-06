<?php
/**
 * Checklist delle verifiche manuali.
 *
 * Sono i due terzi della metodologia AgID che nessuno strumento automatico puo'
 * misurare: la navigazione da tastiera e l'ascolto con uno screen reader. Nessun
 * software sa dire se l'ordine di tabulazione e' logico, se un menu e' una trappola
 * per il focus, o se una pagina letta ad occhi chiusi ha senso.
 *
 * Registrarne l'esito con una data non e' burocrazia: la dichiarazione di
 * accessibilita' su form.agid.gov.it va rinnovata ogni anno e deve poggiare su una
 * verifica tracciabile. Questa e' quella traccia.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Nome dell'option che conserva gli esiti.
 */
if (!defined('italiawp3_A11Y_CHECKLIST_OPTION')) {
    define('italiawp3_A11Y_CHECKLIST_OPTION', 'italiawp3_a11y_checklist');
}

/**
 * Le voci da verificare a mano, raggruppate.
 *
 * @return array
 */
function italiawp3_a11y_voci_manuali() {
    return array(
        'tastiera' => array(
            'etichetta' => __('Keyboard navigation', 'italiawp3'),
            'nota' => __('Put the mouse away. Go through the whole page using only Tab, Shift+Tab, Enter and the arrow keys.', 'italiawp3'),
            'voci' => array(
                'focus-visibile' => array(
                    'criterio' => '2.4.7',
                    'testo' => __('The focus indicator is always clearly visible, on every background, including over images and coloured areas.', 'italiawp3'),
                ),
                'ordine-logico' => array(
                    'criterio' => '2.4.3',
                    'testo' => __('The tab order follows the visual reading order, with no jumps backwards.', 'italiawp3'),
                ),
                'nessuna-trappola' => array(
                    'criterio' => '2.1.2',
                    'testo' => __('No keyboard trap: every menu, dialog and player can be left with the keyboard alone.', 'italiawp3'),
                ),
                'tutto-raggiungibile' => array(
                    'criterio' => '2.1.1',
                    'testo' => __('Everything that works with the mouse also works with the keyboard, including carousels, lightboxes and maps.', 'italiawp3'),
                ),
                'skiplink' => array(
                    'criterio' => '2.4.1',
                    'testo' => __('The skip links appear on the first Tab and actually move the focus where they promise.', 'italiawp3'),
                ),
            ),
        ),
        'screen-reader' => array(
            'etichetta' => __('Screen reader', 'italiawp3'),
            'nota' => __('NVDA, VoiceOver or JAWS. Listen to the page with your eyes closed and check that the experience holds together.', 'italiawp3'),
            'voci' => array(
                'menu-annunciato' => array(
                    'criterio' => '4.1.2',
                    'testo' => __('Menus and accordions announce their open/closed state, and it changes when they are operated.', 'italiawp3'),
                ),
                'form-errori' => array(
                    'criterio' => '3.3.1',
                    'testo' => __('Form validation errors are announced, not just shown in red.', 'italiawp3'),
                ),
                'modali' => array(
                    'criterio' => '4.1.2',
                    'testo' => __('Dialogs are announced as such, take the focus and give it back when closed.', 'italiawp3'),
                ),
                'lettura-coerente' => array(
                    'criterio' => '1.3.2',
                    'testo' => __('The reading order makes sense: the page can be understood without seeing it.', 'italiawp3'),
                ),
                'immagini-utili' => array(
                    'criterio' => '1.1.1',
                    'testo' => __('The alternative texts say something useful: not "image", but what the image shows or does.', 'italiawp3'),
                ),
            ),
        ),
        'contenuti' => array(
            'etichetta' => __('Media and documents', 'italiawp3'),
            'nota' => __('These live in the content, not in the theme, and they are the part most often forgotten.', 'italiawp3'),
            'voci' => array(
                'video-sottotitoli' => array(
                    'criterio' => '1.2.2',
                    'testo' => __('Videos have captions; audio content has a transcript.', 'italiawp3'),
                ),
                'pdf-accessibili' => array(
                    'criterio' => '1.3.1',
                    'testo' => __('The published PDFs have been checked with PAC or Acrobat Pro, not only with the automatic scan on this page.', 'italiawp3'),
                ),
                'zoom-200' => array(
                    'criterio' => '1.4.4',
                    'testo' => __('At 200% zoom nothing is lost and nothing overlaps.', 'italiawp3'),
                ),
                'servizi-esterni' => array(
                    'criterio' => '—',
                    'testo' => __('SPID/CIE login, pagoPA payments and any plugin forms have been tested separately: they are not part of the theme.', 'italiawp3'),
                ),
            ),
        ),
    );
}

/**
 * Gli stati possibili di una voce.
 *
 * @return array
 */
function italiawp3_a11y_stati() {
    return array(
        'non-verificato' => __('Not checked', 'italiawp3'),
        'conforme'       => __('Compliant', 'italiawp3'),
        'non-conforme'   => __('Not compliant', 'italiawp3'),
        'non-applicabile'=> __('Not applicable', 'italiawp3'),
    );
}

/**
 * Legge gli esiti registrati.
 *
 * @return array
 */
function italiawp3_a11y_checklist_esiti() {
    $esiti = get_option(italiawp3_A11Y_CHECKLIST_OPTION, array());

    return is_array($esiti) ? $esiti : array();
}

/**
 * Salva l'esito di una voce, con data e autore.
 *
 * @param string $voce
 * @param string $stato
 * @param string $note
 * @return array L'esito salvato.
 */
function italiawp3_a11y_checklist_salva($voce, $stato, $note) {
    $stati = italiawp3_a11y_stati();
    if (!isset($stati[$stato])) {
        $stato = 'non-verificato';
    }

    $esiti = italiawp3_a11y_checklist_esiti();
    $utente = wp_get_current_user();

    $esiti[$voce] = array(
        'stato'  => $stato,
        'note'   => sanitize_textarea_field($note),
        'data'   => current_time('mysql'),
        'utente' => $utente ? $utente->display_name : '',
    );

    update_option(italiawp3_A11Y_CHECKLIST_OPTION, $esiti, false);

    return $esiti[$voce];
}

/**
 * Conteggio sintetico della checklist.
 *
 * @return array{totale:int,verificate:int,conformi:int,non_conformi:int}
 */
function italiawp3_a11y_checklist_conteggio() {
    $esiti = italiawp3_a11y_checklist_esiti();
    $totale = 0;
    $verificate = 0;
    $conformi = 0;
    $non_conformi = 0;

    foreach (italiawp3_a11y_voci_manuali() as $gruppo) {
        foreach ($gruppo['voci'] as $id => $voce) {
            $totale++;
            $stato = isset($esiti[$id]['stato']) ? $esiti[$id]['stato'] : 'non-verificato';
            if ($stato !== 'non-verificato') {
                $verificate++;
            }
            if ($stato === 'conforme') {
                $conformi++;
            }
            if ($stato === 'non-conforme') {
                $non_conformi++;
            }
        }
    }

    return compact('totale', 'verificate', 'conformi', 'non_conformi');
}
