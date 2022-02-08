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

<!-- https://codesandbox.io/s/bezier-curve-clip-path-h8x8l?from-embed=&file=/index.html:358-505 -->
<svg width="0" height="0">
  <defs>
    <clipPath id="btc-wave" clipPathUnits="objectBoundingBox">
        <path
            d="M 0,0.2
            C .5 .6, .65 -.35, 1 0.2
            L 1,1
            L 0,1
            Z"
        />
    </clipPath>
  </defs>
</svg>

</body>

</html>