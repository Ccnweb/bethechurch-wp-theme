jQuery(document).ready(function($) {

    // ========================================
    // HREF IMAGE BLOCKS
    // ========================================

    // here we allow users to click on wp-block-cover to be able to go to the a href
    jQuery('section.galerie .wp-block-cover a').each(function() {
        let url = jQuery(this).attr('href');
        console.log('galerie url : ', url, jQuery(this).closest('.wp-block-cover'))
        jQuery(this).closest('.wp-block-cover').click(function() {
            window.location.href = url;
            jQuery(this).closest('.wp-block-column').addClass('goto');
            jQuery(this).find('.wp-block-cover__inner-container').html('');
            jQuery(this).find('.wp-block-cover__inner-container').addClass('goto');
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
    // Corrections HTML à la volée
    // ========================================

    // ici on cache le bouton des inscriptions si les inscriptions sont fermées
    let btn_inscr = $(".reserver_place");
    let inscr_status = (btn_inscr.length) ? btn_inscr.find('a').attr('rel') : 'INSCRIPTIONS FERMÉES';
    if (inscr_status == "INSCRIPTIONS FERMÉES") {
        btn_inscr.hide();
    } else if (inscr_status == 'PRÉ-INSCRIPTION') {
        console.log("COCO ", btn_inscr.attr('rel'));
        $('#inscription_btn').css({display: "none"})
    } else if (inscr_status == 'INSCRIPTION') {
        console.log("COCO2 ", btn_inscr.attr('rel'));
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
                let horaire = `<span class="has-text-color has-rouge-color">${hours}<sup>${minutes}</sup></span>`;
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
    
    if ($('body').hasClass('page__programme2')) {
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
    }

});