jQuery(document).ready(function() {
    console.log('OKOKOKOKOKOKOKOK GLAERIE')
    jQuery('.wp-block-cover, img').each(function() {
        let url = jQuery(this).css('background-image').replace(/^\s*url\([\"\']?/, '').replace(/[\'\"\)\s]+$/, '');
        if (!url || !/^(\/|https?\:)/.test(url)) url = jQuery(this).attr('src');
        console.log('new fancy', jQuery(this), url, typeof url)
        jQuery(this).fancybox({
            src: url,
        });
    })
})