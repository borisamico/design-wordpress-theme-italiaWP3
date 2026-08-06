<?php
/**
 * RGB-to-HSL and HSL-to-RGB Converter
 * Check http://www.michaelburri.ch/generate-different-shades-of-a-color/ for explanation
 * @author     Michael Burri, https://github.com/mpbzh
 * @license    GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// Validates hex color code and returns proper value
// Input: String - Format #ffffff, #fff, ffffff or fff
// Output: hex value - 3 byte (000000 if input is invalid)
function validate_hex($hex) {
    // Complete patterns like #ffffff or #fff
    if(preg_match("/^#([0-9a-fA-F]{6})$/", $hex) || preg_match("/^#([0-9a-fA-F]{3})$/", $hex)) {
        // Remove #
        $hex = substr($hex, 1);
    }
    
    // Complete patterns without # like ffffff or 000000
    if(preg_match("/^([0-9a-fA-F]{6})$/", $hex)) {
        return $hex;
    }
    
    // Short patterns without # like fff or 000
    if(preg_match("/^([0-9a-f]{3})$/", $hex)) {
        // Spread to 6 digits
        return substr($hex, 0, 1) . substr($hex, 0, 1) . substr($hex, 1, 1) . substr($hex, 1, 1) . substr($hex, 2, 1) . substr($hex, 2, 1);
    }
    
    // If input value is invalid return black
    return "000000";
}

// Converts hex color code to RGB color
// Input: String - Format #ffffff, #fff, ffffff or fff
// Output: Array(Hue, Saturation, Lightness) - Values from 0 to 1
function hex2hsl($hex) {
    //Validate Hex Input
    $hex = validate_hex($hex);
    
    // Split input by color
    $hex = str_split($hex, 2);

    // Convert color values to value between 0 and 1
    $r = (hexdec($hex[0])) / 255;
    $g = (hexdec($hex[1])) / 255;
    $b = (hexdec($hex[2])) / 255;
    
    return rgb2hsl(array($r,$g,$b));
}

// Converts RGB color to HSL color
// Check http://en.wikipedia.org/wiki/HSL_and_HSV#Hue_and_chroma for 
// details
// Input: Array(Red, Green, Blue) - Values from 0 to 1
// Output: Array(Hue, Saturation, Lightness) - Values from 0 to 1
function rgb2hsl($rgb) {
    // Fill variables $r, $g, $b by array given.
    list($r, $g, $b) = $rgb;
    
    // Determine lowest & highest value and chroma
    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $chroma = $max - $min;
    
    // Calculate Luminosity
    $l = ($max + $min) / 2;
    
    // If chroma is 0, the given color is grey
    // therefore hue and saturation are set to 0
    if ($chroma == 0)
    {
        $h = 0;
        $s = 0;
    }
    
    // Else calculate hue and saturation.
    // Check http://en.wikipedia.org/wiki/HSL_and_HSV for details
    else
    {
        switch($max) {
            case $r:
                $h_ = fmod((($g - $b) / $chroma), 6);
                if($h_ < 0) $h_ = (6 - fmod(abs($h_), 6)); // Bugfix: fmod() returns wrong values for negative numbers
                break;
            
            case $g:
                $h_ = ($b - $r) / $chroma + 2;
                break;
            
            case $b:
                $h_ = ($r - $g) / $chroma + 4;
                break;
            default:
                break;
        }
        
        $h = $h_ / 6;
        $s = $chroma / (1 - abs(2 * $l - 1));
    }
    
    // Return HSL Color as array
    return array($h, $s, $l);
}

// Converts HSL color to RGB color
// Input: Array(Hue, Saturation, Lightness) - Values from 0 to 1
// Output: Array(Red, Green, Blue) - Values from 0 to 1
function hsl2rgb($hsl) {
    // Fill variables $h, $s, $l by array given.
    list($h, $s, $l) = $hsl;
    
    // If saturation is 0, the given color is grey and only
    // lightness is relevant.
    if ($s == 0 ) {
        $rgb = array($l, $l, $l);
    }
    
    // Else calculate r, g, b according to hue.
    // Check http://en.wikipedia.org/wiki/HSL_and_HSV#From_HSL for details
    else
    {
        $chroma = (1 - abs(2*$l - 1)) * $s;
        $h_     = $h * 6;
        $x         = $chroma * (1 - abs((fmod($h_,2)) - 1)); // Note: fmod because % (modulo) returns int value!!
        $m = $l - round($chroma/2, 10); // Bugfix for strange float behaviour (e.g. $l=0.17 and $s=1)
        
             if($h_ >= 0 && $h_ < 1) $rgb = array(($chroma + $m), ($x + $m), $m);
        else if($h_ >= 1 && $h_ < 2) $rgb = array(($x + $m), ($chroma + $m), $m);
        else if($h_ >= 2 && $h_ < 3) $rgb = array($m, ($chroma + $m), ($x + $m));
        else if($h_ >= 3 && $h_ < 4) $rgb = array($m, ($x + $m), ($chroma + $m));
        else if($h_ >= 4 && $h_ < 5) $rgb = array(($x + $m), $m, ($chroma + $m));
        else if($h_ >= 5 && $h_ < 6) $rgb = array(($chroma + $m), $m, ($x + $m)); 
    }
    
    return $rgb;
}

// Converts RGB color to hex code 
// Input: Array(Red, Green, Blue)
// Output: String hex value (#000000 - #ffffff)
function rgb2hex($rgb) {
    list($r,$g,$b) = $rgb;
    $r = round(255 * $r);
    $g = round(255 * $g);
    $b = round(255 * $b);
    return "#".sprintf("%02X",$r).sprintf("%02X",$g).sprintf("%02X",$b);
}

// Converts HSL color to RGB hex code
// Input: Array(Hue, Saturation, Lightness) - Values from 0 to 1
// Output: String hex value (#000000 - #ffffff)
function hsl2hex($hsl) {
    $rgb = hsl2rgb($hsl);
    
    for($i=0; $i < sizeof($rgb); $i++) {
        if($rgb[$i] < 0) $rgb[$i] = 0;
        else if($rgb[$i] > 1) $rgb[$i] = 1;
    }

    return rgb2hex($rgb);
}

/* Customs */

function colorCompl($hex) {
    $hex = validate_hex($hex);
    $hex = str_split($hex, 2);

    $r = (hexdec($hex[0]));
    $g = (hexdec($hex[1]));
    $b = (hexdec($hex[2]));
    
    $new_r = 255-$r;
    $new_g = 255-$g;
    $new_b = 255-$b;
    
    return rgb2hex(array(($new_r/255),($new_g/255),($new_b/255)));
}

// calculates the luminosity of an given RGB color
// the color code must be in the format of RRGGBB
// the luminosity equations are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#relativeluminancedef
function calculateLuminosity($color) {
    $r = hexdec(substr($color, 0, 2)) / 255; // red value
    $g = hexdec(substr($color, 2, 2)) / 255; // green value
    $b = hexdec(substr($color, 4, 2)) / 255; // blue value
    if ($r <= 0.03928) {
        $r = $r / 12.92;
    } else {
        $r = pow((($r + 0.055) / 1.055), 2.4);
    }

    if ($g <= 0.03928) {
        $g = $g / 12.92;
    } else {
        $g = pow((($g + 0.055) / 1.055), 2.4);
    }

    if ($b <= 0.03928) {
        $b = $b / 12.92;
    } else {
        $b = pow((($b + 0.055) / 1.055), 2.4);
    }

    $luminosity = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    return $luminosity;
}

// calculates the luminosity ratio of two colors
// the luminosity ratio equations are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#contrast-ratiodef
function calculateLuminosityRatio($color1, $color2) {
    $l1 = calculateLuminosity($color1);
    $l2 = calculateLuminosity($color2);

    if ($l1 > $l2) {
        $ratio = (($l1 + 0.05) / ($l2 + 0.05));
    } else {
        $ratio = (($l2 + 0.05) / ($l1 + 0.05));
    }
    return $ratio;
}

// it returns the calculated contrast ratio
// the ratio levels are from the WCAG 2 requirements
// http://www.w3.org/TR/WCAG20/#visual-audio-contrast (1.4.3)
// http://www.w3.org/TR/WCAG20/#larger-scaledef
function getContrastRatio($hexcolor1,$hexcolor2='#ffffff') {
    $hexcolor1 = validate_hex($hexcolor1);
    $hexcolor2 = validate_hex($hexcolor2);

    return calculateLuminosityRatio($hexcolor1,$hexcolor2);
}

/* ------------------------------------------------------------------------
 * Contrasto conforme a WCAG 2.1 (Legge Stanca 4/2004, EN 301 549)
 *
 * Il tema deriva l'intera tavolozza da un unico colore scelto dall'utente. Le
 * verifiche che esistevano prima in inc/style.php non erano verifiche di
 * conformita':
 *
 *  - misuravano sempre il colore CONTRO IL BIANCO, mai contro il colore di
 *    testo effettivamente applicato;
 *  - usavano soglie empiriche (2.50 / 2.60 / 2.70 / 3.50) senza relazione con i
 *    4.5:1 richiesti dal criterio 1.4.3;
 *  - meta' delle condizioni era codice morto: un rapporto di contrasto e' per
 *    definizione >= 1.0, quindi i rami "< 0.70" e "< 0.80" non scattavano mai e
 *    i rami ">= 0.90" erano sempre veri.
 *
 * Le funzioni qui sotto riportano la decisione su una soglia reale, misurata
 * sulla coppia che l'utente vede davvero.
 * ---------------------------------------------------------------------- */

// Soglie WCAG 2.1 livello AA.
if (!defined('italiawp3_CONTRAST_TEXT'))    define('italiawp3_CONTRAST_TEXT', 4.5);    // 1.4.3 testo normale
if (!defined('italiawp3_CONTRAST_LARGE'))   define('italiawp3_CONTRAST_LARGE', 3.0);   // 1.4.3 testo grande (>=24px, o >=18.66px bold)
if (!defined('italiawp3_CONTRAST_NONTEXT')) define('italiawp3_CONTRAST_NONTEXT', 3.0); // 1.4.11 componenti UI e grafica

// Riporta un valore nell'intervallo [0,1].
// Serve perche' le varianti si costruiscono sommando delta a saturazione e
// luminosita': senza, i valori escono dall'intervallo e hsl2rgb() puo' lasciare
// $rgb indefinita, con un Warning PHP stampato ai visitatori.
function italiawp3_clamp_unit($value) {
    if ($value < 0) return 0.0;
    if ($value > 1) return 1.0;
    return (float) $value;
}

// Costruisce una variante spostando saturazione e luminosita' di un colore HSL,
// tenendo entrambe dentro l'intervallo valido.
// Input: array HSL (valori 0-1), delta di S e L espressi in punti 0-1.
// Output: stringa hex "#RRGGBB".
function italiawp3_shade($hsl, $delta_s, $delta_l) {
    return hsl2hex(array(
        $hsl[0],
        italiawp3_clamp_unit($hsl[1] + $delta_s),
        italiawp3_clamp_unit($hsl[2] + $delta_l)
    ));
}

// Sceglie fra il testo chiaro e quello scuro del tema quello che contrasta di piu'
// con lo sfondo dato. Corrisponde alla domanda "bianco o nero su questo colore?",
// che prima veniva risposta con una euristica di luminosita' contro il bianco.
//
// Il grigio scuro #1c2024 e' preferito al nero pieno per ragioni tipografiche, ma sui
// mezzitoni non basta: su #828282 si ferma a 4.26:1. Quando nessuna delle due scelte
// raggiunge la soglia si passa agli estremi puri, con cui il caso peggiore da' 4.58:1.
function italiawp3_best_text_color($background, $light = '#ffffff', $dark = '#1c2024', $target = italiawp3_CONTRAST_TEXT) {
    $ratio_light = getContrastRatio($light, $background);
    $ratio_dark  = getContrastRatio($dark, $background);

    if (max($ratio_light, $ratio_dark) >= $target) {
        return $ratio_dark > $ratio_light ? $dark : $light;
    }

    return getContrastRatio('#000000', $background) > getContrastRatio('#ffffff', $background)
        ? '#000000'
        : '#ffffff';
}

// Restituisce un colore di primo piano che raggiunge la soglia richiesta sullo
// sfondo dato. Se la coppia e' gia' conforme il colore torna invariato; altrimenti
// la luminosita' viene spostata a passi di 1% nella direzione che allontana dallo
// sfondo, mantenendo tinta e saturazione — cioe' l'identita' cromatica scelta
// dall'utente — finche' la soglia non e' raggiunta.
//
// Se nemmeno saturando si arriva alla soglia si ricade sul bianco o sul nero:
// per la soglia 4.5 uno dei due basta sempre (nel caso peggiore si ottiene 4.58).
function italiawp3_ensure_contrast($foreground, $background, $target = italiawp3_CONTRAST_TEXT) {
    $foreground = '#' . validate_hex($foreground);
    $background = '#' . validate_hex($background);

    if (getContrastRatio($foreground, $background) >= $target) {
        return $foreground;
    }

    $fg  = hex2hsl($foreground);
    $bg  = hex2hsl($background);
    // Sfondo chiaro: il testo va scurito. Sfondo scuro: schiarito.
    $step = ($bg[2] > 0.5) ? -0.01 : 0.01;

    for ($i = 1; $i <= 100; $i++) {
        $lightness = $fg[2] + ($step * $i);
        if ($lightness < 0 || $lightness > 1) break;

        $candidate = hsl2hex(array($fg[0], $fg[1], $lightness));
        if (getContrastRatio($candidate, $background) >= $target) {
            return $candidate;
        }
    }

    return italiawp3_best_text_color($background);
}

?>
