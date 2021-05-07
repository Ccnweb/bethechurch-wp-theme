<!--  #################################### -->
<!--    LE MENU PRINCIPAL DE NAVIGATION    -->
<!--  #################################### -->

<nav class="navbar navbar-expand-md navbar-light fixed-top menu-principal" role="navigation" <?php if(is_admin_bar_showing()) echo 'style="top:32px"'; ?>>
    <div class="container-fluid d-flex justify-content-end">
        
        <!-- Logo -->
        <?php the_custom_logo(); ?>

        <!-- Menu hamburger pour les petits écrans -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu principal. défini dans Wordpress -->
        <?php
            wp_nav_menu( array(
                'theme_location'  => 'header',
                'depth'           => 2, // 1 = no dropdowns, 2 = with dropdowns.
                'container'       => 'div',
                'container_class' => 'collapse navbar-collapse',
                'container_id'    => 'navbarResponsive',
                'menu_class'      => 'navbar-nav w-100 menu_principal',
                'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
                'walker'          => new WP_Bootstrap_Navwalker(),
            ) ); 
        ?>

    </div>
</nav>

