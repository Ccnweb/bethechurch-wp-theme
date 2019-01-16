// ========================================
//       INITIALIZE SLIDE SCROLL
// ========================================
/* jQuery(document).ready(function(){
    jQuery(".main").onepage_scroll({
        sectionContainer: "section",     // sectionContainer accepts any kind of selector in case you don't want to use section
        easing: "ease",                  // Easing options accepts the CSS3 easing animation such "ease", "linear", "ease-in",
                                            // "ease-out", "ease-in-out", or even cubic bezier value such as "cubic-bezier(0.175, 0.885, 0.420, 1.310)"
        animationTime: 600,             // AnimationTime let you define how long each section takes to animate
        pagination: true,                // You can either show or hide the pagination. Toggle true for show, false for hide.
        updateURL: false,                // Toggle this true if you want the URL to be updated automatically when the user scroll to each page.
        beforeMove: function(index) {},             // This option accepts a callback function. The function will be called before the page moves.
        afterMove: function(index) {},   // This option accepts a callback function. The function will be called after the page moves.
        loop: false,                     // You can have the page loop back to the top/bottom when the user navigates at up/down on the first/last page.
        keyboard: true,                  // You can activate the keyboard controls
        responsiveFallback: false,        // You can fallback to normal page scroll by defining the width of the browser in which
                                            // you want the responsive fallback to be triggered. For example, set this to 600 and whenever
                                            // the browser's width is less than 600, the fallback will kick in.
        direction: "vertical"            // You can now define the direction of the One Page Scroll animation. Options available are "vertical" and "horizontal". The default value is "vertical".  
    });
}); */

jQuery(document).ready(function($) {

    // ========================================
    // INITIALISE FLECHE POUR CHANGER DE SLIDE
    // ========================================

    let height_section = $('.section').height();
    let height_header = $('nav.navbar').height();
    $('.fleche_slide_suivant > i').click(function() {
        start = $(window).scrollTop();
        //if (start == 0) start = -height_header;
        //$('html, body, .page').animate( { scrollTop: start + height_section + height_header }, 400 );
        $('html, body, .page').animate( { scrollTop: start + $(window).height() }, 400 );
    })

    /* $('#fullpage').fullpage({
		//options here
		autoScrolling:true,
		scrollHorizontally: true
	}); */

    // ========================================
    // INITIALISE CONTRÔLE DU DEFILEMENT
    // ========================================

    /* jQuery(window).scroll(function() {
      let height = jQuery(window).scrollTop();
      
    }); */

    // ========================================
    // Correction HTML à la volée
    // ========================================

    // ici on customize un peu le design les horaires pour les journées type
    $('h3,li').each(function() {
        let texte = $(this).text();
        if (/^[0-9]{2,3}/gi.test(texte)) {
            console.log(texte);
            let regex_res = /^([0-9]{3,4})\s+(.*)$/gi.exec(texte);
            if (regex_res && regex_res.length > 2) {
                console.log(regex_res);
                let hours = regex_res[1].substr(0, regex_res[1].length-2);
                let minutes = regex_res[1].substr(regex_res[1].length-2);
                let horaire = `<span class="has-text-color has-rouge-color">${hours}<sup>${minutes}</sup></span>`;
                $(this).html(horaire + ' ' + regex_res[2]);
            }
        }
    })

});