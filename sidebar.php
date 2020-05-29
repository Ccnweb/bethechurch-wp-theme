<?php if (is_active_sidebar( 'sidebar-important-info' )) { ?>

<aside id="important-info-sidebar" class="widget-area" role="complementary" aria-label="<?php esc_attr_e( 'Main widget area', 'ccnbtc' ); ?>">
    <?php dynamic_sidebar( 'sidebar-important-info' ); ?>
</aside>

<?php } ?>

<style>
    #important-info-sidebar {
        background: hsla(9, 70%, 50%, 1);
        position: fixed;
        right: 0;
        color: white;
        top: 5rem;
        width: 10rem;
        max-width: 330px;
        height: 4rem;
        overflow: hidden;
        cursor: pointer;
        z-index: 33;
        border-radius: 200px 0 0 109px;
        box-shadow: hsla(0, 100%, 31%, 0.62) -3px 4px 16px 2px;
        transition: all 0.2s ease-out;
    }
    #important-info-sidebar:hover {
        width: 11rem;
        transition: all 0.1s cubic-bezier(0.17, 0.78, 0.85, 1.25);
    }

    #important-info-sidebar * {
        color: white;
    }
    #important-info-sidebar.show {
        height: 22rem;
        width: 100%;
        padding: 2rem;
        border-radius: 0px;
        right: 2rem;
        transition: width 0.2s ease-out 0.1s, right 0.2s ease-out, border-radius 0.1s ease-out, height 0.2s ease-in 0.2s, padding 0.1s ease-in 0.2s;
    }
    #important-info-sidebar.show h3.important-info-title {
        padding: 0;
    }
    #important-info-sidebar h3.important-info-title {
        color: white;
        padding: 1rem;
        white-space: nowrap;
    }
</style>
<script>
    jQuery('#important-info-sidebar').click(function() {
        jQuery(this).toggleClass('show')
    })
</script>