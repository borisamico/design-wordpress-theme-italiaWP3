# Contribuire a ItaliaWP3

Procedure per chi mantiene il tema. Chi lo installa e basta non ha bisogno di questa
pagina: gli serve il [README](README.md).

---

## Prerequisiti

Nessuno oltre a PHP. Il tema non ha build step, non ha `package.json` e non ha
`node_modules`: gli asset di Bootstrap Italia sono file `dist` vendorizzati in `static/`,
e ogni personalizzazione avviene per sovrascrittura CSS a valle.

I due controlli di manutenzione sono script PHP senza dipendenze:

```bash
php bin/check-css.php          # sintassi dei CSS del tema
php bin/check-bi-version.php   # allineamento delle versioni di Bootstrap Italia
```

`check-css.php` va eseguito dopo **ogni** modifica ai CSS. Un commento malformato — basta
un `*/` di troppo, per esempio scrivendo `m-*/p-*` — chiude il commento in anticipo e il
browser scarta in silenzio tutto il resto del foglio, senza segnalare nulla. È già
successo, con 450 regole su 490 inattive.

---

## Aggiornare Bootstrap Italia

Gli asset si sostituiscono a mano — sono tre file. Scaricare il pacchetto `dist` della
versione desiderata dalle [release ufficiali](https://github.com/italia/bootstrap-italia/releases)
(oppure i singoli file da `https://cdn.jsdelivr.net/npm/bootstrap-italia@X.Y.Z/dist/…`) e
copiare:

| dalla release | nel tema |
|---|---|
| `dist/css/bootstrap-italia.min.css` | `static/css/bootstrap-italia.min.css` |
| `dist/js/bootstrap-italia.bundle.min.js` | `static/js/bootstrap-italia.bundle.min.js` |
| `dist/svg/sprites.svg` | `static/svg/sprite.svg` — **da rinominare** |

Tre avvertenze che fanno risparmiare tempo:

- si prende il **bundle** JS, non `bootstrap-italia.min.js`: dalla 2.x quest'ultimo è UMD e
  dipende da pacchetti esterni (`animejs`, `video.js`, `@popperjs/core`, `@splidejs/splide`)
  che senza bundler non si risolvono. Nel bundle sono già dentro, Popper compreso — che
  infatti non va più accodato a parte;
- lo sprite delle icone all'origine si chiama `sprites.svg` **al plurale**, e va rinominato
  in `sprite.svg` per non toccare i 13 riferimenti sparsi nei template;
- permessi dei file: `644`.

Poi si aggiorna a mano la costante `italiawp3_BOOTSTRAP_ITALIA_VERSION` in
`inc/enqueue.php` — è il _cache buster_: finché resta indietro, i browser continuano a
servire dalla cache il foglio della versione precedente. Infine
`php bin/check-bi-version.php` conferma che i tre punti concordino. Se qualcosa non torna,
lo stesso controllo compare come avviso nella bacheca di WordPress.

Il tema include **Bootstrap Italia 2.18.3** (Bootstrap 5). Il foglio
`static/css/bi1-compat.css` ripristina le metriche della linea 1.x e va accodato **subito
dopo** Bootstrap Italia e **prima** dei fogli del tema: spostarlo rompe la grafica in punti
sparsi. Metodo e strumenti di verifica in `docs/migrazione-bootstrap-italia-v2.md`.

---

## Colori: non scrivere hex di brand nei CSS

Il colore primario lo sceglie l'utente dal Customizer, senza vincoli. `inc/style.php` ne
deriva la palette e ne misura il contrasto, poi emette un blocco `:root` di token **per
ruolo** che i fogli statici consumano come `var(--iwp-…, #hex-originale)`.

Il nome del token dice il ruolo, non la tinta. Lo stesso colore finisce su token diversi a
seconda di come viene usato:

| uso | token | soglia |
|---|---|---|
| fondo pieno | `--iwp-primario` | — |
| testo sopra quel fondo | `--iwp-su-primario` | 4.5:1 |
| testo su fondo chiaro | `--iwp-primario-testo` | 4.5:1 |
| `fill`, `border-color` | `--iwp-primario-icona` | 3:1 |
| velature quasi bianche | `--iwp-primario-tenue` | — |
| seconda tinta (ex oro) | `--iwp-accento` | — |

Regole:

- **Ogni `color:#fff` accoppiato a un fondo derivato va convertito insieme al fondo.** Era
  il difetto originale di `.btn-verde`: fondo fisso più bianco fisso, che con un primario
  chiaro spariva.
- **I fondi non chiari vogliono il loro token.** Cookiebar e footer sono
  `.u-background-80`, non bianco: lì valgono `--iwp-su-primario-80` e
  `--iwp-accento-su-footer`.
- **Restano fissi**, e non vanno "sistemati": i colori SPID (identità di terza parte), le
  utility decorative `.bg-rosa` / `.bg-rosso` / `.bg-azzurro` / `.bg-petrolio` /
  `.bg-grigiomedio`, i neutri e i grigi, i token `--iwp-focus-*` (l'anello di focus deve
  restare visibile *qualunque* colore scelga l'utente, quindi non può dipenderne) e i
  quattro valori di `bi1-compat.css`.
- Le velature semitrasparenti usano
  `color-mix(in srgb, var(--iwp-primario) N%, transparent)`, con la `rgba()` originale come
  dichiarazione precedente di fallback.

Sostituire un hex con una `var()` *dentro* una dichiarazione esistente non tocca selettore
né peso: la cascata del tema resta intatta.

---

## Accessibilità

Il tema è destinato alla PA: l'accessibilità è un obbligo di legge (Legge Stanca 4/2004,
D.Lgs. 106/2018, EN 301 549 → WCAG 2.1 AA), non una preferenza.

Il pannello *Accessibilità* misura circa **un terzo** dei criteri e **non deve mai mostrare
"tutto verde"**. Navigazione da tastiera e screen reader non sono automatizzabili da nessuno
strumento e stanno nella checklist manuale. Se si aggiunge un controllo, deve dichiarare il
criterio WCAG che copre; ciò che non è misurabile va marcato *da verificare a mano* — mai
omesso e mai dichiarato conforme. L'ente firma la dichiarazione su quella base: un falso
verde è peggio di nessun dato.

Due vincoli concreti:

- **Mai `outline: 0` o `outline: none` senza sostituto.** L'indicatore di focus è un doppio
  contorno definito dai token `--iwp-focus-*`: 3px scuri più un alone bianco. Serve a
  restare visibile sia sui fondi chiari sia su quelli scuri, che qui dipendono dal colore
  scelto dall'utente. `box-shadow` da solo non basta: sparisce in Forced Colors Mode e in
  stampa.
- **Ogni `<svg>` decorativo vuole `aria-hidden="true" focusable="false"`.** Se l'icona porta
  un'informazione, quell'informazione va anche in testo — `.sr-only` se non deve vedersi.

Stato di conformità e verifiche aperte in `docs/accessibilita.md`.

---

## Verificare una modifica

Il tema non ha test automatici: la rete di sicurezza è un confronto visivo più uno smoke
test funzionale, da costruire in una directory temporanea fuori dal repo (servono
`puppeteer`, `pngjs`, `pixelmatch`).

1. **Cattura**: screenshot full-page di un insieme di pagine rappresentative × desktop e
   mobile, raccogliendo anche errori console e richieste fallite. Pagine da coprire: home,
   un archivio, un singolo con commenti, una pagina con figli, una galleria, una pagina
   Gutenberg, una pagina con widget di plugin, la 404.
2. **Diff pixel-per-pixel** fra la cattura *prima* e *dopo*.
3. **Smoke test funzionale**: megamenu, menu off-canvas mobile, carosello, lightbox, sprite
   icone, back-to-top, form commenti, e assertion sul caricamento condizionale delle
   librerie.

Prima di catturare, neutralizzare animazioni e transizioni via CSS e impostare il cookie
`cookies_consent`, altrimenti il diff è inutilizzabile.

**Il baseline va validato catturando due volte di seguito senza toccare nulla: deve dare
0 px.** Se non lo dà, l'harness non è pronto e ogni misura successiva è rumore.

Tre trappole già pagate:

- una pagina Gutenberg con un `<video>` remoto non è deterministica: i controlli nativi
  vengono disegnati in modo leggermente diverso a ogni caricamento. Va confrontata con quel
  rumore di riferimento, non con zero;
- la cookiebar è `position: fixed` e in uno screenshot full-page viene dipinta a un'altezza
  che dipende dall'altezza totale della pagina: se una modifica cambia l'altezza, genera una
  banda di differenza che **non è** una regressione. Conviene nasconderla via CSS;
- se il diff mostra una differenza e non è chiaro chi l'abbia causata, **ripristinare solo
  quella modifica** lasciando attive tutte le altre e ricatturare. Se la pagina torna
  identica, il fattore è isolato con certezza — ed è anche il modo per accorgersi che il
  delta veniva da fuori (un contenuto cambiato, un'immagine sostituita) e non dal codice.

Per una modifica ai colori, in più: provare almeno un primario chiarissimo (`#FFD700`) e uno
scurissimo (`#8B0000`), e controllare che nessuna coppia testo/sfondo scenda sotto 4.5:1.

---

## Rilascio

Il tema si auto-aggiorna leggendo `italiawp3.json` nella **root del repository**. Una
release richiede di allineare **insieme**:

1. `style.css` — fonte di verità per WordPress;
2. `italiawp3.json` in root;
3. `italiawp3.zip` rigenerato (e `italiawp3-child.zip` se il tema child cambia).

Se `italiawp3.json` resta indietro rispetto a `style.css` gli aggiornamenti non vengono mai
proposti; se va avanti, vengono proposti all'infinito.

---

## Convenzioni

- Text domain: `italiawp3`.
- Commenti e documentazione **in italiano**, come il resto del progetto.
- I file e le cartelle con prefisso `__` sono backup dell'autore: vanno ignorati e mai
  modificati.
- `inc/tgm-plugin-activation/` è una libreria di terze parti: non modificarla.
