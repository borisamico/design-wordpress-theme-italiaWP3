# ItaliaWP3
## Tema WordPress per la Pubblica Amministrazione

![Licenza GPLv3](https://img.shields.io/badge/licenza-GPLv3-blue.svg)
![WordPress 6.0+](https://img.shields.io/badge/WordPress-6.0%2B-21759b.svg)
![PHP 8.0+](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg)
![Bootstrap Italia 2.18.3](https://img.shields.io/badge/Bootstrap%20Italia-2.18.3-0066cc.svg)

---

#### [**Guarda la demo**](https://italiawp.borisamico.it/demo3) | [**Scarica il tema**](https://raw.githubusercontent.com/borisamico/design-wordpress-theme-italiaWP3/main/italiawp3.zip) | [**Pagina del tema**](https://italiawp.borisamico.it)

---

**ItaliaWP3** è un tema basato su [**Bootstrap Italia**](https://italia.github.io/bootstrap-italia/) utilizzabile per i **siti** della **Pubblica Amministrazione**, sviluppato per [**WordPress**](https://it.wordpress.org/) (CMS open-source).

**ItaliaWP3** utilizza i componenti del [**Prototipo per siti PA di AgID**](https://italia.github.io/design-comuni-prototipi/) delle [**linee guida di design dell'Agenzia per l'Italia digitale**](https://docs.italia.it/italia/designers-italia/design-linee-guida-docs/it/stabile/index.html) rilasciate dal [**Team per la trasformazione digitale**](https://teamdigitale.governo.it/) e su [**Bootstrap Italia**](https://italia.github.io/bootstrap-italia/).

**ItaliaWP3** è una **soluzione open-source e gratuita**, ideale per comuni, istituti e piccoli enti pubblici in genere che hanno scelto di utilizzare WordPress per il proprio sito internet.

È la **riscrittura completa** di [**ItaliaWP2**](https://github.com/italia/design-wordpress-theme-italiaWP2), da cui eredita l'impianto e le convenzioni, a sua volta evoluzione di [**ItaliaWP** (versione 1)](https://github.com/italia/design-wordpress-theme-italiaWP). Rispetto alla versione 2 cambiano il framework — **Bootstrap Italia 2.x** su Bootstrap 5 — e l'impostazione dell'accessibilità, che qui è uno strumento di lavoro e non una dichiarazione.

---

## Installare _ItaliaWP3_:

1. Scaricare il file [.zip](https://raw.githubusercontent.com/borisamico/design-wordpress-theme-italiaWP3/main/italiawp3.zip) in locale
2. Aggiungere un nuovo tema nella sezione _"Aspetto" > "Temi"_
3. Selezionare _"Aggiungi nuovo"_ e successivamente _"Carica tema"_
4. Scegliere il file .zip scaricato in precedenza, senza decomprimerlo e cliccare _"Installa ora"_
5. Selezionare _"Attiva"_ per poter utilizzare il tema

### Requisiti

| | |
|---|---|
| **WordPress** | 6.0 o superiore |
| **PHP** | 8.0 o superiore (testato su 8.3) |
| **Bootstrap Italia** | 2.18.3, incluso nel tema |

Il tema è compatibile con l'editor a blocchi **Gutenberg**. Chi preferisce l'editor
classico può installare il plugin [**Classic Editor**](https://it.wordpress.org/plugins/classic-editor/).

## In caso di utilizzo del _Tema Child_:

Ripetere la procedura precedente ma con il file [.zip](https://raw.githubusercontent.com/borisamico/design-wordpress-theme-italiaWP3/main/italiawp3-child.zip)
del _Tema Child_, successivamente configurare il _Tema Child_ lasciando _italiawp3_ con la configurazione iniziale.

---

## Configurare _ItaliaWP3_:

1. Creare 2 pagine per "Home" e "Articoli"
2. In _"Impostazioni" > "Lettura"_ selezionare _"Una pagina statica"_
3. Assegnare le pagine appena create rispettivamente ad "Homepage" e "Pagina articoli"
4. Impostare i dati di contatto e i dettagli nella sezione _"Dettagli"_ (qui si imposta anche, facoltativamente, il _Measurement ID_ di Google Analytics 4)
5. Attivare le sezioni della "Home" in _"Aspetto" > "Personalizza"_ > _"Sito & Homepage"_
6. In _"Aspetto" > "Menu"_ creare un nuovo Menu ed inserirlo nella posizione "Menu Principale"
7. [Facoltativo] Installare (e poi attivare) i plugin consigliati in _"Aspetto" > "Installa plugin"_ (la voce compare finché i plugin consigliati non sono installati)

---

## Principali caratteristiche di _ItaliaWP3_:

* **WordPress**: è realizzato per l'ultima versione di _WordPress_ disponibile in Italiano
* **Responsive**: si adatta ad _ogni tipo di dispositivo_ (desktop, tablet e mobile) mantenendo le funzionalità
* Pagina **"ACCESSIBILITÀ"** nel backend: controlla il markup delle pagine pubbliche, misura i contrasti reali sul sito renderizzato, scandisce contenuti, immagini e PDF, e tiene il registro delle verifiche manuali con esito e data — il materiale su cui si fonda la _dichiarazione di accessibilità_ da pubblicare su [form.agid.gov.it](https://form.agid.gov.it)
    * I controlli automatici coprono circa **un terzo** dei criteri: navigazione da tastiera e screen reader non sono automatizzabili da nessuno strumento e restano una verifica umana. Il pannello lo dichiara e non mostra mai "tutto verde"
* Pagina **"DETTAGLI"** nel backend: è possibile inserire e modificare facilmente le _informazioni di contatto_, i _link dei profili social_ (Facebook, Twitter, YouTube, Instagram, LinkedIn, Telegram e WhatsApp), il numero di _ultimi articoli_ in homepage, gli _ID delle pagine_ Privacy, Note legali e Contatti, il link alla _dichiarazione di accessibilità_ e il _Measurement ID_ di **Google Analytics 4**
    * Il campo Analytics è **facoltativo**: se lasciato vuoto non viene caricato alcuno script di tracciamento e non parte alcuna richiesta verso terze parti
    * Se mancano i riferimenti obbligatori per un sito della PA (dichiarazione di accessibilità, informativa privacy, note legali), un **avviso in bacheca** li elenca finché non vengono compilati
* **11 posizioni del menu**: _menu principale_, _menu in alto_ e 9 per i _box servizi_ (divisi in 3 tipologie)
    * Nei menu **Box Servizi** è possibile inserire delle immagini utilizzando i _Link personalizzati_ e inserendo direttamente il tag html **< img src="URL" >** nel _Testo del link_
* **5 aree widget**: _Footer Colonne_, _Sidebar Pagine_ (disattivabile scegliendo l'apposito _Template_), _Sidebar Articoli_, _Sezione Notifica_ e _Sezione Credits_
* **Logo, titolo e descrizione**: personalizzazione del _logo_, del _titolo_, della _denominazione_ e dell'_icona_ del sito direttamente dalla personalizzazione del tema
* **Immagini** e **Gallerie Fotografiche**: ritaglio e sistemazione automatica delle _immagini principali_ degli articoli, gestione grafica secondo le linee guida delle _singole immagini_ (disattivabile) e _gallerie_ caricate negli articoli o nelle pagine. Gestione di Gallerie Fotografiche esterne tramite _Custom Post Type_
* **Liste Allegati**: gestione degli allegati (e relative liste) tramite _Plugin esterno_ di cui si richiede l'installazione al momento dell'attivazione del tema
* Integrazione con il **Customizer (Personalizza)** di WordPress:
    * Sezione **"Sito & Homepage"** consente di fissare o no il _menu principale_ e di attivare o disattivare le varie _sezioni_ in home page
    * Sezione **"Preferenze Pagine & Articoli"** consente di attivare l'_immagine in evidenza_ di _default_ (per gli articoli che non ne hanno una), consente di disattivare le _cornici grafiche_ per le immagini "singole"
    * Sezione **"Colori"** consente di impostare il _colore principale_ del sito: le varianti, i colori secondari e ogni coppia testo/sfondo sono generati dinamicamente, con il contrasto verificato sul fondo su cui il testo viene davvero stampato
    * Sezione **"Immagine dell'header"** consente di impostare l'_immagine di copertina_ dell'homepage
    * Sezione **"Menu"** consente di creare, gestire e posizionare i _menu personalizzati_ nelle posizioni previste
    * Sezione **"Widget"** consente di creare, gestire e posizionare i _widget_ nelle colonne del footer

---

## Aggiornamenti:

Gli aggiornamenti sono notificati in _"Aspetto" > "Temi"_ oppure nella _pagina degli aggiornamenti_ del backend di WordPress.

---

## Sviluppo:

Il tema è **autosufficiente**: niente build step, niente SCSS, niente bundler, **niente
Node**. Tutto ciò che il browser riceve è già compilato e versionato dentro `static/`, e la
personalizzazione grafica avviene tramite i fogli di stile del tema, caricati _dopo_ quelli
di _Bootstrap Italia_. Si scarica, si attiva, si lavora.

I due controlli di manutenzione sono script PHP senza dipendenze:

```bash
php bin/check-css.php          # sintassi dei CSS del tema
php bin/check-bi-version.php   # allineamento delle versioni di Bootstrap Italia
```

Il primo va eseguito dopo ogni modifica ai CSS: un commento malformato disattiva
**silenziosamente** tutto il resto del foglio, senza che il browser segnali nulla.

La procedura per aggiornare Bootstrap Italia e quella di rilascio sono in
[`CONTRIBUTING.md`](CONTRIBUTING.md).

### Traduzioni

La traduzione italiana è in `languages/it_IT.po` (sorgente) e `languages/it_IT.mo`
(compilato, quello che WordPress legge). Il text domain è `italiawp3`.

Chi aggiorna le traduzioni con **Poedit** ("Aggiorna dal sorgente") deve verificare che
in _Catalogo > Proprietà > Parole chiave sorgenti_ ci siano **tutte** le funzioni di
traduzione di WordPress, non solo `__` e `_e`:

```
__ · _e · _n:1,2 · _x:1,2c · _ex:1,2c · _nx:4c,1,2
esc_attr__ · esc_attr_e · esc_attr_x:1,2c
esc_html__ · esc_html_e · esc_html_x:1,2c
_n_noop:1,2 · _nx_noop:3c,1,2
```

Il file `.po` del tema le dichiara già. Se però ne manca una, Poedit non trova le stringhe
scritte con quella funzione, le marca come obsolete (`#~`) e sul sito ricompaiono in
inglese. Prima della 1.0.1 era successo con il form dei commenti, il breadcrumb della
pagina 404 e i testi per screen reader. Dopo ogni aggiornamento conviene controllare che
nessuna voce obsoleta sia ancora usata nel codice.

In alternativa il catalogo si può rigenerare con WP-CLI, che riconosce già tutte le
funzioni:

```bash
wp i18n make-pot . languages/italiawp3.pot --domain=italiawp3 --exclude="__*,inc/tgm-plugin-activation,bin,docs,static"
```

---

## Plugin WordPress:

I seguenti plugin WordPress sono già gestiti graficamente nel tema.

**Per la Pubblica Amministrazione:**

* [**Gestione Circolari**](https://it.wordpress.org/plugins/gestione-circolari/) pubblica le circolari dell'ente: il tema ne gestisce l'archivio, la pagina della singola circolare e la sezione _"Ultime circolari"_ in homepage (da attivare in _"Aspetto" > "Personalizza" > "Sito & Homepage"_)
* [**Amministrazione Trasparente**](https://it.wordpress.org/plugins/amministrazione-trasparente/) organizza la sezione obbligatoria _Amministrazione Trasparente_
* [**Albo Pretorio On line**](https://it.wordpress.org/plugins/albo-pretorio-on-line/) pubblica gli atti dell'_Albo Pretorio_
* [**Polylang**](https://it.wordpress.org/plugins/polylang/) rende il sito multilingua; con la traduzione dei media attiva, le cornici delle immagini mostrano titolo e didascalia nella lingua della pagina

**Consigliati:**

* [**Attachments**](https://it.wordpress.org/plugins/attachments/) consente il caricamento di allegati in Pagine e Articoli (proposto all'attivazione del tema)

**Facoltativi:**

* [**Classic Editor**](https://it.wordpress.org/plugins/classic-editor/) ripristina l'*Editor Classico* di WordPress, per chi preferisce non usare l'editor a blocchi

---

## Changelog:

### 1.0.1 — 21 ottobre 2026

**Sicurezza**
* I link dei profili social e i dati della pagina _"Dettagli"_ stampati in intestazione e footer vengono ora sempre filtrati (`esc_url()`, `esc_attr()`, `esc_html()`): un valore malevolo salvato in un campo non può più eseguire codice nelle pagine del sito

**Correzioni**
* **Ricerca per data**: cercando ad esempio _"Settembre 2018"_ o _"12 Settembre 2018"_ si apre l'archivio di quel mese o giorno. Sui siti in italiano non aveva mai funzionato
* **Traduzioni**: tornano in italiano le etichette del form commenti, il breadcrumb della pagina 404, l'istruzione della mappa e i testi letti dagli screen reader
* **WhatsApp**: il link apre la chat WhatsApp (`https://wa.me/…`) e non più il telefono
* I profili social compaiono anche quando sono impostati solo Telegram o WhatsApp
* Le cornici delle immagini mostrano titolo e didascalia corretti anche con due file omonimi caricati in mesi diversi e, con Polylang, nelle pagine in altra lingua (prima compariva il titolo dell'articolo)
* La colonna _"Anteprima"_ nell'elenco delle Pagine in bacheca mostra l'immagine in evidenza
* Eliminati warning e avvisi PHP nelle sezioni delle circolari e nel menu (su PHP 8.1 e successivi)

**Prestazioni**
* Le immagini dei contenuti non eseguono più una scansione completa della tabella `wp_postmeta` per ciascuna immagine
* Ricerca per data con una sola query invece di due
* Le query delle sezioni in homepage e della navigazione fra articoli non calcolano più totali inutilizzati

**Requisiti**
* PHP minimo portato a **8.0**

**Per chi usa un tema child**
* Rimosse le funzioni `my_date_search()` (sostituita da `italiawp3_date_search()`), `italiawp3_sanitize_file()` e `brb_add_editor_styles()`: un tema child che le richiama va aggiornato
* Rimossi i fogli vuoti `inc/admin.css` e `inc/editor.css` e il file `static/js/i18n/datepicker-it.min.js`, non più usato

### 1.0.0

* Prima versione di ItaliaWP3

---

## Licenza:

Copyright (c) 2018-2026 & moral rights [**Boris Amico**](https://borisamico.it).

Il testo integrale della licenza è nel file [`LICENSE.md`](LICENSE.md).

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.

---

## Altre licenze:

* **Bootstrap Italia** [clicca qui](https://github.com/italia/bootstrap-italia/blob/master/LICENSE)
* **jQuery** [clicca qui](https://jquery.org/license/) — il tema utilizza la copia inclusa in WordPress, non ne distribuisce una propria
* Plugin jQuery **Magnific Popup** [clicca qui](https://github.com/dimsemenov/Magnific-Popup)
* Plugin jQuery **Owl Carousel 2** [clicca qui](https://github.com/OwlCarousel2/OwlCarousel2/blob/develop/LICENSE)
* **Leaflet** [clicca qui](https://github.com/Leaflet/Leaflet/blob/main/LICENSE)
* **Theme Update Checker** [clicca qui](https://w-shadow.com/)
* **TGM Plugin Activation** [clicca qui](https://github.com/TGMPA/TGM-Plugin-Activation)
* Plugin WordPress **Attachments** [clicca qui](https://github.com/jchristopher/attachments)
