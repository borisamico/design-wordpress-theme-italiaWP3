<?php

//Custom Settings
add_action('admin_menu', 'italiawp3_add_custom_interface');
function italiawp3_add_custom_interface() {
    add_menu_page('Dettagli', 'Dettagli', 'manage_options', 'functions', 'italiawp3_edit_custom_settings');
    
    /* Per inserire la voce "Dettagli" nel menu Aspetto */
    //add_theme_page('italiawp3 - Dettagli', 'italiawp3 - Dettagli', 'edit_theme_options', 'functions', 'italiawp3_edit_custom_settings');
}

function italiawp3_edit_custom_settings() { ?>
    <div>
        <h2>Informazioni sull'Ente</h2>
        <form method="post" action="options.php">
            <?php wp_nonce_field('update-options') ?>
            
            <br><h3>Altro</h3><hr>
            
            <p><label for="dettagli-link-accessibilita"><strong>Link alla "Dichiarazione di accessibilità AGID" (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-link-accessibilita" name="dettagli-link-accessibilita" size="100" value="<?php echo esc_attr(get_option('dettagli-link-accessibilita')); ?>" /></p>
            
            <p><label for="custom-meta-keywords"><strong>Meta "keywords":</strong></label><br />
                <input type="text" id="custom-meta-keywords" name="custom-meta-keywords" size="100" value="<?php echo esc_attr(get_option('custom-meta-keywords')); ?>" /></p>
            
            <p><label for="custom-meta-description"><strong>Meta "description" (personalizzato):</strong></label><br />
                <input type="text" id="custom-meta-description" name="custom-meta-description" size="100" value="<?php echo esc_attr(get_option('custom-meta-description')); ?>" /></p>
            
            <p><label for="dettagli-ga-id"><strong>Google Analytics &mdash; Measurement ID GA4 (es. G-XXXXXXXXXX):</strong></label><br />
                <input type="text" id="dettagli-ga-id" name="dettagli-ga-id" size="30" value="<?php echo esc_attr(get_option('dettagli-ga-id')); ?>" />
                <br /><em>Se lasciato vuoto non viene caricato alcuno script di tracciamento.</em></p>

            <p><label for="dettagli-num-articoli"><strong>Numero ultimi articoli in Home (consigliati multipli di 3):</strong></label><br />
                <input type="text" id="dettagli-num-articoli" name="dettagli-num-articoli" size="10" value="<?php echo esc_attr(get_option('dettagli-num-articoli')); ?>" /></p>

            <br><h3>Informazioni</h3><hr>
            
            <p><label for="dettagli-nome-ammin-afferente"><strong>Nome amministrazione afferente (compare in caso di assenza del campo successivo):</strong></label><br />
                <input type="text" id="dettagli-nome-ammin-afferente" name="dettagli-nome-ammin-afferente" size="100" value="<?php echo esc_attr(get_option('dettagli-nome-ammin-afferente')); ?>" /></p>
            
            <p><label for="dettagli-logo-ammin-afferente"><strong>URL immagine logo amministrazione afferente:</strong></label><br />
                <input type="text" id="dettagli-logo-ammin-afferente" name="dettagli-logo-ammin-afferente" size="100" value="<?php echo esc_attr(get_option('dettagli-logo-ammin-afferente')); ?>" /></p>
            
            <p><label for="dettagli-url-ammin-afferente"><strong>URL amministrazione afferente (con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-url-ammin-afferente" name="dettagli-url-ammin-afferente" size="100" value="<?php echo esc_attr(get_option('dettagli-url-ammin-afferente')); ?>" /></p>
            
            <br><h3>ID pagine</h3><hr>
            
            <p><label for="dettagli-id-privacy"><strong>ID pagina Privacy:</strong></label><br />
                <input type="text" id="dettagli-id-privacy" name="dettagli-id-privacy" size="10" value="<?php echo esc_attr(get_option('dettagli-id-privacy')); ?>" /></p>
            
            <p><label for="dettagli-id-cookie"><strong>ID pagina Cookie policy:</strong></label><br />
                <input type="text" id="dettagli-id-cookie" name="dettagli-id-cookie" size="10" value="<?php echo esc_attr(get_option('dettagli-id-cookie')); ?>" /></p>
            
            <p><label for="dettagli-id-notelegali"><strong>ID pagina Note legali:</strong></label><br />
                <input type="text" id="dettagli-id-notelegali" name="dettagli-id-notelegali" size="10" value="<?php echo esc_attr(get_option('dettagli-id-notelegali')); ?>" /></p>
            
            <p><label for="dettagli-id-contatti"><strong>ID pagina Contatti:</strong></label><br />
                <input type="text" id="dettagli-id-contatti" name="dettagli-id-contatti" size="10" value="<?php echo esc_attr(get_option('dettagli-id-contatti')); ?>" /></p>
            
            <br><h3>Link "Accedi" in Header</h3><hr>
            
            <p><label for="dettagli-url-accedi"><strong>URL per il bottone "Accedi": (con HTTP o HTTPS)</strong></label><br />
                <input type="text" id="dettagli-url-accedi" name="dettagli-url-accedi" size="100" value="<?php echo esc_attr(get_option('dettagli-url-accedi')); ?>" /></p>
            
            <br><h3>Contatti</h3><hr>

            <p><label for="dettagli-indirizzo"><strong>Indirizzo:</strong></label><br />
                <input type="text" id="dettagli-indirizzo" name="dettagli-indirizzo" size="100" value="<?php echo esc_attr(get_option('dettagli-indirizzo')); ?>" /></p>

            <p><label for="dettagli-cap"><strong>CAP:</strong></label><br />
                <input type="text" id="dettagli-cap" name="dettagli-cap" size="100" value="<?php echo esc_attr(get_option('dettagli-cap')); ?>" /></p>

            <p><label for="dettagli-citta"><strong>Città:</strong></label><br />
                <input type="text" id="dettagli-citta" name="dettagli-citta" size="100" value="<?php echo esc_attr(get_option('dettagli-citta')); ?>" /></p>
            
            <p><label for="dettagli-telefono"><strong>Telefono (senza prefisso nazionale [+39] e punteggiatura):</strong></label><br />
                <input type="text" id="dettagli-telefono" name="dettagli-telefono" size="100" value="<?php echo esc_attr(get_option('dettagli-telefono')); ?>" /></p>
            
            <p><label for="dettagli-fax"><strong>Fax (senza prefisso nazionale [+39] e punteggiatura):</strong></label><br />
                <input type="text" id="dettagli-fax" name="dettagli-fax" size="100" value="<?php echo esc_attr(get_option('dettagli-fax')); ?>" /></p>
            
            <p><label for="dettagli-email"><strong>Email:</strong></label><br />
                <input type="text" id="dettagli-email" name="dettagli-email" size="100" value="<?php echo esc_attr(get_option('dettagli-email')); ?>" /></p>
            
            <p><label for="dettagli-email2"><strong>Email 2 (opzionale):</strong></label><br />
                <input type="text" id="dettagli-email2" name="dettagli-email2" size="100" value="<?php echo esc_attr(get_option('dettagli-email2')); ?>" /></p>
            
            <p><label for="dettagli-pec"><strong>PEC:</strong></label><br />
                <input type="text" id="dettagli-pec" name="dettagli-pec" size="100" value="<?php echo esc_attr(get_option('dettagli-pec')); ?>" /></p>
            
            <p><label for="dettagli-cfpiva"><strong>C.F. / P.IVA:</strong></label><br />
                <input type="text" id="dettagli-cfpiva" name="dettagli-cfpiva" size="100" value="<?php echo esc_attr(get_option('dettagli-cfpiva')); ?>" /></p>
            
            <p><label for="dettagli-codunivoco"><strong>Cod. Univoco:</strong></label><br />
                <input type="text" id="dettagli-codunivoco" name="dettagli-codunivoco" size="100" value="<?php echo esc_attr(get_option('dettagli-codunivoco')); ?>" /></p>
            
            <p><label for="dettagli-iban"><strong>IBAN:</strong></label><br />
                <input type="text" id="dettagli-iban" name="dettagli-iban" size="100" value="<?php echo esc_attr(get_option('dettagli-iban')); ?>" /></p>

            <br><h3>Link Social</h3><hr>
            
            <p><label for="dettagli-facebook"><strong>Facebook (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-facebook" name="dettagli-facebook" size="100" value="<?php echo esc_attr(get_option('dettagli-facebook')); ?>" /></p>
            
            <p><label for="dettagli-twitter"><strong>Twitter (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-twitter" name="dettagli-twitter" size="100" value="<?php echo esc_attr(get_option('dettagli-twitter')); ?>" /></p>
            
            <p><label for="dettagli-youtube"><strong>YouTube (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-youtube" name="dettagli-youtube" size="100" value="<?php echo esc_attr(get_option('dettagli-youtube')); ?>" /></p>
            
            <p><label for="dettagli-instagram"><strong>Instagram (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-instagram" name="dettagli-instagram" size="100" value="<?php echo esc_attr(get_option('dettagli-instagram')); ?>" /></p>
            
            <p><label for="dettagli-telegram"><strong>Telegram (nome utente, senza l'URL completa https://telegram.me/NOMEUTENTE ):</strong></label><br />
                <input type="text" id="dettagli-telegram" name="dettagli-telegram" size="100" value="<?php echo esc_attr(get_option('dettagli-telegram')); ?>" /></p>
            
            <p><label for="dettagli-whatsapp"><strong>Whatsapp (senza prefisso nazionale [+39] e punteggiatura):</strong></label><br />
                <input type="text" id="dettagli-whatsapp" name="dettagli-whatsapp" size="100" value="<?php echo esc_attr(get_option('dettagli-whatsapp')); ?>" /></p>
            
            <p><label for="dettagli-linkedin"><strong>LinkedIn (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-linkedin" name="dettagli-linkedin" size="100" value="<?php echo esc_attr(get_option('dettagli-linkedin')); ?>" /></p>
            
            <br><h3>Configurazione Mappa</h3><hr>
            <ul>
                <li>Per utilizzare la mappa di <label for="dettagli-map"><strong>Google</strong> riempire il 1° campo.</li>
                <li>Per utilizzare la mappa di <strong>OpenStreet</strong> riempire i campi relativi a <strong>Latitudine e Longitudine</strong> lasciando vuoto il 1° campo.</li>
            </ul>
            <br>

            <p><strong>Google Maps iframe SRC (URL intero con HTTP o HTTPS):</strong></label><br />
                <input type="text" id="dettagli-map" name="dettagli-map" size="100" value="<?php echo esc_attr(get_option('dettagli-map')); ?>" />
            </p>

            <p><label for="dettagli-map-latitude"><strong>Latitudine:</strong></label><br />
                <input type="text" id="dettagli-map-latitude" name="dettagli-map-latitude" size="40" value="<?php echo esc_attr(get_option('dettagli-map-latitude')); ?>" />
            </p>

            <p><label for="dettagli-map-longitude"><strong>Longitudine:</strong></label><br />
                <input type="text" id="dettagli-map-longitude" name="dettagli-map-longitude" size="40" value="<?php echo esc_attr(get_option('dettagli-map-longitude')); ?>" />
            </p>

            <p><label for="dettagli-map-popup"><strong>[Facoltativo] Testo nel popup del marker (<i>é possibile usare HTML</i>):</strong></label><br />
                <input type="text" id="dettagli-map-popup" name="dettagli-map-popup" size="40" value="<?php echo esc_attr(get_option('dettagli-map-popup')); ?>" />
            </p>
            <br>

            <p><input type="submit" class="button button-primary" name="Submit" value="SALVA" /></p>

            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="page_options" value="dettagli-num-articoli,
                                                            dettagli-ga-id,
                                                            custom-meta-keywords,custom-meta-description,
                                                            dettagli-link-accessibilita,
                                                            dettagli-nome-ammin-afferente,dettagli-logo-ammin-afferente,dettagli-url-ammin-afferente,
                                                            dettagli-id-privacy,dettagli-id-cookie,dettagli-id-notelegali,dettagli-id-contatti,
                                                            dettagli-indirizzo,dettagli-cap,dettagli-citta,dettagli-telefono,dettagli-fax,
                                                            dettagli-email,dettagli-pec,dettagli-cfpiva,dettagli-facebook,
                                                            dettagli-twitter,dettagli-youtube,dettagli-instagram,
                                                            dettagli-telegram,dettagli-whatsapp,dettagli-linkedin,dettagli-map,
                                                            dettagli-map-latitude,dettagli-map-longitude,dettagli-map-popup,
                                                            dettagli-email2,dettagli-url-accedi,dettagli-codunivoco,
                                                            dettagli-iban" />

        </form>
    </div>
<?php }