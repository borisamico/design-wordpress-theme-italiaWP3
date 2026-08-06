<?php
/**
 * Resa della pagina «Accessibilita».
 *
 * Separata da dashboard.php, che si occupa di registrazione, permessi e azioni:
 * qui c'e' solo markup.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Stampa il riquadro con il conteggio degli esiti automatici.
 *
 * @param array $esiti
 */
function italiawp3_a11y_riepilogo_html($esiti) {
    $errori = 0;
    $avvisi = 0;
    $pagine = isset($esiti['pagine']) ? $esiti['pagine'] : array();

    foreach ($pagine as $pagina) {
        foreach ($pagina['problemi'] as $p) {
            if ($p['gravita'] === 'errore') {
                $errori++;
            } else {
                $avvisi++;
            }
        }
    }

    return array('errori' => $errori, 'avvisi' => $avvisi, 'pagine' => count($pagine));
}

/**
 * La pagina.
 */
function italiawp3_a11y_pagina() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Not allowed.', 'italiawp3'));
    }

    $campione        = italiawp3_a11y_campione();
    $esiti_html      = get_option(italiawp3_A11Y_OPT_ESITI_HTML, array());
    $esiti_contenuti = get_option(italiawp3_A11Y_OPT_ESITI_CONTENUTI, array());
    $esiti_resa      = get_option(italiawp3_A11Y_OPT_ESITI_RESA, array());
    $riepilogo       = italiawp3_a11y_riepilogo_html($esiti_html);
    $conteggio       = italiawp3_a11y_checklist_conteggio();
    $bi              = italiawp3_a11y_stato_bootstrap_italia();
    $stati           = italiawp3_a11y_stati();
    $esiti_checklist = italiawp3_a11y_checklist_esiti();
    ?>
    <div class="wrap italiawp3-a11y">
        <h1><?php esc_html_e('Accessibility', 'italiawp3'); ?></h1>

        <div class="notice notice-info inline" style="margin:16px 0;padding:12px">
            <p style="margin-top:0"><strong><?php esc_html_e('What this page can and cannot tell you', 'italiawp3'); ?></strong></p>
            <p>
                <?php esc_html_e('Automatic checks catch roughly one third of accessibility problems. The other two thirds — keyboard navigation and screen reader use — cannot be measured by any tool, this one included. They are listed further down as manual checks and have to be done by a person.', 'italiawp3'); ?>
            </p>
            <p style="margin-bottom:0">
                <?php
                printf(
                    /* translators: %s: form.agid.gov.it link. */
                    esc_html__('An empty report here does not mean the site is compliant. The accessibility statement published on %s stays the responsibility of the public body, and has to be renewed every year.', 'italiawp3'),
                    '<a href="https://form.agid.gov.it" target="_blank" rel="noopener noreferrer">form.agid.gov.it</a>'
                );
                ?>
            </p>
        </div>

        <h2 class="screen-reader-text"><?php esc_html_e('Summary', 'italiawp3'); ?></h2>
        <div style="display:flex;gap:12px;flex-wrap:wrap;margin:16px 0">
            <?php
            $tessere = array(
                array(
                    'etichetta' => __('Errors found in the markup', 'italiawp3'),
                    'valore'    => $riepilogo['errori'],
                    'nota'      => sprintf(
                        /* translators: %d: number of pages. */
                        _n('on %d page examined', 'on %d pages examined', $riepilogo['pagine'], 'italiawp3'),
                        $riepilogo['pagine']
                    ),
                ),
                array(
                    'etichetta' => __('Warnings', 'italiawp3'),
                    'valore'    => $riepilogo['avvisi'],
                    'nota'      => __('worth a look, not necessarily a failure', 'italiawp3'),
                ),
                array(
                    'etichetta' => __('Findings in the content', 'italiawp3'),
                    'valore'    => isset($esiti_contenuti['rilievi']) ? count($esiti_contenuti['rilievi']) : 0,
                    'nota'      => __('images, tables, headings, PDFs', 'italiawp3'),
                ),
                array(
                    'etichetta' => __('Manual checks done', 'italiawp3'),
                    'valore'    => $conteggio['verificate'] . '/' . $conteggio['totale'],
                    'nota'      => $conteggio['non_conformi']
                        ? sprintf(
                            /* translators: %d: number of failed checks. */
                            _n('%d not compliant', '%d not compliant', $conteggio['non_conformi'], 'italiawp3'),
                            $conteggio['non_conformi']
                        )
                        : __('the part no tool can measure', 'italiawp3'),
                ),
            );
            foreach ($tessere as $t) :
                ?>
                <div class="card" style="flex:1 1 200px;min-width:200px;margin:0;padding:12px 16px">
                    <div style="font-size:28px;font-weight:600;line-height:1.2"><?php echo esc_html($t['valore']); ?></div>
                    <div style="font-weight:600"><?php echo esc_html($t['etichetta']); ?></div>
                    <div style="color:#646970"><?php echo esc_html($t['nota']); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <hr>

        <h2><?php esc_html_e('1. Markup of the public pages', 'italiawp3'); ?></h2>
        <p class="description">
            <?php esc_html_e('The pages are fetched from the site itself, logged out, and their markup is inspected: language, heading structure, alt text, labels, duplicate ids, aria references, tables, icons.', 'italiawp3'); ?>
        </p>

        <p>
            <button type="button" class="button button-primary" id="iwp-a11y-avvia-html">
                <?php esc_html_e('Run the markup check', 'italiawp3'); ?>
            </button>
            <span id="iwp-a11y-stato-html" style="margin-left:8px"></span>
        </p>

        <div id="iwp-a11y-esiti-html">
            <?php if (empty($esiti_html['pagine'])) : ?>
                <p><em><?php esc_html_e('Never run yet.', 'italiawp3'); ?></em></p>
            <?php else : ?>
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: date and time. */
                        esc_html__('Last run: %s', 'italiawp3'),
                        esc_html(mysql2date('j F Y, H:i', $esiti_html['data']))
                    );
                    ?>
                </p>
                <?php foreach ($esiti_html['pagine'] as $url => $pagina) : ?>
                    <h3 style="margin-bottom:4px">
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html(str_replace(home_url(), '', $url) ?: '/'); ?>
                        </a>
                    </h3>
                    <?php if (!$pagina['ok']) : ?>
                        <p class="notice notice-error inline" style="padding:8px">
                            <?php
                            printf(
                                /* translators: %s: error message. */
                                esc_html__('Page not readable: %s', 'italiawp3'),
                                esc_html($pagina['errore'])
                            );
                            ?>
                        </p>
                    <?php elseif (empty($pagina['problemi'])) : ?>
                        <p style="color:#00694e">✓ <?php esc_html_e('No problem found by the automatic checks on this page.', 'italiawp3'); ?></p>
                    <?php else : ?>
                        <table class="widefat striped" style="margin-bottom:16px">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:90px"><?php esc_html_e('Severity', 'italiawp3'); ?></th>
                                    <th scope="col" style="width:70px"><?php esc_html_e('WCAG', 'italiawp3'); ?></th>
                                    <th scope="col"><?php esc_html_e('Finding', 'italiawp3'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pagina['problemi'] as $p) : ?>
                                    <tr>
                                        <td>
                                            <?php if ($p['gravita'] === 'errore') : ?>
                                                <strong style="color:#b32d2e"><?php esc_html_e('Error', 'italiawp3'); ?></strong>
                                            <?php else : ?>
                                                <span style="color:#996800"><?php esc_html_e('Warning', 'italiawp3'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><code><?php echo esc_html($p['criterio']); ?></code></td>
                                        <td>
                                            <?php echo esc_html($p['messaggio']); ?>
                                            <?php if (!empty($p['dettagli'])) : ?>
                                                <ul style="margin:6px 0 0 0">
                                                    <?php foreach ($p['dettagli'] as $d) : ?>
                                                        <li><code style="font-size:11px"><?php echo esc_html($d); ?></code></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                                <?php if ($p['totale'] > count($p['dettagli'])) : ?>
                                                    <p style="margin:4px 0 0;color:#646970">
                                                        <?php
                                                        printf(
                                                            /* translators: %d: number of further occurrences. */
                                                            esc_html__('…and %d more.', 'italiawp3'),
                                                            (int) ($p['totale'] - count($p['dettagli']))
                                                        );
                                                        ?>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <hr>

        <h2><?php esc_html_e('2. Contrast, focus and reflow on the rendered page', 'italiawp3'); ?></h2>
        <p class="description">
            <?php esc_html_e('These depend on the computed styles, which PHP cannot see. The pages are loaded in a frame on this same site and measured directly in the browser: real contrast ratios, focus outline width and contrast, and horizontal scrolling at 320px. Nothing leaves the site.', 'italiawp3'); ?>
        </p>

        <p>
            <button type="button" class="button button-primary" id="iwp-a11y-avvia-resa">
                <?php esc_html_e('Run the rendered check', 'italiawp3'); ?>
            </button>
            <span id="iwp-a11y-stato-resa" style="margin-left:8px"></span>
        </p>

        <div id="iwp-a11y-esiti-resa">
            <?php if (empty($esiti_resa['pagine'])) : ?>
                <p><em><?php esc_html_e('Never run yet.', 'italiawp3'); ?></em></p>
            <?php else : ?>
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: date and time. */
                        esc_html__('Last run: %s', 'italiawp3'),
                        esc_html(mysql2date('j F Y, H:i', $esiti_resa['data']))
                    );
                    ?>
                </p>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e('Page', 'italiawp3'); ?></th>
                            <th scope="col"><?php esc_html_e('Contrast', 'italiawp3'); ?></th>
                            <th scope="col"><?php esc_html_e('Focus', 'italiawp3'); ?></th>
                            <th scope="col"><?php esc_html_e('Reflow at 320px', 'italiawp3'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($esiti_resa['pagine'] as $url => $dati) : ?>
                            <tr>
                                <td><?php echo esc_html(str_replace(home_url(), '', $url) ?: '/'); ?></td>
                                <td>
                                    <?php if (empty($dati['contrasto'])) : ?>
                                        <span style="color:#00694e">✓</span>
                                    <?php else : ?>
                                        <ul style="margin:0"><?php foreach ($dati['contrasto'] as $c) : ?><li><code style="font-size:11px"><?php echo esc_html($c); ?></code></li><?php endforeach; ?></ul>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (empty($dati['focus'])) : ?>
                                        <span style="color:#00694e">✓</span>
                                    <?php else : ?>
                                        <ul style="margin:0"><?php foreach ($dati['focus'] as $f) : ?><li><code style="font-size:11px"><?php echo esc_html($f); ?></code></li><?php endforeach; ?></ul>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($dati['reflow']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div id="iwp-a11y-culla" style="position:absolute;left:-99999px;top:0" aria-hidden="true"></div>

        <hr>

        <h2><?php esc_html_e('3. Content, images and PDFs', 'italiawp3'); ?></h2>
        <p class="description">
            <?php esc_html_e('This is the part that lives in the content, not in the theme, and it is the one most often forgotten. The whole database is scanned in batches.', 'italiawp3'); ?>
        </p>

        <p>
            <button type="button" class="button button-primary" id="iwp-a11y-avvia-contenuti">
                <?php esc_html_e('Scan the content', 'italiawp3'); ?>
            </button>
            <span id="iwp-a11y-stato-contenuti" style="margin-left:8px"></span>
        </p>

        <div id="iwp-a11y-esiti-contenuti">
            <?php if (empty($esiti_contenuti['rilievi'])) : ?>
                <p><em><?php esc_html_e('Never run yet, or nothing found.', 'italiawp3'); ?></em></p>
            <?php else : ?>
                <p class="description">
                    <?php
                    printf(
                        /* translators: %s: date and time. */
                        esc_html__('Last run: %s', 'italiawp3'),
                        esc_html(mysql2date('j F Y, H:i', $esiti_contenuti['data']))
                    );
                    ?>
                </p>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th scope="col" style="width:80px"><?php esc_html_e('Severity', 'italiawp3'); ?></th>
                            <th scope="col" style="width:70px"><?php esc_html_e('WCAG', 'italiawp3'); ?></th>
                            <th scope="col" style="width:220px"><?php esc_html_e('Content', 'italiawp3'); ?></th>
                            <th scope="col"><?php esc_html_e('Finding', 'italiawp3'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($esiti_contenuti['rilievi'], 0, 300) as $r) : ?>
                            <tr>
                                <td>
                                    <?php if ($r['gravita'] === 'errore') : ?>
                                        <strong style="color:#b32d2e"><?php esc_html_e('Error', 'italiawp3'); ?></strong>
                                    <?php else : ?>
                                        <span style="color:#996800"><?php esc_html_e('Warning', 'italiawp3'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo esc_html($r['criterio']); ?></code></td>
                                <td>
                                    <?php if ($r['modifica']) : ?>
                                        <a href="<?php echo esc_url($r['modifica']); ?>"><?php echo esc_html($r['titolo'] ?: '#' . $r['post_id']); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($r['titolo'] ?: '#' . $r['post_id']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html($r['messaggio']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($esiti_contenuti['rilievi']) > 300) : ?>
                    <p class="description">
                        <?php
                        printf(
                            /* translators: %d: number of further findings. */
                            esc_html__('…and %d more findings, not shown here.', 'italiawp3'),
                            (int) (count($esiti_contenuti['rilievi']) - 300)
                        );
                        ?>
                    </p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <hr>

        <h2><?php esc_html_e('4. Manual checks', 'italiawp3'); ?></h2>
        <p class="description">
            <?php esc_html_e('No tool can do these. Record who checked what and when: this is the evidence the accessibility statement rests on.', 'italiawp3'); ?>
        </p>

        <?php foreach (italiawp3_a11y_voci_manuali() as $gruppo_id => $gruppo) : ?>
            <h3><?php echo esc_html($gruppo['etichetta']); ?></h3>
            <p class="description"><?php echo esc_html($gruppo['nota']); ?></p>
            <table class="widefat striped" style="margin-bottom:20px">
                <thead>
                    <tr>
                        <th scope="col" style="width:70px"><?php esc_html_e('WCAG', 'italiawp3'); ?></th>
                        <th scope="col"><?php esc_html_e('What to check', 'italiawp3'); ?></th>
                        <th scope="col" style="width:150px"><?php esc_html_e('Outcome', 'italiawp3'); ?></th>
                        <th scope="col" style="width:220px"><?php esc_html_e('Notes', 'italiawp3'); ?></th>
                        <th scope="col" style="width:160px"><?php esc_html_e('Checked on', 'italiawp3'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gruppo['voci'] as $voce_id => $voce) :
                        $esito = isset($esiti_checklist[$voce_id]) ? $esiti_checklist[$voce_id] : array();
                        $stato = isset($esito['stato']) ? $esito['stato'] : 'non-verificato';
                        ?>
                        <tr data-voce="<?php echo esc_attr($voce_id); ?>">
                            <td><code><?php echo esc_html($voce['criterio']); ?></code></td>
                            <td><?php echo esc_html($voce['testo']); ?></td>
                            <td>
                                <label class="screen-reader-text" for="stato-<?php echo esc_attr($voce_id); ?>">
                                    <?php esc_html_e('Outcome', 'italiawp3'); ?>
                                </label>
                                <select id="stato-<?php echo esc_attr($voce_id); ?>" class="iwp-a11y-stato">
                                    <?php foreach ($stati as $valore => $etichetta) : ?>
                                        <option value="<?php echo esc_attr($valore); ?>" <?php selected($stato, $valore); ?>>
                                            <?php echo esc_html($etichetta); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <label class="screen-reader-text" for="note-<?php echo esc_attr($voce_id); ?>">
                                    <?php esc_html_e('Notes', 'italiawp3'); ?>
                                </label>
                                <input type="text" id="note-<?php echo esc_attr($voce_id); ?>" class="iwp-a11y-note regular-text"
                                       style="width:100%"
                                       value="<?php echo esc_attr(isset($esito['note']) ? $esito['note'] : ''); ?>">
                            </td>
                            <td class="iwp-a11y-data">
                                <?php if (!empty($esito['data'])) : ?>
                                    <?php echo esc_html(mysql2date('j/n/Y H:i', $esito['data'])); ?>
                                    <?php if (!empty($esito['utente'])) : ?>
                                        <br><span style="color:#646970"><?php echo esc_html($esito['utente']); ?></span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span style="color:#646970">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>

        <hr>

        <h2><?php esc_html_e('5. Sample of pages', 'italiawp3'); ?></h2>
        <p class="description">
            <?php esc_html_e('The AgID methodology asks for at least one page per template. These are the ones the theme itself distinguishes; add any layout your site introduces, one URL per line.', 'italiawp3'); ?>
        </p>
        <form method="post" action="">
            <?php wp_nonce_field('italiawp3_a11y_campione', 'italiawp3_a11y_campione_nonce'); ?>
            <textarea name="campione" rows="8" style="width:100%;max-width:820px;font-family:monospace"><?php
                echo esc_textarea(implode("\n", $campione));
            ?></textarea>
            <p>
                <button type="submit" class="button"><?php esc_html_e('Save the sample', 'italiawp3'); ?></button>
                <span class="description" style="margin-left:8px">
                    <?php esc_html_e('Only URLs on this site are accepted.', 'italiawp3'); ?>
                </span>
            </p>
        </form>

        <hr>

        <h2><?php esc_html_e('6. Bootstrap Italia', 'italiawp3'); ?></h2>
        <table class="widefat striped" style="max-width:820px">
            <tbody>
                <?php foreach ($bi['versioni'] as $dove => $versione) : ?>
                    <tr>
                        <td style="width:60%"><code><?php echo esc_html($dove); ?></code></td>
                        <td><?php echo $versione === null
                            ? '<em>' . esc_html__('not found', 'italiawp3') . '</em>'
                            : esc_html($versione); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong><?php esc_html_e('Aligned', 'italiawp3'); ?></strong></td>
                    <td>
                        <?php if ($bi['allineate']) : ?>
                            <span style="color:#00694e">✓ <?php esc_html_e('yes', 'italiawp3'); ?></span>
                        <?php else : ?>
                            <strong style="color:#b32d2e"><?php esc_html_e('no — the cache buster is out of step with the assets', 'italiawp3'); ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e('Latest release published', 'italiawp3'); ?></strong></td>
                    <td>
                        <?php if ($bi['ultima']) : ?>
                            <?php echo esc_html($bi['ultima']); ?>
                        <?php else : ?>
                            <em><?php esc_html_e('could not be checked', 'italiawp3'); ?></em>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="description" style="max-width:820px">
            <?php esc_html_e('Read only, on purpose. The Bootstrap Italia assets travel inside the theme releases: the way to update them is to update the theme, which does it by itself. Downloading and overwriting files from here would need a writable theme folder, would run remote code without a checksum, and would be overwritten at the next theme update anyway.', 'italiawp3'); ?>
        </p>
    </div>
    <?php
}
