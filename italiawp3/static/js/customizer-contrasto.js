/*
 * Avviso di contrasto live nel customizer (WCAG 2.1 AA, criterio 1.4.3).
 *
 * La descrizione del controllo "Colore principale" e' generata da PHP al caricamento
 * del pannello, quindi rifletterebbe solo il colore gia' salvato. Qui viene ricalcolata
 * a ogni movimento del selettore, cosi' l'utente vede subito se la tinta che sta
 * scegliendo regge la soglia di legge o se il tema dovra' correggerla.
 *
 * La formula della luminanza relativa e' la stessa di inc/colors.php, che a sua volta
 * segue la definizione WCAG.
 */
(function ($) {
    'use strict';

    var SOGLIA = 4.5;

    function hexToRgb(hex) {
        hex = String(hex).replace('#', '');
        if (hex.length === 3) {
            hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
        }
        if (!/^[0-9a-fA-F]{6}$/.test(hex)) {
            return null;
        }
        return [
            parseInt(hex.substr(0, 2), 16),
            parseInt(hex.substr(2, 2), 16),
            parseInt(hex.substr(4, 2), 16)
        ];
    }

    function luminanza(rgb) {
        var c = rgb.map(function (v) {
            v = v / 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
    }

    // Rapporto di contrasto del colore dato rispetto al bianco.
    function rapportoSuBianco(hex) {
        var rgb = hexToRgb(hex);
        if (!rgb) {
            return null;
        }
        return (1.0 + 0.05) / (luminanza(rgb) + 0.05);
    }

    function aggiorna(valore) {
        var $avviso = $('.italiawp3-contrasto-avviso');
        if (!$avviso.length) {
            return;
        }

        var ratio = rapportoSuBianco(valore);
        if (ratio === null) {
            return;
        }

        var arrotondato = ratio.toFixed(2);

        if (ratio >= SOGLIA) {
            $avviso
                .removeClass('italiawp3-contrasto-ko')
                .addClass('italiawp3-contrasto-ok')
                .text(
                    'Contrasto su fondo bianco: ' + arrotondato +
                    ':1. Sufficiente per il testo (minimo di legge 4.5:1).'
                );
        } else {
            $avviso
                .removeClass('italiawp3-contrasto-ok')
                .addClass('italiawp3-contrasto-ko')
                .text(
                    'Contrasto su fondo bianco: ' + arrotondato +
                    ':1, sotto il minimo di legge di 4.5:1. Per i testi il tema userà ' +
                    'automaticamente una variante più scura di questo colore, quindi ' +
                    'link e titoli appariranno diversi dalla tinta scelta.'
                );
        }
    }

    wp.customize('italiawp3_main_color', function (setting) {
        aggiorna(setting.get());
        setting.bind(aggiorna);
    });

    // Un po' di colore all'avviso, per distinguere a colpo d'occhio i due stati.
    // Le tinte scelte reggono a loro volta la soglia sul fondo del pannello.
    $(function () {
        $('<style>' +
          '.italiawp3-contrasto-avviso{display:block;margin-top:6px;line-height:1.5}' +
          '.italiawp3-contrasto-ok{color:#14562c}' +
          '.italiawp3-contrasto-ko{color:#8a2208;font-weight:600}' +
          '</style>').appendTo('head');
    });
})(jQuery);
