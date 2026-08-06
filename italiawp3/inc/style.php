<?php

include_once('colors.php');

function italiawp3_css_strip_whitespace($css){
	  $replace = array(
	    "#/\*.*?\*/#s" => "",
	    "#\s\s+#"      => " ",
	  );
	  $search = array_keys($replace);
	  $css = preg_replace($search, $replace, $css);

	  $replace = array(
	    ": "  => ":",
	    "; "  => ";",
	    " {"  => "{",
	    " }"  => "}",
	    ", "  => ",",
	    "{ "  => "{",
	    ";}"  => "}",
	    ",\n" => ",",
	    "\n}" => "}",
	    "} "  => "}\n",
	  );
	  $search = array_keys($replace);
	  $css = str_replace($search, $replace, $css);
          
	  return trim($css);
}

/**
 * Salva la palette derivata dal colore primario, usata dalla editor-color-palette.
 *
 * Scrive solo le option effettivamente cambiate, cosi' a regime il costo e' zero
 * scritture. La versione precedente usava il pattern
 * `get_option(x) ? update_option(x, v) : add_option(x, v)`: quando una option valeva
 * "0" o "" il ternario prendeva il ramo add_option(), che falliva in silenzio perche'
 * la option esisteva gia', e ritentava a ogni singolo pageload.
 *
 * @param array $options coppie nome_option => valore.
 */
function italiawp3_store_palette_options($options) {
    foreach ($options as $name => $value) {
        if (get_option($name) !== $value) {
            update_option($name, $value);
        }
    }
}

function italiawp3_dymanic_styles() {
    $color_black = "#000";
    $color_white = "#fff";
    $color_grey_10 = "#f5f5f0";
    $color_grey_15 = "#f6f9fc";
    $color_grey_20 = "#eee";
    $color_grey_30 = "#ddd";
    $color_grey_40 = "#a5abb0";
    $color_grey_50 = "#5a6772";
    $color_grey_60 = "#444e57";
    $color_grey_80 = "#30373d";
    $color_grey_90 = "#1c2024";
    $color_teal_30 = "#00c5ca";
    $color_teal_50 = "#65dcdf";
    $color_teal_70 = "#004a4d";
    
    $main_color = get_option('italiawp3_main_color', '#06c');
    $main_color_HSL = hex2hsl($main_color);
    //$hex = hsl2hex($main_color_HSL);
      
    /*
     * Le varianti sommano delta a saturazione e luminosita'. Senza clamp i valori
     * uscivano dall'intervallo [0,1]: con un primario chiaro color_5/10/20 finivano
     * tutti sullo stesso bianco, con uno scuro color_95/98/99 collassavano sul nero, e
     * hsl2rgb() poteva lasciare $rgb indefinita (Warning PHP stampato ai visitatori).
     * italiawp3_shade() applica il clamp prima della conversione.
     */
    $color_5  = italiawp3_shade($main_color_HSL, -50/100, +50/100);
    $color_10 = italiawp3_shade($main_color_HSL, -40/100, +40/100);
    $color_20 = italiawp3_shade($main_color_HSL, -30/100, +30/100);
    $color_30 = italiawp3_shade($main_color_HSL, -20/100, +20/100);
    $color_40 = italiawp3_shade($main_color_HSL, -15/100,  +8/100);

    $color_50 = $main_color;

    $color_60 = italiawp3_shade($main_color_HSL, 0,  -5/100);
    $color_70 = italiawp3_shade($main_color_HSL, 0, -10/100);
    $color_80 = italiawp3_shade($main_color_HSL, 0, -15/100);
    $color_90 = italiawp3_shade($main_color_HSL, 0, -20/100);
    $color_95 = italiawp3_shade($main_color_HSL, 0, -25/100);
    $color_98 = italiawp3_shade($main_color_HSL, 0, -40/100);
    $color_99 = italiawp3_shade($main_color_HSL, 0, -60/100);

    $color_compl = colorCompl($main_color);
    $color_compl_HSL = hex2hsl($color_compl);

    //$color_compl_5 = colorSetSL($color_compl, 20, 95);
    $color_compl_5 = hsl2hex(array($color_compl_HSL[0], (20/100), (95/100) ));

    //$color_compl_10 = colorSetSL($color_compl, 30, 90);
    $color_compl_10 = hsl2hex(array($color_compl_HSL[0], (30/100), (90/100) ));

    //$color_compl_80 = colorSetSL($color_compl, 100, 40);
    $color_compl_80 = hsl2hex(array($color_compl_HSL[0], (100/100), (40/100) ));

    //$color_compl_link_footer = colorSetSL($main_color, 100, 80);
    $color_compl_link_footer = hsl2hex(array($color_compl_HSL[0], (100/100), (80/100) ));

    /* ------------------------------------------------------------------
     * Colori con contrasto garantito (WCAG 1.4.3 / 1.4.11)
     *
     * Tutto cio' che segue deriva dal colore scelto dall'utente, che puo' essere
     * qualsiasi cosa: il customizer non lo vincola. Queste variabili applicano la
     * soglia di legge alla coppia effettiva testo/sfondo, invece di fidarsi della
     * tinta di partenza.
     *
     * Convenzione dei nomi:
     *   $on_<sfondo>   colore di testo leggibile SOPRA quello sfondo
     *   $<nome>_su_bianco  variante del colore usabile come TESTO su fondo bianco
     * ------------------------------------------------------------------ */

    // Testo sopra i fondi colorati del tema (header, footer, menu, bottoni).
    $on_color_50 = italiawp3_best_text_color($color_50);
    $on_color_80 = italiawp3_best_text_color($color_80);
    $on_color_90 = italiawp3_best_text_color($color_90);
    $on_color_95 = italiawp3_best_text_color($color_95);
    $on_color_10 = italiawp3_best_text_color($color_10);
    $on_color_20 = italiawp3_best_text_color($color_20);
    $on_color_70 = italiawp3_best_text_color($color_70);

    /*
     * Il primario usato come colore di TESTO su fondo chiaro: link di contenuto, chip,
     * titoli di card. Con un primario chiaro (giallo, ciano, verde acido) il primario
     * grezzo sta ampiamente sotto 4.5:1.
     *
     * Il fondo di riferimento non e' il bianco puro ma #ECEFF1, il grigio chiarissimo
     * che bootstrap-italia usa per card e chip: e' il piu' scuro dei fondi "chiari" su
     * cui questo testo puo' finire. Misurare sul bianco lasciava passare coppie che poi
     * sulla card scendevano sotto soglia (il chip si fermava a 3.97:1).
     */
    $fondo_chiaro = '#ECEFF1';

    $color_50_su_bianco = italiawp3_ensure_contrast($color_50, $fondo_chiaro);
    $color_40_su_bianco = italiawp3_ensure_contrast($color_40, $fondo_chiaro);
    $color_95_su_bianco = italiawp3_ensure_contrast($color_95, $fondo_chiaro);

    // Bordi e contorni: componenti non testuali, soglia 3:1 (1.4.11).
    $color_50_bordo = italiawp3_ensure_contrast($color_50, $fondo_chiaro, italiawp3_CONTRAST_NONTEXT);

    // Banner "notifica importante": sfondo $color_compl (complementare del primario).
    $on_color_compl = italiawp3_best_text_color($color_compl);

    // Didascalie delle figure: sfondo grigio scuro fisso, non derivato dal primario.
    $on_grey_60 = italiawp3_best_text_color($color_grey_60);
    // Il chip al passaggio usa $color_50_bordo come sfondo: il testo va misurato su quello.
    $on_color_50_bordo = italiawp3_best_text_color($color_50_bordo);

    // Link del footer. $color_compl_link_footer ha luminosita' fissa all'80%: come
    // colore di testo non raggiunge mai la soglia su fondo chiaro, e su fondo scuro
    // dipende dalla tinta. Va misurato sullo sfondo su cui viene davvero stampato.
    $link_footer_su_80 = italiawp3_ensure_contrast($color_compl_link_footer, $color_80);
    // $color_compl_link_footer e' anche lo sfondo di alcuni elementi (calendario,
    // .Button--default): li' serve il testo che ci sta sopra.
    $on_link_footer   = italiawp3_best_text_color($color_compl_link_footer);

    /*
     * Testo generale del footer: sta su .u-background-80, cioe' $color_80. Prima era
     * $color_10 corretto ($footer_testo): conforme, ma una tinta slavata. Qui serve il
     * testo, non una sfumatura del primario, quindi si usa direttamente il colore
     * leggibile su quel fondo — bianco puro col footer scuro, cioe' la resa storica, e
     * testo scuro se il primario e' chiarissimo, invece di sparire.
     */
    $footer_testo = $on_color_80;
    // Stato hover dei link del footer: deve restare distinguibile e conforme.
    $footer_testo_hover = italiawp3_ensure_contrast($color_compl_10, $color_80);

    /*
     * Bordi dentro il footer: righe del calendario, filetto sopra il widget e
     * sottolineatura dei titoli. Erano grigi fissi (#d6dce3 e rgb(90,103,114) in
     * adjustments.css) o $color_10 grezzo, tutti indipendenti dal fondo su cui stanno.
     * Sono componenti non testuali: soglia 3:1 (1.4.11) misurata su $color_80.
     */
    $footer_bordo = italiawp3_ensure_contrast($color_10, $color_80, italiawp3_CONTRAST_NONTEXT);

    /*
     * Pulsante di ricerca in testata: inverte la coppia della barra, come nel disegno
     * originale — fondo del colore del testo della testata, icona del colore della
     * testata. Prima qui c'era $scuro, cioe' $color_98 misurato sul BIANCO e non sul
     * fondo reale: col primario di default la luminosita' arrivava a 0 e il pulsante
     * diventava un cerchio nero con icona bianca su una barra colorata.
     *
     * $on_color_50 e' gia' il testo conforme su $color_50, quindi la coppia e' sopra
     * soglia per costruzione; l'icona e' un componente non testuale (1.4.11, 3:1).
     */
    $cerca_sfondo = $on_color_50;
    $cerca_icona  = italiawp3_ensure_contrast($color_50, $cerca_sfondo, italiawp3_CONTRAST_NONTEXT);
    // Al passaggio il fondo diventa $color_compl: l'icona va rimisurata su quello.
    $cerca_icona_hover = italiawp3_ensure_contrast($color_50, $color_compl, italiawp3_CONTRAST_NONTEXT);

    // Nome dell'ente in testata: sta su #mainheader, che e' .u-background-50.
    // $color_40 e' il primario con L+8%, cioe' quasi lo stesso colore dello sfondo.
    $header_owner = italiawp3_ensure_contrast($color_40, $color_50);

    // Occhiello della testata: sta sul grigio scuro $color_grey_80.
    $header_banner = italiawp3_ensure_contrast($color_60, $color_grey_80);

    // Voci del menu di primo livello: fondo $color_90.
    $on_menu_lev1 = italiawp3_best_text_color($color_90);

    /* ------------------------------------------------------------------
     * Token :root per i fogli statici
     *
     * I CSS in static/ nascono con i colori del disegno originale scritti a mano: il
     * blu istituzionale #003882, il verde #4E7E5B e l'oro #796C31. Sono tre tinte fisse
     * che il customizer non tocca, quindi cambiando il primario meta' del sito lo
     * seguiva e meta' no — le CTA 'Vedi tutti', il 'Torna alla Home' di 403/404 e il
     * bottone 'Accetto' della cookiebar restavano verdi su un sito rosso.
     *
     * Questi token sono l'aggancio: i fogli statici li consumano come
     * var(--iwp-..., #hex-originale). Sostituire un hex con una var() DENTRO una
     * dichiarazione gia' esistente non tocca ne' il selettore ne' il peso, quindi la
     * cascata del tema — l'ordine degli enqueue e i ~380 !important scritti contro
     * l'output della 1.x — resta esattamente com'era. Il fallback nella var() copre i
     * contesti dove questo foglio non gira (l'editor a blocchi in iframe), dove
     * altrimenti si otterrebbe il nero.
     *
     * I nomi dicono il RUOLO, non la tinta: 'primario' e' un fondo pieno, 'primario-testo'
     * lo stesso colore reso leggibile su fondo chiaro. Lo stesso hex di partenza finisce
     * su token diversi a seconda di come era usato — un #4E7E5B fondo di bottone e un
     * #4E7E5B colore di un link non sono la stessa cosa e non possono valere lo stesso.
     * ------------------------------------------------------------------ */

    // Testo sopra la cookiebar, che e' .u-background-80 ($color_80).
    $on_color_80_tok = italiawp3_best_text_color($color_80);

    /*
     * L'oro dei titoli di sezione era la terza tinta del disegno. Con il verde assorbito
     * dal primario lo slot della seconda tinta si libera, e l'accento complementare —
     * gia' usato dal tema per gli hover della testata e il bordo del navmenu — lo eredita:
     * cosi' il sito resta a due colori invece di collassare a uno solo.
     *
     * $color_compl_80 e' il complementare portato a S 100% / L 40%: una tinta piena,
     * adatta a un fondo. Come testo su fondo chiaro va comunque corretto, perche' a L 40%
     * non tutte le tinte reggono 4.5:1.
     */
    $on_accento     = italiawp3_best_text_color($color_compl_80);
    $accento_testo  = italiawp3_ensure_contrast($color_compl_80, $fondo_chiaro);
    // Icone e bordi color accento: componenti non testuali, soglia 3:1 (1.4.11).
    $accento_icona  = italiawp3_ensure_contrast($color_compl_80, $fondo_chiaro, italiawp3_CONTRAST_NONTEXT);
    /*
     * L'accento compare anche nel postFooter, che sta su .u-background-80: li' misurarlo
     * sul fondo chiaro darebbe un numero che non c'entra nulla con cio' che si vede.
     * $color_compl_80 ha luminosita' fissa al 40%, quindi su un fondo scuro non e' mai
     * conforme di suo: va corretto sullo sfondo reale.
     */
    $accento_su_80        = italiawp3_ensure_contrast($color_compl_80, $color_80);
    $accento_icona_su_80  = italiawp3_ensure_contrast($color_compl_80, $color_80, italiawp3_CONTRAST_NONTEXT);

    /*
     * Fondi pallidi: nei fogli statici sono #EAF2EC, #E6EFE8, #E6E9F2 — velature quasi
     * bianche (L 94%, saturazione minima) dietro icone e schede.
     *
     * Non si usa $color_10: le varianti della palette sommano delta al primario, quindi
     * il risultato dipende da quanto e' scuro il colore di partenza. Col primario di
     * default $color_10 e' quasi bianco, ma con un primario gia' chiaro resta una tinta
     * piena — misurato: col teal #09768D esce #8CC9D6, un ciano medio che come velatura
     * dietro un'icona non funziona e cambia il peso visivo della pagina.
     *
     * Qui serve un colore ANCORATO, non derivato per somma: stessa tinta del primario,
     * saturazione al 25% e luminosita' al 94% comunque, cioe' il ruolo che #EAF2EC
     * svolgeva. E' lo stesso metodo gia' usato per $color_compl_5/_10/_80 poco sopra.
     */
    $color_tenue = hsl2hex(array($main_color_HSL[0], (25/100), (94/100)));
    // Il testo che ci finisce sopra va misurato su quel fondo, non dato per scuro.
    $on_color_tenue = italiawp3_best_text_color($color_tenue);

    /*
     * Hover su fondi colorati. Non sono scelte nuove: sono la convenzione che questo
     * stesso file applica gia' piu' sotto, dove sostituisce il #6FA67E della testata con
     * $color_compl e il #BADEC3 del footer con $color_compl_10. I token la rendono
     * leggibile anche dal foglio statico, invece di lasciarla implicita in un override.
     */
    $hover_su_primario = italiawp3_ensure_contrast($color_compl, $color_50, italiawp3_CONTRAST_NONTEXT);
    // Il filetto del menu a scomparsa: quel menu sta su bianco, non su un fondo derivato.
    $navmenu_bordo     = italiawp3_ensure_contrast($color_compl, '#ffffff', italiawp3_CONTRAST_NONTEXT);
    $hover_su_footer   = italiawp3_ensure_contrast($color_compl_10, $color_80);

    /*
     * Nota sulle classi utility (.u-color-*, .u-background-*): restano ai valori
     * derivati grezzi. Sono un'API di design token, esposta anche alla tavolozza di
     * Gutenberg, e il loro significato e' "il colore numero N": cambiarne il valore
     * romperebbe l'accoppiamento con la .u-background-* corrispondente. La conformita'
     * si ottiene sulle coppie concrete, dove lo sfondo e' noto.
     */

    // Queste option servono solo alla editor-color-palette (functions.php).
    // Vengono riscritte solo se il colore primario e' cambiato: prima erano 6
    // update_option/add_option incondizionate dentro wp_head, cioe' scritture sul DB
    // durante una richiesta di sola lettura.
    italiawp3_store_palette_options(array(
        'italiawp3_colore_primario'        => $color_50,
        'italiawp3_colore_primario_chiaro' => $color_30,
        'italiawp3_colore_primario_scuro'  => $color_95,
        'italiawp3_colore_complementare'   => $color_compl,
        'italiawp3_colore_nero'            => $color_black,
        'italiawp3_colore_bianco'          => $color_white,
    ));

    $custom_css = "
/* Token consumati dai fogli statici come var(--iwp-..., #hex-originale).
   Il ruolo di ciascuno e' spiegato dove viene calcolato, piu' sopra in questo file.
   Niente !important: sono valori, non dichiarazioni in gara nella cascata. */
:root {
  --iwp-primario: {$color_50};
  --iwp-su-primario: {$on_color_50};
  --iwp-primario-hover: {$color_90};
  --iwp-su-primario-hover: {$on_color_90};
  --iwp-primario-testo: {$color_50_su_bianco};
  --iwp-primario-icona: {$color_50_bordo};
  --iwp-primario-tenue: {$color_tenue};
  --iwp-su-primario-tenue: {$on_color_tenue};
  --iwp-primario-80: {$color_80};
  --iwp-su-primario-80: {$on_color_80_tok};
  --iwp-hover-su-primario: {$hover_su_primario};
  --iwp-hover-su-footer: {$hover_su_footer};
  --iwp-accento: {$color_compl_80};
  --iwp-su-accento: {$on_accento};
  --iwp-accento-testo: {$accento_testo};
  --iwp-accento-icona: {$accento_icona};
  --iwp-accento-su-footer: {$accento_su_80};
  --iwp-accento-icona-su-footer: {$accento_icona_su_80};
}

.u-color-black {
  color: {$color_black} !important;
}

.u-background-black {
  background-color: {$color_black} !important;
}

/* Il bianco resta solo dove lo sfondo e' effettivamente scuro a prescindere dal
   primario. Le voci che stanno su fondi derivati — footer ($color_80), menu di primo
   livello ($color_90), Share/ScrollTop ($color_50 e $color_95) — sono state tolte da
   qui: il loro colore si calcola piu' sotto sullo sfondo reale, perche' con un primario
   chiaro il bianco forzato produceva testo bianco su fondo chiaro. */
.u-color-white,
.Form-input.Form-input:focus+[role=tooltip],
.Spid-button {
  color: {$color_white} !important;
}

.Bullets>li:before,
.Share-reveal>a>span, .Share-revealIcon, .Share>ul>li, .Share>ul>li>a {
  color: {$on_color_50} !important;
}

.Footer, .Footer-blockTitle, .Footer-subTitle,
.Footer-block li, .Footer-subBlock,
#footer .wp-calendar-table,
#footer .wp-block-calendar table tbody {
  color: {$footer_testo} !important;
}

.ScrollTop, .ScrollTop-icon {
  color: {$on_color_95} !important;
}

.u-background-white,
.Megamenu--default .Megamenu-subnav, .Skiplinks>li>a, .Spid-menu {
  background-color: {$color_white} !important;
}

.u-color-grey-10,
.Footer-block address {
  color: {$color_grey_10} !important;
}

.u-background-grey-10,
.Spid-idp:hover {
  background-color: {$color_grey_10} !important;
}

.u-color-grey-15 {
  color: {$color_grey_15} !important;
}

.u-background-grey-15 {
  background-color: {$color_grey_15} !important;
}

.u-color-grey-20 {
  color: {$color_grey_20} !important;
}

.u-background-grey-20,
.Hero-content, .Share-reveal, .Share-revealIcon.is-open,
.Treeview--default li[aria-expanded=true] li a, .Treeview--default li[aria-expanded=true] li a:hover,
#menu-sinistro .menu-separatore {
  background-color: {$color_grey_20} !important;
}

.u-color-grey-30,
.Accordion--default .Accordion-header, .Accordion--plus .Accordion-header, .Linklist, .Linklist li, .Timeline {
  color: {$color_grey_30} !important;
}

.u-background-grey-30,
.Treeview--default li[aria-expanded=true] li li a, .Treeview--default li[aria-expanded=true] li li a:hover {
  background-color: {$color_grey_30} !important;
}

.Accordion--default .Accordion-header, .Accordion--plus .Accordion-header, .Footer-block li, .Footer-links,
.Footer-subBlock, .Leads-link, .Linklist li, .u-border-top-xxs {
  border-color: {$color_grey_30} !important;
}

.u-color-grey-40,
.Megamenu--default .Megamenu-subnavGroup {
  color: {$color_grey_40} !important;
}

.u-background-grey-40 {
  background-color: {$color_grey_40} !important;
}

.u-color-grey-50,
.Megamenu--default .Megamenu-subnavGroup>li, .Share-revealText {
  color: {$color_grey_50} !important;
}

.u-background-grey-50 {
  background-color: {$color_grey_50} !important;
}

.u-color-grey-60 {
  color: {$color_grey_60} !important;
}

.u-background-grey-60,
#articolo-dettaglio-testo .galleriasfondo {
  background-color: {$color_grey_60} !important;
}

.u-color-grey-80,
.Megamenu--default .Megamenu-subnavGroup>li>ul>li>ul>li>a, .Megamenu--default .Megamenu-subnavGroup>li>ul>li a,
#menu-sinistro :is(h2,h4).dropdown,
#briciole .breadcrumb .breadcrumb-item.active,

label {
  color: {$color_grey_80} !important;
}

.u-background-grey-80,
.Form-input.Form-input:focus+[role=tooltip],
.Header-banner {
  background-color: {$color_grey_80} !important;
}

.u-color-grey-90 {
  color: {$color_grey_90} !important;
}

.u-background-grey-90 {
  background-color: {$color_grey_90} !important;
}

/* Link / buttons */

.u-color-teal-30 {
  color: {$color_teal_30} !important;
}

.u-background-teal-30 {
  background-color: {$color_teal_30} !important;
}

.u-color-teal-50 {
  color: {$color_teal_50} !important;
}

.u-background-teal-50 {
  background-color: {$color_teal_50} !important;
}

.u-color-teal-70 {
  color: {$color_teal_70} !important;
}

.u-background-teal-70 {
  background-color: {$color_teal_70} !important;
}

/* Color primary */

.u-color-5 {
  color: {$color_5} !important;
}

.u-background-5,
.Accordion--default .Accordion-header:hover, .Accordion--plus .Accordion-header:hover,
.Linklist a:hover {
  background-color: {$color_5} !important;
}

.u-color-10,
/* Testo del footer: sta su .u-background-80, non su bianco. $color_10 e' il primario
   schiarito del 40% e su un primario gia' chiaro finiva quasi sullo stesso tono del
   fondo. table#wp-calendar a e' escluso: ha uno sfondo proprio, gestito piu' sotto. */
#footer, #footer .postFooter, #footer label, #footer caption,
#footer .wp-calendar-table th,
#footer .wp-block-calendar th {
  color: {$footer_testo} !important;
}

#footer .footer-list .icon {
  fill: {$footer_testo};
}

.u-background-10,
.Linklist-link.Linklist-link--lev3 {
  background-color: {$color_10} !important;
}

/* Bordo misurato sul fondo del footer, non $color_10 grezzo: e' un componente non
   testuale e con un primario chiaro spariva dentro lo sfondo. */
#footer :is(h2,h3,h4),
#footer .postFooter {
  border-color: {$footer_bordo} !important;
}

.u-background-20,
.Linklist-link.Linklist-link--lev2, .Linklist-link.Linklist-link--lev2:hover {
  background-color: {$color_20} !important;
}

.u-color-20 {
  color: {$color_20} !important;
}

.u-color-30, .has-colore-primario-chiaro-color {
  color: {$color_30} !important;
}

.u-background-30, .has-colore-primario-chiaro-background-color {
    background-color: {$color_30} !important;
}

.u-color-40 {
    color: {$color_40} !important;
}

/* Il nome dell'ente sta sul fondo .u-background-50: separato dall'utility perche'
   $color_40 e' il primario con L+8%, cioe' praticamente lo sfondo stesso. */
.Header-owner {
    color: {$header_owner} !important;
}

.u-background-40,
.Megamenu--default {
    background-color: {$color_40} !important;
}

.u-color-50 {
    color: {$color_50} !important;
}

/* Testo primario su fondo chiaro: $color_50_su_bianco garantisce 4.5:1 (1.4.3)
   anche quando l'utente sceglie un primario chiaro, mantenendone tinta e saturazione. */
.Accordion--default .Accordion-link, .Accordion--plus .Accordion-link,
.ErrorPage-subtitle, .ErrorPage-title, .Header-language-other a,
.Linklist-link, .Linklist a, .Share-revealIcon.is-open, .Skiplinks>li>a,
.Header-socialIcons [class*=\" Icon-\"], .Header-socialIcons [class^=Icon-],
.has-colore-primario-color,
.tutte,
.chip.chip-primary,
.chip.chip-primary>.chip-label,
.scheda-argomento-lista-testo a,
/* I bottoni sono esclusi: non sono link di testo, hanno un fondo proprio, e questa
   regola col suo !important vi stampava sopra il primario. I loro colori si decidono
   piu' sotto, come coppia fondo+testo.
   - blocchi Gutenberg (Buttons e File): primario sul grigio scuro del core, 1.08:1;
   - .btn del tema: il pulsante 'Torna alla Home' di 403/404 e' .btn-verde, che ha
     fondo #4E7E5B e dichiara color:#fff !important — ma questa regola arriva dopo,
     con lo stesso peso, e vinceva: primario su verde, 1.12:1, illeggibile. Trovato
     dal pannello Accessibilita'. */
.articolo-paragrafi a:not(.wp-block-button__link):not(.wp-block-file__button):not(.btn),
#articolo-dettaglio-testo .scheda-allegato :is(h2,h3,h4) a,
#articolo-dettaglio-testo .scheda-ufficio-contatti :is(h2,h3,h4) a,
.card .card-body a h5.card-title {
    color: {$color_50_su_bianco} !important;
}

/* Il pulsante del blocco File di Gutenberg ha uno sfondo scuro proprio (#32373c,
   definito dal core, non derivato dal primario): il testo che ci sta sopra e' il bianco,
   10.4:1. Restava qui anche quando il tema non lo escludeva dai link di contenuto; ora
   l'esclusione c'e', ma la dichiarazione resta esplicita perche' la coppia con quel fondo
   non deve dipendere dall'ordine dei fogli del core. */
.wp-block-file__button {
    color: #fff !important;
}

/* Bordi e contorni: componenti non testuali, soglia 3:1 (1.4.11). */
.chip.chip-primary,
.chip.chip-primary:hover,
.btn-outline-primary {
    border-color: {$color_50_bordo} !important;
}

.btn-primary {
    border-color: {$color_50} !important;
}

.btn-outline-primary {
    -webkit-box-shadow: inset 0 0 0 1px {$color_50_bordo};
    box-shadow: inset 0 0 0 1px {$color_50_bordo};
    color: {$color_50_su_bianco} !important;
}

.chip.chip-primary:hover {
    background-color: {$color_50_bordo};
}

/* Il testo del chip al passaggio sta sopra il fondo appena impostato. */
.chip.chip-primary:hover,
.chip.chip-primary:hover>.chip-label {
    color: {$on_color_50_bordo} !important;
}

.u-background-50,
.Header-navbar,
.Bullets>li:before, .Share-revealIcon, .Share>ul>li,
.Header-searchTrigger button,
.has-colore-primario-background-color,
.flag-icon, .btn-primary {
    background-color: {$color_50} !important;
}

/* Il testo del bottone primario non era mai adattato allo sfondo: restava il #fff di
   bootstrap-italia anche con un primario chiaro. */
.btn-primary,
.btn-primary:hover,
.btn-primary:focus {
    color: {$on_color_50} !important;
}

/* Blocco Buttons di Gutenberg. Il tema non l'aveva mai vestito: restava la pillola grigia
   del core (#32373c) con sopra il primario preso dai link di contenuto, 1.08:1. Riceve lo
   stesso trattamento di .btn-primary — fondo primario, testo misurato su quel fondo.

   Le esclusioni :not() non sono cosmetiche: dicono \"il tema decide solo il default\". Se il
   redattore sceglie un colore nell'editor (classi has-*-background-color / has-text-color
   o stile inline) quella scelta resta sua, e l'!important qui non gliela porta via. */
.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not([class*=\"-background-color\"]):not([style*=\"background\"]) {
    background-color: {$color_50} !important;
    border-color: {$color_50} !important;
}

/* :not([class*=\"-background-color\"]) c'e' anche qui, e non e' ridondante: se il redattore
   sceglie un fondo dalla tavolozza, il testo lo decide la coppia stampata piu' sotto,
   misurata su QUEL fondo. Senza questa esclusione vinceva sempre questa regola, che ha
   specificita' maggiore, e \"fondo bianco\" dava testo bianco su bianco. */
.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not(.has-text-color):not([style*=\"color\"]):not([class*=\"-background-color\"]) {
    color: {$on_color_50} !important;
}

/* Al passaggio il fondo scende a $color_90, lo stesso di .btn-primary:hover; il testo va
   rimisurato su quello. */
.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not([class*=\"-background-color\"]):not([style*=\"background\"]):hover,
.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not([class*=\"-background-color\"]):not([style*=\"background\"]):focus {
    background-color: {$color_90} !important;
    border-color: {$color_90} !important;
}

.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not(.has-text-color):not([style*=\"color\"]):hover,
.wp-block-button:not(.is-style-outline) > .wp-block-button__link:not(.has-text-color):not([style*=\"color\"]):focus {
    color: {$on_color_90} !important;
}

/* Variante \"contorno\": fondo trasparente, quindi vale la coppia su fondo chiaro, come
   .btn-outline-primary. Il fondo e' dichiarato qui e non lasciato al core: gli stili dei
   blocchi vengono accodati solo quando il blocco e' presente, e senza di essi restava il
   grigio pieno con sopra il testo pensato per il fondo chiaro. */
.wp-block-button.is-style-outline > .wp-block-button__link:not([class*=\"-background-color\"]):not([style*=\"background\"]) {
    background-color: transparent !important;
}

.wp-block-button.is-style-outline > .wp-block-button__link:not(.has-text-color):not([style*=\"color\"]):not([class*=\"-background-color\"]) {
    color: {$color_50_su_bianco} !important;
}

.wp-block-button.is-style-outline > .wp-block-button__link:not([class*=\"-border-color\"]) {
    border-color: {$color_50_bordo} !important;
}

/* Anello di focus del tema anche su questi bottoni. Su di loro vinceva quello di
   bootstrap-italia — :focus:not([data-focus-mouse=\"true\"]), specificita' (0,2,0) contro
   la (0,1,1) di a:focus-visible — che e' realizzato con il solo box-shadow e quindi
   sparisce in Forced Colors Mode e in stampa. */
.wp-block-button__link:focus-visible,
.wp-block-file__button:focus-visible {
    outline: var(--iwp-focus-spessore) solid var(--iwp-focus-colore) !important;
    outline-offset: 1px !important;
    box-shadow: 0 0 0 6px var(--iwp-focus-alone) !important;
}

/* Icone su fondo chiaro: grafica funzionale, soglia 3:1 (1.4.11). */
.tutte svg.icon,
#articolo-dettaglio-testo .scheda-allegato svg.icon, .scheda-allegato svg.icon {
    fill: {$color_50_bordo} !important;
}

.u-color-60 {
    color: {$color_60} !important;
}

/* L'occhiello di testata sta sul grigio scuro $color_grey_80. */
.Header-banner {
    color: {$header_banner} !important;
}

.u-background-60 {
    background-color: {$color_60} !important;
}

.u-color-70 {
    color: {$color_70} !important;
}

.u-background-70,
.push-body-toright .body_wrapper,
.push-body-toright .body_wrapper .preheader {
    background-color: {$color_70} !important;
}

.u-color-80,
.Button--info {
    color: {$color_80} !important;
}

.u-background-80 {
    background-color: {$color_80} !important;
}

/* La cookiebar e' .u-background-80, quindi il suo 'Accetto' non sta su bianco come tutti
   gli altri .btn-verde ma su $color_80: la stessa tinta del bottone, solo piu' scura.
   Con il fondo primario il bottone si confondeva nella barra — misurato circa 2:1 fra i
   due, sotto i 3:1 che 1.4.11 chiede al contorno di un componente.
   Si inverte la coppia, come gia' fa il pulsante di ricerca in testata: fondo del colore
   del testo della barra, testo del colore della barra. Per costruzione i due si vedono. */
.cookiebar .btn-verde,
.cookiebar .btn-verde:hover,
.cookiebar .btn-verde:focus {
    background: {$on_color_80} !important;
    color: {$color_80} !important;
}

/* Il testo della barra ha .text-white scritto nel template: un bianco fisso su un fondo
   che invece dipende dal primario. Con un primario chiaro $color_80 e' chiaro anche lui
   e la coppia crolla — misurato 2,86:1 con primario giallo, sotto i 4,5:1 di 1.4.3.
   Qui il colore si decide sul fondo reale. Il selettore e' piu' specifico di .text-white
   e porta lo stesso !important, quindi vince; il template resta com'e'. */
.cookiebar, .cookiebar p, .cookiebar p.text-white {
    color: {$on_color_80} !important;
}

input[type=\"date\"], input[type=\"datetime-local\"], input[type=\"email\"],
input[type=\"number\"], input[type=\"password\"], input[type=\"search\"],
input[type=\"tel\"], input[type=\"text\"], input[type=\"time\"], input[type=\"url\"], textarea {
    border-color: {$color_80} !important;
}

#mainheader .preheader .accedi {
    border-color: transparent;
}

.u-color-90 {
    color: {$color_90} !important;
}

.dropdown-item {
    color: {$color_90};
}

.u-background-90,
.Linklist-link.Linklist-link--lev1, .Linklist-link.Linklist-link--lev1:hover,
.btn-primary:hover {
    background-color: {$color_90} !important;
}

.btn-primary:hover, .btn-outline-primary:hover {
    border-color: {$color_90} !important;
}

.btn-outline-primary:hover {
    -webkit-box-shadow: inset 0 0 0 1px {$color_90};
    box-shadow: inset 0 0 0 1px {$color_90};
}

.u-color-95 {
    color: {$color_95} !important;
}

/* Testo scuro derivato su fondo chiaro. Le voci Linklist di livello 2 e 3 sono state
   tolte da questo elenco: stanno su fondo colorato ($color_20 e $color_10) e il loro
   colore si decide piu' sotto, sullo sfondo su cui poggiano davvero. */
#intro .titolo-sezione :is(h1,h2), #intro-argomenti .titolo-sezione :is(h1,h2),
#introricerca .titolo-sezione :is(h1,h2), #intro-sititematici .titolo-sezione :is(h1,h2),
#briciole a,

.Linklist a:hover,
.Megamenu--default .Megamenu-subnavGroup>li>a, .Treeview--default li[aria-expanded=true] li a,
.Treeview--default li[aria-expanded=true] li a:hover, .Treeview--default li[aria-expanded=true] li li a,
.Treeview--default li[aria-expanded=true] li li a:hover,
.Footer-socialIcons [class*=Icon-], .Footer-socialIcons [class^=Icon-],
.Button--default, .has-colore-primario-scuro-color,

.scheda .scheda-testo :is(h2,h3,h4) a, .scheda .scheda-testo :is(h2,h3,h4) a:not([href]):not([tabindex]),
.scheda .scheda-testo :is(h2,h3,h4) a:not([href]):not([tabindex]):focus,
.scheda .scheda-testo :is(h2,h3,h4) a:not([href]):not([tabindex]):hover,

.form-group input, .form-group optgroup, .form-group textarea {
    color: {$color_95_su_bianco} !important;
}

.articolo-paragrafi a:not(.wp-block-button__link):not(.wp-block-file__button):hover,

.novita-testo h2 a, .scheda .scheda-testo-small :is(h2,h3,h4) a,
.scheda .scheda-icona-small a, .cbp-spmenu .logotxt-burger a,
.navmenu li a, .utilitymobile li a,
.socialmobile .small,

#mainheader .p_cercaMobile input[type=text],
#menu-sinistro #lista-paragrafi .list-group-item, #menu-sinistro .lista-paragrafi .list-group-item,
.dropdown-item:focus, .dropdown-item:hover,

.navmenu>li.open ul li a,
.navmenu>li.open ul li a:hover, .navmenu>li.open ul>li.open>a,
.navmenu a, .navmenu li>a>span, .navmenu li>a:hover>span,

.italiawp3-sidebar .cerca input,

section .pagination .page-item .page-link, section .pagination .page-item .page-numbers,
section .pagination .page-item .page-link, section .pagination .page-item .page-links {
    color: {$color_95_su_bianco};
}

/* Il testo segnaposto e' testo a tutti gli effetti: vale la soglia 4.5:1 (1.4.3). */
#mainheader .p_cercaMobile input[type=text]::placeholder,
.italiawp3-sidebar .cerca input::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
    color: {$color_95_su_bianco};
    opacity: 1; /* Firefox */
}

#mainheader .p_cercaMobile input[type=text]:-ms-input-placeholder,
.italiawp3-sidebar .cerca input:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: {$color_95_su_bianco};
}

#mainheader .p_cercaMobile input[type=text]::-ms-input-placeholder,
.italiawp3-sidebar .cerca input::-ms-input-placeholder { /* Microsoft Edge */
    color: {$color_95_su_bianco};
}

.italiawp3-sidebar .cerca input,
#mainheader .p_cercaMobile input[type=text] {
    border-color: {$color_95_su_bianco} !important;
}

.argomenti .badge-argomenti,
.scheda-news .scheda-argomenti .badge-argomenti {
    color: {$color_95_su_bianco};
    border-color: {$color_95_su_bianco};
}

/* Il pulsante di ricerca in testata non e' piu' in questo elenco: $color_95_su_bianco e'
   misurato su #ECEFF1, mentre quell'icona sta sul fondo del pulsante. Il suo colore si
   calcola piu' sotto, insieme al fondo ($cerca_icona). */
.socialmobile a svg.icon,
.articolo-paragrafi .icon-external-link,
#menu-sinistro .icon,

section .pagination .page-item .page-link svg.icon,
section .pagination .page-item .page-numbers svg.icon {
    fill: {$color_95_su_bianco};
}

.argomenti a.badge-argomenti:hover,
.italiawp3-sidebar .btn-cerca,
.italiawp3-sidebar .wp-block-search__button,
.scheda-news .scheda-argomenti a.badge-argomenti:hover {
    background-color: {$color_95};
}

/* Al passaggio il badge si riempie di $color_95: il testo va rimisurato su quel fondo,
   altrimenti resta il colore usato su bianco e sparisce. */
.argomenti a.badge-argomenti:hover,
.scheda-news .scheda-argomenti a.badge-argomenti:hover,
.italiawp3-sidebar .btn-cerca,
.italiawp3-sidebar .wp-block-search__button {
    color: {$on_color_95} !important;
}

.italiawp3-sidebar .btn-cerca svg.icon,
.italiawp3-sidebar .wp-block-search__button svg.icon {
    fill: {$on_color_95} !important;
}

.u-background-95,
.ScrollTop, .mfp-bg, mfp-img,
.Footer .Form-input:not(.is-disabled), .Footer .Form-input:not(:disabled),
.has-colore-primario-scuro-background-color  {
    background-color: {$color_95} !important;
}

.u-backround-none {
    background-color: transparent !important;
}

.u-color-compl, .has-colore-complementare-color,
#mainheader .social a:hover,
#mainheader .preheader .accedi .btn-accedi:hover span,
.socialmobile a:hover {
    color: {$color_compl} !important;
}

#mainheader .preheader .accedi .btn-accedi:hover svg.icon,
#mainheader .social a:hover svg.icon,
.socialmobile a:hover svg.icon {
    fill: {$color_compl} !important;
}

.u-background-compl, .has-colore-complementare-background-color,
#mainheader .btn-cerca:hover,
#sezione-notifica {
    background-color: {$color_compl} !important;
}

/* Il banner di notifica ha uno sfondo derivato ma testo e link erano fissi nei CSS
   statici (#555 e il blu istituzionale): con un complementare scuro diventavano
   illeggibili — 1.03:1 misurato con primario giallo. */
#sezione-notifica,
#sezione-notifica p,
#sezione-notifica h1, #sezione-notifica h2, #sezione-notifica h3,
#sezione-notifica .col-12 {
    color: {$on_color_compl} !important;
}

#sezione-notifica a {
    color: {$on_color_compl} !important;
    text-decoration: underline;
}

#sezione-notifica svg.icon {
    fill: {$on_color_compl} !important;
}

/* Il filetto di 4px a sinistra della voce e' l'unico segno che dice quale voce e' aperta:
   e' grafica funzionale, quindi 3:1 (1.4.11) sul fondo su cui poggia. Il menu a scomparsa
   sta su bianco — misurato, non dedotto — e $color_compl grezzo li' non ci arriva: con il
   primario teal il complementare e' un rosa chiaro, 1.5:1, praticamente invisibile. */
.navmenu>li.open>a,
.navmenu>li>a:hover, .navmenu>li>a:focus {
    border-color: {$navmenu_bordo} !important;
}

.u-background-compl-5,
.navmenu>li>a:hover, .navmenu>li>a:focus,
.navmenu>li.open ul li a:hover, .navmenu>li.open ul>li.open>a,
.dropdown-item:focus, .dropdown-item:hover {
    background-color: {$color_compl_5} !important;
}

.u-color-compl-5 {
    color: {$color_compl_5} !important;
}

.u-color-compl-10,
#footer .footer-list li a:hover,
#footer .social a:hover,
#footer .postFooter a:hover {
    color: {$color_compl_10} !important;
}

.u-background-compl-10,
.navmenu>li.open>a {
    background-color: {$color_compl_10} !important;
}

#footer .social a:hover svg.icon {
    fill: {$color_compl_10} !important;
}

.u-color-compl-80 {
    color: {$color_compl_80} !important;
}

.u-background-compl-80,
.u-background-compl-80 a:not(.Button--info) {
    background-color: {$color_compl_80} !important;
}

/* $color_compl_link_footer ha luminosita' fissa all'80%: come colore di testo non
   raggiunge mai 4.5:1 da solo. Qui e' misurato sul fondo scuro del footer ($color_80). */
.Footer a, .CookieBar a, .section-gallery a,
.owl-prev, .owl-next {
    color: {$link_footer_su_80} !important;
}

/* Le didascalie delle immagini nel contenuto stanno su .u-background-grey-60, un grigio
   scuro fisso e indipendente dal primario: vanno misurate su quello. Il selettore e'
   volutamente stretto: la versione generica su figure figcaption catturava anche le
   didascalie del carosello gallerie, che poggiano invece sul colore 80. */
.u-background-grey-60 figcaption > p,
.u-background-grey-60 figcaption > p:first-of-type {
    color: {$on_grey_60} !important;
}

/* Il carosello gallerie compare in due contesti con fondi diversi, distinguibili dalla
   classe .galleriahome sulla sezione contenitore:
   - in home la sezione e' #articolo-dettaglio-testo.galleriahome.u-background-80,
     quindi le didascalie poggiano su $color_80;
   - nella pagina della singola galleria il fondo e' .galleriasfondo, un div FRATELLO a
     tutta larghezza colorato con il grigio scuro fisso $color_grey_60. Non essendo un
     antenato il colore non si eredita e va imposto esplicitamente.
   Prima erano trattati allo stesso modo: con un primario chiaro una delle due rese
   finiva fuori soglia. */
#articolo-dettaglio-testo.galleriahome .galleriaslide,
#articolo-dettaglio-testo.galleriahome .galleriaslide p,
#articolo-dettaglio-testo.galleriahome .galleriaslide :is(h2,h3,h4),
#articolo-dettaglio-testo.galleriahome .galleriaslide small,
#articolo-dettaglio-testo.galleriahome .galleriaslide figcaption {
    color: {$on_color_80} !important;
}

/* Il selettore prende tutti i p, non solo quelli in figcaption: la data della foto sta
   in .galleria-foto, fuori dalla didascalia, ed ereditava il #555 del body — 1.14:1
   sul fondo scuro. */
#articolo-dettaglio-testo:not(.galleriahome) .galleriaslide,
#articolo-dettaglio-testo:not(.galleriahome) .galleriaslide p,
#articolo-dettaglio-testo:not(.galleriahome) .galleriaslide :is(h2,h3,h4),
#articolo-dettaglio-testo:not(.galleriahome) .galleriaslide small,
#articolo-dettaglio-testo:not(.galleriahome) .galleriaslide figcaption {
    color: {$on_grey_60} !important;
}

.Button--default {
    border-color: {$color_compl_link_footer} !important;
}

#wp-calendar a, .Footer-socialIcons [class*=Icon-], .Footer-socialIcons [class^=Icon-],
.Button--default {
    background-color: {$color_compl_link_footer} !important;
}

/* Dove $color_compl_link_footer e' lo SFONDO, il testo sopra va misurato su di esso. */
#wp-calendar a, .Button--default {
    color: {$on_link_footer} !important;
}

.Footer-socialIcons [class*=Icon-], .Footer-socialIcons [class^=Icon-] {
    fill: {$on_link_footer} !important;
}";

    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

    /*
     * Bottoni Gutenberg con un fondo scelto dal redattore.
     *
     * Le classi has-*-background-color generate dal core sono mono-proprieta': cambiano
     * il fondo e lasciano il testo a quello che c'era, che qui e' il default del tema
     * calcolato su $color_50. Scegliere "colore bianco" come fondo produceva quindi
     * testo chiaro su bianco. Per ogni voce della tavolozza si stampa il testo che ci sta
     * sopra; :not(.has-text-color) perche' un colore di testo scelto a mano resta
     * dell'autore.
     *
     * Gli slug e i valori sono gli stessi registrati in editor-color-palette
     * (functions.php), che li legge dalle option scritte da italiawp3_store_palette_options().
     */
    $palette_bottoni = array(
        'colore-nero'            => $color_black,
        'colore-bianco'          => $color_white,
        'colore-primario'        => $color_50,
        'colore-primario-chiaro' => $color_30,
        'colore-primario-scuro'  => $color_95,
        'colore-complementare'   => $color_compl,
    );

    $custom_css = '';
    foreach ($palette_bottoni as $slug => $fondo) {
        $sopra = italiawp3_best_text_color($fondo);
        $custom_css .= "
.wp-block-button__link.has-{$slug}-background-color:not(.has-text-color),
.wp-block-file__button.has-{$slug}-background-color:not(.has-text-color) {
    color: {$sopra} !important;
}";
    }

    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';
    
    /*
     * Colori del testo sui fondi colorati.
     *
     * Questi quattro blocchi erano condizionati a soglie che non erano soglie WCAG e
     * che in meta' dei casi non potevano scattare (vedi la nota in inc/colors.php).
     * Ora sono incondizionati: il colore del testo viene sempre calcolato sullo sfondo
     * su cui poggia, con italiawp3_best_text_color(), che sceglie fra chiaro e scuro
     * quello che massimizza il rapporto di contrasto.
     */

    /* - Menu sidebar livello 3: fondo $color_10 - */
    $custom_css = "
.Linklist-link.Linklist-link--lev3,
.Linklist-link.Linklist-link--lev3:hover {
    color: {$on_color_10} !important;
}
#menu-sinistro .Linklist-link.Linklist-link--lev3 .icon {
    fill: {$on_color_10} !important;
}";
    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

    /* - Menu sidebar livello 2: fondo $color_20 - */
    $custom_css = "
.Linklist-link.Linklist-link--lev2,
.Linklist-link.Linklist-link--lev2:hover {
    color: {$on_color_20} !important;
}
#menu-sinistro .Linklist-link.Linklist-link--lev2 .icon {
    fill: {$on_color_20} !important;
}";
    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

    /* - Menu principale livello 1: fondo $color_90 - */
    $custom_css = "
.Linklist-link.Linklist-link--lev1,
.Linklist-link.Linklist-link--lev1:hover,
.Megamenu--default .Megamenu-item>a {
    color: {$on_menu_lev1} !important;
}";
    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

    /* - Testata: fondo $color_50 - */
    $custom_css = "
#mainheader,
#mainheader .comune .logotxt .logo-titolo a, #mainheader .comune .logotxt .logo-titolo a:hover,
#mainheader .cerca input,
.scheda-sito.u-background-50 {
    color: {$on_color_50} !important;
}
#mainheader .social a svg.icon {
    fill: {$on_color_50} !important;
}

/* La preheader e' .u-background-70, non .u-background-50: il suo contenuto va misurato
   su $color_70. Con un primario grigio desaturato la differenza fra i due fondi bastava
   a far scendere il testo a 4.26:1. */
#mainheader .preheader,
#mainheader .preheader .entesup a,
#mainheader .preheader .accedi .btn-accedi span {
    color: {$on_color_70} !important;
}
#mainheader .preheader .accedi .btn-accedi svg.icon {
    fill: {$on_color_70} !important;
}
#mainheader .cerca input {
    border-color: {$on_color_50} !important;
}
#mainheader .cerca input::placeholder {
    color: {$on_color_50} !important;
    opacity: 1;
}
/* La regola vale a ogni larghezza: sotto i 992px tema.css lascia il pulsante
   trasparente, ma il cerchio pieno e' la resa voluta anche li'. */
#mainheader .btn-cerca,
#mainheader .p_cercaMobile .btn-cerca {
    background: {$cerca_sfondo} !important;
}
#mainheader .btn-cerca svg.icon,
#mainheader .p_cercaMobile .btn-cerca svg.icon {
    fill: {$cerca_icona} !important;
}
#mainheader .btn-cerca:hover svg.icon,
#mainheader .p_cercaMobile .btn-cerca:hover svg.icon {
    fill: {$cerca_icona_hover} !important;
}
@media (min-width: 992px) {
    .navbar .navbar-collapsable .navbar-nav li a.nav-link {
        color: {$on_color_50} !important;
    }
}";
    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

    /*
     * - Footer: fondo $color_80 -
     *
     * Anche qui la condizione e' sparita. Le regole per .chip che stavano dentro questo
     * blocco sono state tolte: il chip non e' nel footer, e il suo contrasto e' gia'
     * garantito piu' sopra su fondo bianco ($color_50_su_bianco / $color_50_bordo).
     */
    $custom_css = "
#footer .footer-list li a,
#footer :is(h2,h3,h4),
.galleriahome h3,
#articolo-dettaglio-testo .galleriaslide figcaption,
#footer .postFooter a,
#footer label, #footer caption,
#footer a,
.scheda-sito.u-background-80 {
    color: {$footer_testo} !important;
}
#footer .social a svg.icon,
#footer .icon-external-link {
    fill: {$footer_testo} !important;
}
#footer .footer-list li a:hover, #footer .social a:hover,
#footer .postFooter a:hover {
    color: {$footer_testo_hover} !important;
}

/* Bordi del calendario e del suo contenitore. In adjustments.css sono grigi fissi
   (#d6dce3 sulle righe, rgb(90,103,114) sul filetto): pensati per una tabella su fondo
   chiaro, dentro il footer restavano di un tono estraneo al resto. Servono !important
   perche' adjustments.css li dichiara gia' cosi'. */
#footer table tr,
#footer .wp-calendar-table tr,
#footer .wp-block-calendar tr,
#footer table td, #footer table th {
    border-color: {$footer_bordo} !important;
}
#footer .Footer-block .calendar_wrap {
    border-top-color: {$footer_bordo} !important;
}";
    echo '<style>'.italiawp3_css_strip_whitespace($custom_css).'</style>';

}

add_action('wp_head', 'italiawp3_dymanic_styles', 99);
