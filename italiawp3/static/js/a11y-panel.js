/**
 * Pannello Accessibilita: controlli che richiedono una pagina resa.
 *
 * Contrasto, focus e reflow dipendono dagli stili calcolati, che il PHP non vede.
 * Le pagine si caricano in un <iframe> dello stesso dominio: contentDocument e'
 * accessibile, quindi getComputedStyle funziona. Nessun servizio esterno, nessun
 * dato che esce dal sito, nessuna dipendenza.
 *
 * DUE TRAPPOLE GIA' PAGATE, riportate qui perche' non si ripaghino:
 *
 *  1. Il fondo non e' sempre un antenato. Nel tema, .galleriasfondo e' largo 200vw
 *     e dipinto DIETRO al carosello senza esserne antenato: risalendo il DOM ci si
 *     ferma al <body> bianco e si conclude "bianco su bianco" dove a schermo il
 *     testo e' bianco su grigio scuro. Serve elementsFromPoint, controllando pero'
 *     PRIMA lo sfondo dell'elemento stesso.
 *
 *  2. offsetParent non e' un test di visibilita'. E' sempre null per gli elementi
 *     position:fixed, e la cookie bar lo e': un primo giro di misure l'aveva
 *     dichiarata nascosta mentre era perfettamente visibile.
 */
(function () {
	'use strict';

	var cfg = window.italiawp3A11y || {};
	var SOGLIA_TESTO = 4.5;
	var SOGLIA_GRANDE = 3.0;
	var SOGLIA_FOCUS = 3.0;

	// Sentinella: lo sfondo e' un'immagine, quindi il contrasto non e' un numero.
	var SFONDO_IMMAGINE = 'immagine';

	/* --------------------------------------------------------------- utilita' */

	function post(azione, dati) {
		var corpo = new URLSearchParams();
		corpo.append('action', azione);
		corpo.append('nonce', cfg.nonce);
		Object.keys(dati || {}).forEach(function (k) {
			corpo.append(k, dati[k]);
		});

		return fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: corpo.toString()
		}).then(function (r) {
			return r.json();
		});
	}

	function stato(id, testo) {
		var el = document.getElementById(id);
		if (el) {
			el.textContent = testo;
		}
	}

	/* ------------------------------------------------- 1. controlli statici PHP */

	function avviaHtml() {
		var bottone = document.getElementById('iwp-a11y-avvia-html');
		bottone.disabled = true;

		function passo(indice) {
			return post('italiawp3_a11y_html', { indice: indice }).then(function (r) {
				if (!r || !r.success) {
					stato('iwp-a11y-stato-html', (r && r.data && r.data.messaggio) || cfg.i18n.errore);
					bottone.disabled = false;
					return;
				}
				stato(
					'iwp-a11y-stato-html',
					cfg.i18n.pagina + ' ' + r.data.indice + ' ' + cfg.i18n.di + ' ' + r.data.totale
				);
				if (!r.data.finita) {
					return passo(r.data.indice);
				}
				stato('iwp-a11y-stato-html', cfg.i18n.completato);
				window.location.reload();
			});
		}

		stato('iwp-a11y-stato-html', cfg.i18n.avvio);
		passo(0);
	}

	/* ------------------------------------------------ 2. scansione dei contenuti */

	function avviaContenuti() {
		var bottone = document.getElementById('iwp-a11y-avvia-contenuti');
		bottone.disabled = true;

		function passo(fase, offset) {
			return post('italiawp3_a11y_contenuti', { fase: fase, offset: offset }).then(function (r) {
				if (!r || !r.success) {
					stato('iwp-a11y-stato-contenuti', (r && r.data && r.data.messaggio) || cfg.i18n.errore);
					bottone.disabled = false;
					return;
				}
				stato(
					'iwp-a11y-stato-contenuti',
					r.data.fase + ': ' + r.data.esaminati + ' ' + cfg.i18n.di + ' ' + r.data.totale
				);
				if (!r.data.finita) {
					return passo(r.data.prossima, r.data.offset);
				}
				stato('iwp-a11y-stato-contenuti', cfg.i18n.completato);
				window.location.reload();
			});
		}

		stato('iwp-a11y-stato-contenuti', cfg.i18n.avvio);
		passo('contenuti', 0);
	}

	/* ---------------------------------------------------- 3. controlli sul reso */

	// Luminanza relativa secondo WCAG.
	function luminanza(rgb) {
		var c = rgb.map(function (v) {
			v = v / 255;
			return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
		});

		return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
	}

	function rapporto(a, b) {
		var la = luminanza(a);
		var lb = luminanza(b);

		return (Math.max(la, lb) + 0.05) / (Math.min(la, lb) + 0.05);
	}

	function leggiColore(s) {
		var m = s && s.match(/rgba?\(([^)]+)\)/);
		if (!m) {
			return null;
		}
		var p = m[1].split(',').map(function (x) {
			return parseFloat(x);
		});

		return { rgb: [p[0], p[1], p[2]], alpha: p.length > 3 ? p[3] : 1 };
	}

	// Fonde un colore semitrasparente sul suo fondo.
	function fondi(sopra, sotto) {
		return sopra.rgb.map(function (v, i) {
			return Math.round(v * sopra.alpha + sotto[i] * (1 - sopra.alpha));
		});
	}

	/**
	 * Sfondo effettivo di un elemento.
	 *
	 * Prima si guarda l'elemento stesso; se e' trasparente si usa elementsFromPoint,
	 * che vede l'ordine di disegno reale e non solo la catena degli antenati. E' la
	 * trappola 1: .galleriasfondo dipinge dietro senza essere antenato.
	 */
	function sfondoEffettivo(doc, win, el) {
		var proprio = leggiColore(win.getComputedStyle(el).backgroundColor);
		if (proprio && proprio.alpha === 1) {
			return proprio.rgb;
		}

		// elementsFromPoint lavora in coordinate del viewport: un punto fuori dal
		// viewport non restituisce nulla. Non si schiaccia pero' il punto dentro al
		// viewport, che era l'errore originale — cadeva su un elemento a caso e
		// produceva rapporti 1.00:1, il colore contro se stesso. Si campiona invece
		// il centro della PORZIONE VISIBILE dell'elemento: dentro all'elemento e
		// dentro allo schermo. Serve per le slide del carosello, che owl trasla
		// lasciandone sporgere solo un pezzo.
		var r = el.getBoundingClientRect();
		var sx = Math.max(r.left, 0);
		var dx = Math.min(r.right, win.innerWidth);
		var sy = Math.max(r.top, 0);
		var dy = Math.min(r.bottom, win.innerHeight);

		if (dx <= sx || dy <= sy) {
			return risaliSfondo(win, el);
		}

		var x = (sx + dx) / 2;
		var y = (sy + dy) / 2;

		var pila = doc.elementsFromPoint(x, y) || [];
		for (var i = 0; i < pila.length; i++) {
			var sotto = pila[i];

			// Sotto al testo c'e' un'immagine: il contrasto non e' un numero solo,
			// dipende da ogni pixel della fotografia. Nessuno strumento automatico
			// puo' stabilirlo — va guardato a mano, ed e' quello che il pannello
			// dira'. Dichiararlo fallito sarebbe falso quanto dichiararlo conforme.
			if (/^(IMG|VIDEO|CANVAS|PICTURE)$/.test(sotto.tagName)) {
				return SFONDO_IMMAGINE;
			}
			var stile = win.getComputedStyle(sotto);
			if (stile.backgroundImage && stile.backgroundImage !== 'none' &&
				stile.backgroundImage.indexOf('url(') !== -1) {
				return SFONDO_IMMAGINE;
			}

			var c = leggiColore(stile.backgroundColor);
			if (c && c.alpha === 1) {
				return c.rgb;
			}
		}

		return risaliSfondo(win, el);
	}

	/**
	 * Ripiego: si risale la catena degli antenati.
	 *
	 * Da solo non basta — e' la trappola 1 — ma serve quando il punto non e'
	 * misurabile, ed e' comunque meglio di un bianco inventato.
	 */
	function risaliSfondo(win, el) {
		var nodo = el.parentElement;
		while (nodo) {
			var c = leggiColore(win.getComputedStyle(nodo).backgroundColor);
			if (c && c.alpha === 1) {
				return c.rgb;
			}
			nodo = nodo.parentElement;
		}

		return [255, 255, 255];
	}

	/**
	 * Visibilita'. NON usa offsetParent: e' sempre null per position:fixed, e la
	 * cookie bar lo e' (trappola 2).
	 *
	 * Esclude anche cio' che e' nascosto alla vista ma esposto agli screen reader —
	 * .sr-only e simili, ritagliati con clip/clip-path a un pixel. Misurarne il
	 * contrasto non ha senso: nessuno li guarda. Senza questo controllo il pannello
	 * segnalava come illeggibili gli skiplink e le etichette per screen reader,
	 * cioe' esattamente gli elementi messi li' per l'accessibilita'.
	 */
	function visibile(win, el) {
		var s = win.getComputedStyle(el);
		if (s.display === 'none' || s.visibility === 'hidden' || parseFloat(s.opacity) === 0) {
			return false;
		}

		// Ritaglio a zero: la tecnica classica di .sr-only.
		if (s.clip && /rect\(\s*0(px)?[\s,]+0(px)?[\s,]+0(px)?[\s,]+0(px)?\s*\)/.test(s.clip)) {
			return false;
		}
		if (s.clipPath && s.clipPath !== 'none' && /inset\(\s*(100%|50%)/.test(s.clipPath)) {
			return false;
		}

		var r = el.getBoundingClientRect();

		// Un riquadro di un pixel con overflow nascosto non e' testo che si legge.
		if (r.width <= 2 || r.height <= 2) {
			return false;
		}

		// Fuori dallo schermo in orizzontale: e' il caso delle slide del carosello,
		// che owl trasla a sinistra con una transform (x = -768 su una slide non
		// attiva). Non sono visibili adesso, e misurarle dava 1.00:1 — il colore
		// contro se stesso — perche' nessun punto misurabile le copriva.
		if (r.right <= 0 || r.left >= win.innerWidth) {
			return false;
		}

		return true;
	}

	function descrivi(el) {
		var s = el.tagName.toLowerCase();
		if (el.id) {
			s += '#' + el.id;
		}
		if (el.className && typeof el.className === 'string') {
			s += '.' + el.className.trim().split(/\s+/).slice(0, 2).join('.');
		}

		return s;
	}

	function controllaContrasto(doc, win) {
		var problemi = [];
		var sopraImmagine = [];
		var visti = 0;
		var elementi = doc.body.querySelectorAll('*');

		for (var i = 0; i < elementi.length && visti < 1200; i++) {
			var el = elementi[i];

			// Solo elementi con testo proprio.
			var testo = '';
			for (var n = 0; n < el.childNodes.length; n++) {
				if (el.childNodes[n].nodeType === 3) {
					testo += el.childNodes[n].nodeValue;
				}
			}
			testo = testo.trim();
			if (!testo || !visibile(win, el)) {
				continue;
			}
			visti++;

			var s = win.getComputedStyle(el);
			var primo = leggiColore(s.color);
			if (!primo) {
				continue;
			}
			var sfondo = sfondoEffettivo(doc, win, el);

			if (sfondo === SFONDO_IMMAGINE) {
				sopraImmagine.push(descrivi(el) + ' «' + testo.slice(0, 30) + '»');
				continue;
			}

			var colore = primo.alpha < 1 ? fondi(primo, sfondo) : primo.rgb;

			var px = parseFloat(s.fontSize);
			var grassetto = parseInt(s.fontWeight, 10) >= 700;
			var grande = px >= 24 || (grassetto && px >= 18.66);
			var soglia = grande ? SOGLIA_GRANDE : SOGLIA_TESTO;

			var r = rapporto(colore, sfondo);
			if (r < soglia) {
				problemi.push(
					descrivi(el) + ' — ' + r.toFixed(2) + ':1 (min ' + soglia + ') «' + testo.slice(0, 30) + '»'
				);
			}
			if (problemi.length >= 25) {
				break;
			}
		}

		if (sopraImmagine.length) {
			problemi.push(
				'DA VERIFICARE A MANO — ' + sopraImmagine.length +
				' testi sopra un\'immagine: il contrasto dipende dalla fotografia, ' +
				'nessuno strumento puo\' misurarlo. Es.: ' + sopraImmagine.slice(0, 3).join('; ')
			);
		}

		return problemi;
	}

	/**
	 * Regole che spengono il contorno di focus senza metterci nulla al posto.
	 *
	 * NON si misura il contorno reso, e la ragione va detta: il tema disegna il
	 * focus con :focus-visible, che si attiva solo su focus da tastiera. Un
	 * element.focus() da JavaScript non lo attiva, e nemmeno focus({focusVisible:true});
	 * dentro un iframe non e' possibile sintetizzare un Tab autentico, perche' gli
	 * eventi generati da script non sono fidati. Verificato: con .focus() il contorno
	 * risulta 'none', con un Tab vero risulta 'solid 3px'. Misurarlo cosi' avrebbe
	 * dichiarato privi di indicatore proprio gli skiplink, cioe' gli elementi messi
	 * li' per l'accessibilita'.
	 *
	 * Cio' che invece si puo' accertare, ed e' il difetto che nella pratica ricorre,
	 * e' che un foglio di stile azzeri outline senza sostituto. La visibilita' vera
	 * dell'indicatore resta una voce della checklist manuale, dove deve stare.
	 */
	/**
	 * Il selettore colpisce solo elementi non raggiungibili col Tab?
	 *
	 * Si guarda il documento vero invece di riconoscere i selettori per nome: cosi'
	 * vale anche per i bersagli che un altro sito, o un plugin, chiama diversamente.
	 *
	 * @param {Document} doc
	 * @param {string} selettore
	 * @return {boolean} false anche quando non si riesce a stabilirlo, per non
	 *                   nascondere un difetto vero.
	 */
	function soloProgrammatici(doc, selettore) {
		var senzaFocus = selettore.replace(/:focus(-visible|-within)?/g, '');
		var trovati;

		try {
			trovati = doc.querySelectorAll(senzaFocus);
		} catch (e) {
			return false;
		}
		if (!trovati.length) {
			return false;
		}

		for (var i = 0; i < trovati.length; i++) {
			if (trovati[i].getAttribute('tabindex') !== '-1') {
				return false;
			}
		}

		return true;
	}

	function controllaFocus(doc) {
		var problemi = [];

		for (var i = 0; i < doc.styleSheets.length; i++) {
			var foglio = doc.styleSheets[i];

			// Il CSS vendorizzato di bootstrap-italia viene sovrascritto a valle dai
			// fogli del tema: segnalarlo produrrebbe righe su cui nessuno puo'
			// intervenire, che e' il modo piu' rapido per far ignorare il pannello.
			if (foglio.href && foglio.href.indexOf('bootstrap-italia') !== -1) {
				continue;
			}

			var regole;
			try {
				regole = foglio.cssRules;
			} catch (e) {
				continue;                    // foglio di un'altra origine: non leggibile
			}
			if (!regole) {
				continue;
			}

			for (var j = 0; j < regole.length; j++) {
				var regola = regole[j];
				if (!regola.style || !regola.selectorText) {
					continue;
				}
				if (regola.selectorText.indexOf('focus') === -1) {
					continue;
				}

				// :not(:focus-visible) spegne il contorno quando il focus NON arriva
				// da tastiera: e' esattamente cio' che si deve fare, non un difetto.
				if (regola.selectorText.indexOf(':not(:focus-visible)') !== -1) {
					continue;
				}

				// Elementi con tabindex="-1": ricevono il focus solo per programma —
				// e' il caso dei bersagli degli skiplink, <main> e le due <nav> — e
				// col Tab non ci si arriva mai. Un contorno intorno all'intero
				// contenuto sarebbe rumore visivo, non un indicatore di focus.
				if (soloProgrammatici(doc, regola.selectorText)) {
					continue;
				}

				var stile = regola.style.getPropertyValue('outline-style');
				var larghezza = regola.style.getPropertyValue('outline-width');
				var scorciatoia = regola.style.getPropertyValue('outline');
				var azzerato =
					stile === 'none' ||
					/^0(px)?$/.test(larghezza.trim()) ||
					/^\s*(0(px)?|none)\s*$/.test(scorciatoia);

				if (!azzerato) {
					continue;
				}

				// Un sostituto vale se si vede anche in Forced Colors Mode e in stampa:
				// un bordo lo e', un box-shadow da solo no.
				var sostituto =
					regola.style.getPropertyValue('border') ||
					regola.style.getPropertyValue('border-color') ||
					regola.style.getPropertyValue('border-width') ||
					regola.style.getPropertyValue('outline-offset');

				var ombra = regola.style.getPropertyValue('box-shadow');

				if (!sostituto) {
					problemi.push(
						regola.selectorText.slice(0, 60) + ' — azzera outline' +
						(ombra ? ' e lascia solo box-shadow (sparisce in Forced Colors e in stampa)' : ' senza sostituto')
					);
				}

				if (problemi.length >= 10) {
					return problemi;
				}
			}
		}

		return problemi;
	}

	function controllaReflow(doc) {
		var e = doc.documentElement;
		var scarto = e.scrollWidth - e.clientWidth;

		return scarto > 1
			? scarto + 'px di scorrimento orizzontale'
			: 'ok';
	}

	function caricaIframe(url, larghezza) {
		return new Promise(function (risolvi, rifiuta) {
			var culla = document.getElementById('iwp-a11y-culla');
			var iframe = document.createElement('iframe');
			iframe.width = larghezza;
			iframe.height = 900;
			iframe.style.border = '0';
			iframe.src = url;

			var scaduto = setTimeout(function () {
				iframe.remove();
				rifiuta(new Error('timeout'));
			}, 30000);

			iframe.addEventListener('load', function () {
				clearTimeout(scaduto);
				// Un giro di rendering, cosi' i font e gli stili sono applicati.
				setTimeout(function () {
					// L'iframe si stiracchia all'altezza dell'intero documento: cosi'
					// ogni elemento sta nel viewport e elementsFromPoint funziona anche
					// sotto la piega. Con un iframe di 900px su una pagina di 4000 tutto
					// cio' che stava piu' in basso veniva misurato sul punto sbagliato.
					try {
						var doc = iframe.contentDocument;
						var altezza = Math.max(
							doc.documentElement.scrollHeight,
							doc.body ? doc.body.scrollHeight : 0
						);
						if (altezza > 900) {
							iframe.height = Math.min(altezza, 12000);
						}
					} catch (e) {
						// Same-origin garantito dal campione, ma non si rischia comunque.
					}
					setTimeout(function () {
						risolvi(iframe);
					}, 250);
				}, 400);
			});
			iframe.addEventListener('error', function () {
				clearTimeout(scaduto);
				iframe.remove();
				rifiuta(new Error('error'));
			});

			culla.appendChild(iframe);
		});
	}

	function preparaDocumento(doc) {
		// La cookie bar e' position:fixed e copre il contenuto: va neutralizzata,
		// altrimenti falsa i contrasti misurati sotto di essa.
		doc.cookie = 'cookies_consent=true; path=/';
		var stile = doc.createElement('style');
		stile.textContent =
			'*,*::before,*::after{animation:none!important;transition:none!important}' +
			'.cookiebar,#cookiebar{display:none!important}';
		doc.documentElement.appendChild(stile);
	}

	function avviaResa() {
		var bottone = document.getElementById('iwp-a11y-avvia-resa');
		bottone.disabled = true;

		var pagine = cfg.campione || [];
		var esiti = {};

		function passo(i) {
			if (i >= pagine.length) {
				stato('iwp-a11y-stato-resa', cfg.i18n.completato);

				return post('italiawp3_a11y_resa', { esiti: JSON.stringify(esiti) }).then(function () {
					window.location.reload();
				});
			}

			stato('iwp-a11y-stato-resa', cfg.i18n.pagina + ' ' + (i + 1) + ' ' + cfg.i18n.di + ' ' + pagine.length);

			return caricaIframe(pagine[i], 1280)
				.then(function (iframe) {
					var doc = iframe.contentDocument;
					var win = iframe.contentWindow;
					preparaDocumento(doc);

					var risultato = {
						contrasto: controllaContrasto(doc, win),
						focus: controllaFocus(doc),
						reflow: ''
					};

					// Reflow: si stringe l'iframe a 320px e si guarda se compare
					// scorrimento orizzontale (WCAG 1.4.10).
					iframe.width = 320;

					return new Promise(function (r) {
						setTimeout(function () {
							risultato.reflow = controllaReflow(doc);
							iframe.remove();
							esiti[pagine[i]] = risultato;
							r();
						}, 500);
					});
				})
				.catch(function () {
					esiti[pagine[i]] = { contrasto: [], focus: [], reflow: cfg.i18n.errore };
				})
				.then(function () {
					return passo(i + 1);
				});
		}

		passo(0);
	}

	/* ------------------------------------------------- 4. checklist manuale */

	function collegaChecklist() {
		document.querySelectorAll('tr[data-voce]').forEach(function (riga) {
			var voce = riga.getAttribute('data-voce');
			var select = riga.querySelector('.iwp-a11y-stato');
			var note = riga.querySelector('.iwp-a11y-note');
			var cella = riga.querySelector('.iwp-a11y-data');
			var attesa = null;

			function salva() {
				post('italiawp3_a11y_checklist', {
					voce: voce,
					stato: select.value,
					note: note.value
				}).then(function (r) {
					if (r && r.success && cella) {
						var d = new Date();
						cella.innerHTML =
							d.toLocaleDateString() + ' ' +
							d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) +
							(r.data.utente ? '<br><span style="color:#646970">' + r.data.utente + '</span>' : '');
					}
				});
			}

			if (select) {
				select.addEventListener('change', salva);
			}
			if (note) {
				note.addEventListener('input', function () {
					clearTimeout(attesa);
					attesa = setTimeout(salva, 700);
				});
			}
		});
	}

	/* ------------------------------------------------------------- avvio */

	document.addEventListener('DOMContentLoaded', function () {
		var bHtml = document.getElementById('iwp-a11y-avvia-html');
		var bResa = document.getElementById('iwp-a11y-avvia-resa');
		var bCont = document.getElementById('iwp-a11y-avvia-contenuti');

		if (bHtml) {
			bHtml.addEventListener('click', avviaHtml);
		}
		if (bResa) {
			bResa.addEventListener('click', avviaResa);
		}
		if (bCont) {
			bCont.addEventListener('click', avviaContenuti);
		}
		collegaChecklist();
	});
})();
