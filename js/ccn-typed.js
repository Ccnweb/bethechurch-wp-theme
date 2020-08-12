jQuery(document).ready(function() {
    // ========================================
    // INITIALISE TYPED.JS TYPEWRITER EFFECT
    // ========================================
    console.log('init typewriter', jQuery('.typed').length)

    jQuery('.typed').each(function() {
        let typed_id = jQuery(this).attr('id');
        let words_id = jQuery(this).attr('data-typed');
        let options = {
            stringsElement: '#' + words_id,
            loop: true,
            typeSpeed: 60
        }
        console.log('typed options', options)
        let typed = new Typed('#' + typed_id, options)
    })
})