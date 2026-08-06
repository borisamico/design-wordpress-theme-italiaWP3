<?php
/*
 * ### SEZIONE MAPPA ###
 * Mostra la mappa di Google Maps
 *
 */
?>

<?php if (get_option('dettagli-map') != "" ): ?>

<section id="mappa" class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3><?php echo __('Interactive Map','italiawp3'); ?></h3>
                <?php
                /*
                 * Era un <h5> subito dopo l'<h3>: salto di livello nella gerarchia dei
                 * titoli (WCAG 1.3.1), e non e' un titolo ma un'istruzione. Il testo
                 * precedente ("Click on to activate") era inoltre un'istruzione
                 * sensoriale, riferita al solo mouse (WCAG 3.3.2).
                 */
                ?>
                <p class="h5-map"><?php echo __('Activate the map to interact with it','italiawp3'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="map-full-content">
    <?php
    /*
     * L'overlay era un <div> con handler solo su click: non focalizzabile e inutilizzabile
     * da tastiera (WCAG 2.1.1). Come <button> e' azionabile con Invio e Spazio senza
     * bisogno di handler aggiuntivi.
     * L'iframe parte con tabindex="-1" perche' finche' l'overlay lo copre e' raggiungibile
     * col Tab pur non essendo utilizzabile; inc/scripts.js glielo toglie all'attivazione.
     */
    ?>
    <button type="button" class="map-wrap">
        <span class="sr-only"><?php echo __('Activate the map to interact with it','italiawp3'); ?></span>
    </button>
    <iframe src="<?php echo sanitize_text_field(get_option('dettagli-map')); ?>"
            title="<?php echo esc_attr__('Interactive Map','italiawp3'); ?>"
            tabindex="-1" allowfullscreen></iframe>
</section>

<?php else: ?>

    <?php if ((get_option('dettagli-map-latitude') != "") && (get_option('dettagli-map-longitude')  != "")): ?>
    <?php italiawp3_enqueue_map_assets(); ?>
    <section id="mappa" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h3><?php echo __('Interactive Map','italiawp3'); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <section class="map-full-content">
        <div id="map" class="map-container">
            <script>
            // Leaflet e' in footer
            window.addEventListener('load', function () {

                const c = [
                    <?php echo floatval(get_option('dettagli-map-latitude')); ?>,
                    <?php echo floatval(get_option('dettagli-map-longitude')); ?>
                ];

                let map = new L.map('map');
                map.setView(c,100);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                let marker = L.marker(c).addTo(map);
                <?php if (get_option('dettagli-map-popup')!=""): ?>
                    marker.bindPopup("<?php echo esc_js(get_option('dettagli-map-popup')); ?>").openPopup();
                <?php else : ?>
                    marker.bindPopup("<span><strong><?php echo esc_js(get_option('blogname')); ?></strong><br><?php echo esc_js(get_option('dettagli-indirizzo')); ?><br><?php echo esc_js(get_option('dettagli-cap')); ?>, <?php echo esc_js(get_option('dettagli-citta')); ?></span>").openPopup();
                <?php endif; ?>
    
            });
            </script>
        </div>
    </section>
    <?php endif; ?>

<?php endif; ?>
