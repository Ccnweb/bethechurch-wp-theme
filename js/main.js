jQuery(document).ready(function($) {

    // ========================================
    // HREF IMAGE BLOCKS
    // ========================================

    // here we allow users to click on wp-block-cover to be able to go to the a href
    /* jQuery('.wp-block-columns.is-style-squares .wp-block-cover a').each(function() {
        let url = jQuery(this).attr('href');
        console.log('galerie url : ', url, jQuery(this).closest('.wp-block-cover'))
        jQuery(this).closest('.wp-block-cover').click(function() {
            window.location.href = url;
            jQuery(this).closest('.wp-block-column').addClass('goto');
            jQuery(this).find('.wp-block-cover__inner-container').html('');
            jQuery(this).find('.wp-block-cover__inner-container').addClass('goto');
        })
    }) */
    jQuery('.wp-block-columns.is-style-squares .wp-block-cover h3').each(function() {
        let url = jQuery(this).find('a').attr('href');
        let me = $(this).clone()
        me.addClass('square-title')
        jQuery(this).closest('.wp-block-cover').prepend(me)
        // jQuery(this).closest('.wp-block-cover').prepend('<h3 class="square-title">' + $(this).html() + '</h3>')
        jQuery(this).closest('.wp-block-cover').click(function() {
            window.location.href = url;  
        })
    })

    // ========================================
    // ANIMER LES GOUTTES
    // ========================================

    function anim_gouttes() {
        // get gouttes positions
        let section = jQuery('section');
        if (section.length == 0) return;
        let gouttes = section.eq(0).find('img.goutte');

        let positions = gouttes.map(function() {
            return jQuery(this).position();
        }).toArray();
        console.log('POS', positions);
        // compute gouttes center
        let bary = positions.reduce((a,b) => {return {top:a.top+b.top, left:a.left+b.left}}, {top:0,left:0});
        bary.top /= positions.length;
        bary.left /=positions.length;
        console.log('BAR',bary);
        // move gouttes
        gouttes.each(function() {
            let pos = jQuery(this).position();
            let r_top = (pos.top - bary.top)*0.05;
            let r_left = (pos.left - bary.left)*0.05;
            jQuery(this).animate({
                top: pos.top + r_top,
                left: pos.left + r_left,
            }, { 
                duration: 400, 
                easing: "swing", 
            }).animate({
                top: pos.top,
                left: pos.left,
            }, { 
                duration: 200, 
                easing: "swing", 
            })
        })
    }
    setTimeout(anim_gouttes, 500);

    // ========================================
    // INITIALISE FLECHE POUR CHANGER DE SLIDE
    // ========================================

    // on détecte où sont toutes les sections
    let sections_top = [];
    jQuery('.section, main > .wp-block-group').each(function() {sections_top.push($(this).offset().top)});

    // quand on clique sur la flèche, elle va au prochain slide
    $('.fleche_slide_suivant > i').click(function() {
        start = $(window).scrollTop();
        //$('html, body, .page').animate( { scrollTop: start + $(window).height() }, 400 );

        for (let top_position of sections_top) {
            if (start+2 < top_position) {
                $('html, body, .page').animate( { scrollTop: top_position }, 400 );
                break;
            }
        }
    })

    // ========================================
    // INITIALISE CONTRÔLE DU DEFILEMENT
    // ========================================

    jQuery(window).scroll(function() {
      let start = jQuery(window).scrollTop();
      if (start+2 >= sections_top[sections_top.length-1]) $('.fleche_slide_suivant').hide();
      else $('.fleche_slide_suivant').show();
    });

    // ========================================
    // INITIALISE CONTRÔLE DU DEFILEMENT DES LISTES DE CARRES
    // ========================================

    jQuery('.scroll-arrow.arrow-prev').each(function() {
        let step = jQuery(this).next().find('div.carre').eq(0).width() + 10;
        jQuery(this).click(function() {
            let container = jQuery(this).next();
            container.animate( { scrollLeft: container.scrollLeft() - step}, 300);
        })
    })
    jQuery('.scroll-arrow.arrow-next').each(function() {
        let step = jQuery(this).prev().find('div.carre').eq(0).width() + 10;
        jQuery(this).click(function() {
            let container = jQuery(this).prev();
            container.animate( { scrollLeft: container.scrollLeft() + step}, 300);
        })
    })

    // ========================================
    // Appearance animations on scroll
    // ========================================

    jQuery('.translate-appear').each(function(i) {
        if (jQuery(this).position().top > jQuery(window).height()) jQuery(this).addClass((i % 2 == 0) ? 'translate-hide-left' : 'translate-hide-right' )
    })
    let windowHeight = jQuery(window).height();
    jQuery(window).on('scroll', function() {
        let scrollPos = jQuery(window).scrollTop();
        jQuery('.translate-hide-left, .translate-hide-right').each(function() {
            if (jQuery(this).position().top <= windowHeight + scrollPos) jQuery(this).removeClass('translate-hide-left').removeClass('translate-hide-right')
        })
    })

    // ========================================
    // Corrections HTML à la volée
    // ========================================

    // ici on cache le bouton des inscriptions si les inscriptions sont fermées
    let btn_inscr = $(".reserver_place");
    let inscr_status = (btn_inscr.length) ? btn_inscr.find('a').attr('rel') : 'INSCRIPTIONS FERMÉES';
    if (inscr_status == "INSCRIPTIONS FERMÉES") {
        btn_inscr.hide();
    } else if (inscr_status == 'PRÉ-INSCRIPTION') {
        $('#inscription_btn').css({display: "none"})
    } else if (inscr_status == 'INSCRIPTION') {
        $('#ccnbtc_preinscr_preinscrire_form').hide();
    }

    // ici on customize un peu le design des horaires pour les journées type :
    // quand on écrit "800 mon titre", l'horaire est transformé pour être plus joli
    $('h3,li').each(function() {
        let texte = $(this).text();
        if (/^[0-9]{2,3}/gi.test(texte)) {
            let regex_res = /^([0-9]{3,4})\s+(.*)$/gi.exec(texte);
            if (regex_res && regex_res.length > 2) {
                let hours = regex_res[1].substr(0, regex_res[1].length-2);
                let minutes = regex_res[1].substr(regex_res[1].length-2);
                let horaire = `<span class="has-text-color has-jaune-color">${hours}<sup>${minutes}</sup></span>`;
                $(this).html(horaire + ' ' + regex_res[2]);
            }
        }
    })

    // ici on transforme les éléments .transform-double-title en doubles titres
    $('.transform-double-title').each(function() {
        let titre = $(this).text();
        $(this).html( `<div class="col-sm-12 double_title  mt-auto">
            <h2 class="w-100 text-center point ">${titre}</h2>
            <h2 class="w-100 text-center point hollow ">${titre}</h2>
        </div>`)
    })

    // ========================================
    // Points d'Ariane verticaux pour la navigation (à déplacer dans un autre fichier)
    // ========================================

    function initArianePoints(section_selector = '.section', options = {}) {
        /**
         * @param string section_selector   Le sélecteur jquery pour sélectionner toutes les parties qui doivent avoir un point d'Ariane
         * 
         * ## SOMMAIRE
         * 0. Préparation des fonctions utiles pour la suite
         * 1. On récupère tous les éléments HTML correspondants à des sections (1 section = 1 point d'ariane)
         * 2. On ajoute les points d'ariane HTML au document
         * 3. On initialise la librairie qui affiche les tooltips sur chaque point d'ariane
         * 4. On contrôle le défilement pour changer de point d'ariane actif au bon moment
         */

        let default_options = {
            scroll_speed: 400,   // vitesse de scroll quand on clique sur un point d'Ariane
            point_style: 'fa-circle', // le style des points d'Ariane (peut être aussi une fonction ou un array d'éléments html)
            on_section_change: function(from_index, to_index) {}, // la fonction à appeler lorsqu'on change de point (en argument les indexes des sections impliquées)
            tooltips: '', // fonction ou liste des tooltips à afficher ou attribut HTML associé au @selection_selector pour récupérer le texte du tooltip
        }
        options = Object.assign(default_options, options);

        // == 0. == on prépare les fonctions qui nous aideront
        // récupère le style de points d'Ariane à utiliser pour chaque slide
        function getPointStyle(section_obj, n) {
            if (typeof options.point_style == 'string') {
                if (/^fa-/gi.test(options.point_style)) return `<i class="fas ${options.point_style}"></i>`;
                else return options.point_style;
            } else if (typeof options.point_style == 'function') {
                return options.point_style(section_obj, n);
            } else {
                return options.point_style[n % options.point_style.length];
            }
        }
        // récupère le texte des tooltips
        function getTooltip(section_obj, n) {
            if (!options.tooltips) return '';
            if (typeof options.tooltips == 'function') return options.tooltips(section_obj, n);
            if (Array.isArray(options.tooltips)) return options.tooltips[n % options.tooltips.length];
            if (typeof options.tooltips == 'string') return $(section_selector).eq(n).attr(options.tooltips);
            return '';
        }

        // == 1. == on récupère toutes les sections
        let my_sections = [];
        $(section_selector).each(function() {
            my_sections.push({
                id: $(this).attr('id'),
                obj: $(this),
                top: $(this).offset().top,
                height: $(this).height(),
            })
        })
        // TODO sort my_sections by top

        // == 2. == on ajoute les points d'Ariane au document
        let ariane_points = $(`<ul class="ariane_points"></ul>`);
        let i = 0;
        for (let section of my_sections) {
            let tooltip_text = getTooltip(section, i);
            let point = $(`<li data-toggle="tooltip" data-placement="left" title="${tooltip_text}"></li>`)
            point.append(getPointStyle(section, i))
            point.click(function() {
                $('html, body').animate( { scrollTop: section.top }, options.scroll_speed );
                options.on_section_change(i)
            });
            ariane_points.append(point)
            i++
        }
        $('body').append(ariane_points)

        // == 3. == on (re)-initialise popperjs pour les tooltips
        $('[data-toggle="tooltip"]').tooltip()

        // == 4. == on contrôle le défilement pour changer le status des points au bon moment
        curr_section = 0;
        jQuery(window).scroll(function() {
            let start = jQuery(window).scrollTop();
            let i = 0
            for (let section of my_sections) {
                let mem_section = curr_section
                if (section.top < start-10 && start < section.top + section.height*0.5 && curr_section != i) {
                    curr_section = i
                } else if (start < section.top + section.height + 10 && start > section.top + section.height*0.5 && curr_section != i+1) {
                    curr_section = i+1
                }
                if (mem_section != curr_section) {
                    $('ul.ariane_points > li').removeClass('active');
                    $(`ul.ariane_points > li:nth-child(${curr_section+1})`).addClass('active');
                    options.on_section_change(curr_section)
                    break;
                }
                i++
            }
        })
    }
    
    /* if ($('body').hasClass('page__programme2')) {
        console.log('init ariane 4 programme2')
        initArianePoints('main > .wp-block-group:visible', {
            tooltips: function(section, n) {
                let res = section.obj.find('h3');
                if (res.length) return res.eq(0).text();
                else return "";
            },
        });
    } else {
        initArianePoints('.section', {
            tooltips: 'data-title',
            on_section_change: function(ind) {
                if ($('body').hasClass('page__infos-pratiques') && (ind == 1 || ind == 4 || ind == 7) ) $('ul.ariane_points').addClass('black');
                else if ($('.section').eq(ind).hasClass('bg-blanc')) $('ul.ariane_points').addClass('black');
                else $('ul.ariane_points').removeClass('black');
            }
        });
    } */



    // ============================================
    // Gouttes 2.0
    // ============================================

    function place_gouttes(gouttes, root_el) {
        let w = root_el.width()
        let h = root_el.height()
        for (let g of gouttes) {
            let posX = 0; let posY = 0;
            if (g.angle !== undefined) {
                let rad = g.angle * Math.PI / 180;
                posX = Math.round(g.dist * Math.cos(rad) * w / 2) + w / 2;
                posY = - Math.round(g.dist * Math.sin(rad) * h / 2) + h / 2;
            } else {
                posX = g.x
                posY = g.y
            }
            let vBox = [
                `viewBox="${-g.strokeW/1.6 || 0} ${-g.strokeW/1.6 || 0} ${70.8 + (g.strokeW/1.1 || 0)} ${120.65 + (g.strokeW/1.1 || 0)}"`,
                `viewBox="${-g.strokeW/1.6 || 0} ${-g.strokeW/1.6 || 0} ${140.76 + (g.strokeW/1.1 || 0)} ${191.16 + (g.strokeW/1.1 || 0)}"`,
            ];
            let paths = [
                `<path class="cls-1" fill="${g.color}" stroke="${g.stroke || ''}" stroke-width="${g.strokeW || 0}"  d="M13.26,120.65c11.39-28.93,6.52-52.52-7.55-75A34.16,34.16,0,0,1,2,36.45c-2.83-10.35-3.8-20.62,5.41-29,9.39-8.53,21.05-8.6,32.53-5.88C61.19,6.63,74.2,27.18,70,48.9,64.05,79.93,40.49,99,13.26,120.65Z"/>`,
                `<path class="cls-1" fill="${g.color}" stroke="${g.stroke || ''}" stroke-width="${g.strokeW || 0}" d="M2.15,191.16C-4,133.76,1.57,79.4,39.14,33.24,54.77,14,74.75.21,101,0c33.31-.26,49.5,27.42,33.74,56.94-8.27,15.49-21,26.82-35.6,35.42C58,116.56,22.14,145.63,2.15,191.16Z"/>`,
            ];
            g.size = g.size ? `calc(${g.size} * min(10vw,10vh))` : g.sizeAbs
            let svg_code = `<svg class="svg-goutte" xmlns="http://www.w3.org/2000/svg" ${vBox[g.path]} style="transform-origin:center;transform:rotate(${g.rot}deg) scaleX(${g.flip});height:${g.size};position:absolute;top:${posY}px;left:${posX}px;z-index:1">
                ${paths[g.path]}
            </svg>`;
            root_el.append(svg_code)
        }
    }

    function compute_gouttes() {
        $('.wp-block-cover.gouttes').each(function() {
            $(this).css({position: 'relative'})
            $(this).find('.svg-goutte').remove()
            let gouttes = [
                // {path:0, color: '', size: 0.2, rot: 0, flip: 1, dist: 0, angle: 0},
                {path:1, color: 'var(--green)', size: 1.5, rot: 0, flip: 1, dist: 0.69, angle: 50},
                {path:0, color: 'transparent', stroke: "var(--blue-light)", strokeW: 5, size: 1.3, rot: -30, flip: 1, dist: 0.77, angle: 62},
                {path:0, color: 'var(--red)', size: 1.6, rot: -73, flip: 1, dist: 0.8, angle: 125},
                {path:0, color: 'var(--blue-light)', size: 1.3, rot: -26, flip: 1, dist: 0.72, angle: 175},
                {path:0, color: 'var(--green)', size: 2.3, rot: -73, flip: -1, dist: 0.81, angle: 193},
                {path:0, color: 'transparent', stroke: "var(--yellow)", strokeW: 3, size: 2.3, rot: -115, flip: -1, dist: 0.93, angle: -122},
                {path:0, color: 'var(--blue-light)', size: 1.3, rot: 130, flip: 1, dist: 0.9, angle: -109},
                {path:0, color: 'transparent', stroke: "var(--red)", strokeW: 3.5, size: 1.7, rot: 110, flip: 1, dist: 0.83, angle: -62},
                {path:1, color: 'var(--yellow)', size: 1.6, rot: 35, flip: 1, dist: 0.85, angle: -19},
                {path:0, color: 'var(--red)', size: 1.75, rot: 0, flip: 1, dist: 0.77, angle: 0},
            ]
            place_gouttes(gouttes, $(this))
    
        })

        $('.wp-block-media-text.gouttes-left').each(function() {
            $(this).css({position: 'relative'})
            $(this).find('.svg-goutte').remove()
            let w = $(this).width()
            let h = $(this).height()
            console.log('dim', w, h)
            let gouttes = [
                {path:1, color: 'var(--yellow)', sizeAbs: window.outerWidth * 0.065, rot: 0, flip: -1, x: 30, y: 50},
                {path:0, color: 'transparent', stroke: "var(--green)", strokeW: 3, sizeAbs: window.outerWidth * 0.11, rot: 25, flip: -1, x: 90, y: -50},
            ]
            place_gouttes(gouttes, $(this))
        })

        $('h2.gouttes-right, h3.gouttes-right, h4.gouttes-right').each(function() {
            $(this).css({position: 'relative'})
            $(this).find('.svg-goutte').remove()
            let w = $(this).width()
            let h = $(this).height()
            console.log('dim', w, h)
            let gouttes = [
                {path:1, color: 'transparent', stroke: "var(--blue-light)", strokeW: 7, sizeAbs: window.outerWidth * 0.07, rot: 38, flip: 1, x: w*0.85, y: -10},
                {path:0, color: 'var(--yellow)', sizeAbs: window.outerWidth * 0.1, rot: 0, flip: 1, x: w*0.75, y: -130},
            ]
            place_gouttes(gouttes, $(this))
        })

        $('.wp-block-cover.gouttes-diagonale, .wp-block-cover.gouttes-diagonal').each(function() {
            $(this).css({position: 'relative'})
            $(this).find('.svg-goutte').remove()
            let w = $(this).width()
            let h = $(this).height()
            console.log('dim diag', w, h)
            let gouttes = [
                {path:0, color: 'transparent', stroke: "var(--red)", strokeW: 2, sizeAbs: window.outerWidth * 0.12, rot: 190, flip: 1, x: window.outerWidth * 0.07, y: h-window.outerWidth * 0.07},
                {path:1, color: 'var(--yellow)', sizeAbs: window.outerWidth * 0.07, rot: 220, flip: 1, x: window.outerWidth * 0.06, y: h-window.outerWidth * 0.12},
                {path:1, color: 'var(--green)', sizeAbs: window.outerWidth * 0.083, rot: 20, flip: -1, x: window.outerWidth * 0.82, y: -30},
                {path:1, color: 'var(--blue-light)', sizeAbs: window.outerWidth * 0.083, rot: 0, flip: 1, x: window.outerWidth * 0.87, y: 40},
            ]
            place_gouttes(gouttes, $(this))
        })
    }

    compute_gouttes()
    $(window).resize(compute_gouttes)
    

});