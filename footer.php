<?php wp_footer(); ?>

<div class="footer-legal">

    <div class="left" style="background: #222;">
        <a target="_blank" href="https://www.chemin-neuf.fr">
            <img class="logo-ccn" src="https://dam.chemin-neuf.net/logo-ccn-fr-white-notext/" alt="">
        </a>

        <div class="info">
            © 2021 Communauté du Chemin Neuf<br>69005 Lyon France
        </div>
    </div>

    <?php wp_nav_menu( array(
        'theme_location'  => 'menu-footer',
        // 'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
        // 'container'       => 'div',
        'container_class' => 'menu-footer',
        // 'container_id'    => 'navbarResponsive',
        // 'menu_class'      => 'navbar-nav w-100 menu_principal',
        // 'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
        // 'walker'          => new WP_Bootstrap_Navwalker(),
    ) ); ?>

</div>

</body>

</html>