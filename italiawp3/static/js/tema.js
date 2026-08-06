jQuery(function ($) {

    /*
     * Movimento ridotto (WCAG 2.3.3). Gli scorrimenti e gli slideUp/slideDown di questo
     * file sono animazioni jQuery: non passano dal CSS, quindi la media query in
     * tema.css non le tocca. $.fx.off le rende istantanee mantenendo i callback.
     */
    if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        $.fx.off = true;
    }

    $.fn.jPushMenu = function (customOptions) {
        var o = $.extend({}, $.fn.jPushMenu.defaultOptions, customOptions);

        $('body').addClass(o.pushBodyClass);

        // Add class to toggler
        $(this).addClass('jPushMenuBtn');

        $(this).click(function (e) {
            e.stopPropagation();

            var target = '',
                    push_direction = '';

            // Determine menu and push direction
            if ($(this).is('.' + o.showLeftClass)) {
                target = '.cbp-spmenu-left';
                push_direction = 'toright';
            } else if ($(this).is('.' + o.showRightClass)) {
                target = '.cbp-spmenu-right';
                push_direction = 'toleft';
            } else if ($(this).is('.' + o.showTopClass)) {
                target = '.cbp-spmenu-top';
            } else if ($(this).is('.' + o.showBottomClass)) {
                target = '.cbp-spmenu-bottom';
            }

            if (target == '') {
                return;
            }

            $(this).toggleClass(o.activeClass);
            $(target).toggleClass(o.menuOpenClass);

            if ($(this).is('.' + o.pushBodyClass) && push_direction != '') {
                $('body').toggleClass(o.pushBodyClass + '-' + push_direction);
            }

            // Disable all other buttons
            $('.jPushMenuBtn').not($(this)).toggleClass('disabled');

            // Accessibilita': lo stato del pannello va esposto agli AT (WCAG 4.1.2) e il
            // focus va spostato dentro il menu all'apertura e riportato sul pulsante alla
            // chiusura (WCAG 2.4.3). Senza, il menu si apriva "sopra" la pagina ma il
            // focus restava nel body sottostante.
            var isOpen = $(target).hasClass(o.menuOpenClass);
            $(this).attr('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) {
                $.fn.jPushMenu.lastToggler = this;
                $(target).attr('tabindex', '-1').trigger('focus');
            } else if ($.fn.jPushMenu.lastToggler) {
                $($.fn.jPushMenu.lastToggler).trigger('focus');
                $.fn.jPushMenu.lastToggler = null;
            }

            return;
        });

        var jPushMenu = {
            close: function (o) {
                $('.jPushMenuBtn,body,.cbp-spmenu')
                        .removeClass('disabled ' + o.activeClass + ' ' + o.menuOpenClass + ' ' + o.pushBodyClass + '-toleft ' + o.pushBodyClass + '-toright');
                $('.push_container').css('position', 'relative');
                $('.jPushMenuBtn').attr('aria-expanded', 'false');

                if ($.fn.jPushMenu.lastToggler) {
                    $($.fn.jPushMenu.lastToggler).trigger('focus');
                    $.fn.jPushMenu.lastToggler = null;
                }
            }
        }

        // Close menu on clicking outside menu
        if (o.closeOnClickOutside) {
            $(document).click(function () {
                jPushMenu.close(o);
            });
        }

        // Close menu on clicking menu link
        if (o.closeOnClickLink) {
            $('.cbp-spmenu a').on('click', function () {
                if ($(this).hasClass('preventclick')) {
                    return;
                }

                // @modifica : === FIX MENU MOBILE LINK ===
                var node = $(this).is('a') ? $(this) : $(this).closest('a');

                /* Clear all open and not nested element  */
                if (!$(this).closest('li.open').length) {
                    $('.navmenu ul').slideUp(300);
                    $('.navmenu li').removeClass('open');
                }

                if (node.siblings('ul').is(':visible')) {
                    node.siblings('ul').slideUp(300);
                    node.parent().removeClass('open');
                } else {
                    node.siblings('ul').slideDown(300);
                    node.parent().addClass('open');
                }
                // @modifica : === FINE FIX MENU MOBILE LINK ===

                // @modifica fix voce menu con sottovoci figli che non si apre su mobile !
                // jPushMenu.close(o);
            });
        }

        // Chiusura al solo focus: disattivata per default (vedi defaultOptions).
        // Spostare il focus su .logo_wrapper a chiudeva il menu, cioe' un cambio di
        // contesto provocato dal solo focus — WCAG 3.2.1 (On Focus).
        if (o.closeOnFocus) {
            $('.logo_wrapper a').on('focus', function () {
                jPushMenu.close(o);
            });
        }

        // ESC chiude il pannello, e il focus resta confinato al suo interno finche' e'
        // aperto (WCAG 2.1.2, No Keyboard Trap letto al contrario: senza confinamento il
        // Tab usciva silenziosamente sul contenuto sottostante, invisibile e coperto).
        $(document).on('keydown.jPushMenu', function (e) {
            var $panel = $('.cbp-spmenu.' + o.menuOpenClass);

            if (!$panel.length) {
                return;
            }

            if (e.which === 27) { // ESC
                jPushMenu.close(o);
                return false;
            }

            if (e.which !== 9) { // Tab
                return;
            }

            var $stops = $panel
                    .find('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]')
                    .filter(':visible')
                    .filter(function () {
                        return this.tabIndex >= 0;
                    });

            if (!$stops.length) {
                return;
            }

            var first = $stops.first()[0];
            var last = $stops.last()[0];

            if (e.shiftKey && (e.target === first || e.target === $panel[0])) {
                last.focus();
                return false;
            }

            if (!e.shiftKey && e.target === last) {
                first.focus();
                return false;
            }
        });

    };

    $.fn.jPushMenu.defaultOptions = {
        pushBodyClass: 'push-body',
        showLeftClass: 'menu-left',
        showRightClass: 'menu-right',
        showTopClass: 'menu-top',
        showBottomClass: 'menu-bottom',
        activeClass: 'menu-active',
        menuOpenClass: 'menu-open',
        closeOnClickOutside: false,
        closeOnClickLink: true,
        closeOnFocus: false
    };

    $(document).ready(function () {
        $("#topcontrol").click(function () {
            $('body,html').animate({scrollTop: 0}, 500);
        });

        $("#searchclear").click(function () {
            $("#cerca-txt").val('');
        });

        /* Menu navigazione */
        $('.navmenu ul').each(function () {
            var listItem = $(this).closest('li');
            var itemLink = listItem.find('> a');
            var title = 'Espandi il menu ' + $.trim(itemLink.text());
            var trigger = $('<span></span>').attr({
                tabindex: 0,
                'aria-label': title,
                'title': title,
            });

            $(listItem).find('[href^="#"]')
                    .add(trigger)
                    .on('click', function (ev) {
                        var node = $(this).is('a') ? $(this) : $(this).closest('a');

                        /* Clear all open and not nested element  */
                        if (!$(this).closest('li.open').length) {
                            $('.navmenu ul').slideUp(300);
                            $('.navmenu li').removeClass('open');
                        }

                        if (node.siblings('ul').is(':visible')) {
                            node.siblings('ul').slideUp(300);
                            node.parent().removeClass('open');
                        } else {
                            node.siblings('ul').slideDown(300);
                            node.parent().addClass('open');
                        }
                        return false;
                    })
                    .on('keydown', function (e) {
                        if (e.which === 13 || e.which === 32) {
                            $(this).click();
                            return false;
                        }
                    });

            itemLink.append(trigger);
        });

        $('.navmenu .current').parents().filter(function (index) {
            return (this.nodeName === 'LI');
        }).addClass('active open');

        /* jPush Menu */
        $('.navbar-toggle').jPushMenu();
        $(".navbar-toggle").click(function (event) {
            event.preventDefault();
        });
        /* End jPush Menu */

        $('.megamenu .dropdown-menu').click(function (e) {
            e.stopPropagation();
        });
        
    });

    $(window).on("scroll touchmove", function () {
        $('#mainheader').toggleClass('ridotto', $(document).scrollTop() > 40);
        $('body').toggleClass('ridotto', $(document).scrollTop() > 40);
        $('#topcontrol').toggleClass('visualizza', $(document).scrollTop() > 300);
    });

});