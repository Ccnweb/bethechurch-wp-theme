<?php

require_once(get_template_directory() . '/lib/php/lib.php');

/* ========================================================= */
/*                       THEME SETUP                         */
/* ========================================================= */

// import de la classe qui permet de gérer les navbar bootstrap de la bonne façon avec wordpress
require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';

function ccnbtc_setup() {
    add_theme_support( 'title-tag' );       // laisse wordpress définir le titre du site
    add_theme_support( 'post-thumbnails' ); // image de mise en avant pour les articles
    add_theme_support( 'html5', array( // éléments html5
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Set up the WordPress core custom background feature.
    add_theme_support( 'custom-background', apply_filters( 'wtp2019_custom_background_args', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ) ) );

    // Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );
    
    // Le Logo
    add_theme_support( 'custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ) );

    // Les menus (wp_nav_menu())
    // source: https://www.wpbeginner.com/wp-themes/how-to-add-custom-navigation-menus-in-wordpress-3-0-themes/
    register_nav_menus( array(
        'header' => 'Menu principal.',
    ) );

    // Les palettes de couleur dans l'éditeur d'articles

    // Adds support for editor color palette.
    add_theme_support( 'editor-color-palette', array(
        array(
            'name'  => __( 'Blanc', 'ccnbtc' ),
            'slug'  => 'blanc',
            'color' => '#fff',
        ),
        array(
            'name'  => __( 'Noir', 'ccnbtc' ),
            'slug'  => 'noir',
            'color' => '#000',
        ),
        array(
            'name'  => __( 'Vert', 'ccnbtc' ),
            'slug'  => 'vert',
            'color'	=> '#31D2AE',
        ),
        array(
            'name'  => __( 'Rouge', 'ccnbtc' ),
            'slug'  => 'rouge',
            'color'	=> '#EA5E6B',
        ),
        array(
            'name'  => __( 'Jaune', 'ccnbtc'),
            'slug'  => 'jaune',
            'color' => '#FCC300', 
        ),
        array(
            'name'  => __( 'Bleu Klein', 'ccnbtc'),
            'slug'  => 'bleu-klein',
            'color' => '#242148', 
        ),
        array(
            'name'  => __( 'Bleu clair pelorous', 'ccnbtc'),
            'slug'  => 'bleu-clair',
            'color' => '#34ACB6', 
        ),
    ) );

}
add_action( 'after_setup_theme', 'ccnbtc_setup' );


/* ========================================================= */
/*                 LOAD STYLES AND SCRIPTS                   */
/* ========================================================= */

function ccnbtc_scripts() {
    // ## 1 ## For all pages
    //wp_enqueue_style( 'ccnbtc-style', get_stylesheet_uri() );
    wp_enqueue_script('jquery');

    // Bootstrap
    wp_enqueue_style('ccnbtc-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css', array(), '4.1.3', 'all');
    wp_enqueue_script('ccnbtc-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array('jquery'));
    wp_enqueue_script('ccnbtc-bootstrap-script', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array('jquery', 'ccnbtc-popper'));

    // FontAwesome pour les icônes
    wp_enqueue_style( 'ccnbtc-fa', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css');

    // OnePageScroll pour l'effet "slide" lorsqu'on scroll
    //wp_enqueue_style('ccnbtc-onepage-style', 'https://cdnjs.cloudflare.com/ajax/libs/onepage-scroll/1.3.1/onepage-scroll.min.css', array(), '1.3.1', 'all');
    //wp_enqueue_script( 'ccnbtc-onepage-script', 'https://cdnjs.cloudflare.com/ajax/libs/onepage-scroll/1.3.1/jquery.onepage-scroll.min.js', array('jquery'));
    
    // FullPage pour l'effet "slide" lorsqu'on scroll
    //wp_enqueue_style('ccnbtc-fullapge-style', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.0.4/fullpage.min.css', array(), '3.0.4', 'all');
    //wp_enqueue_script( 'ccnbtc-fullpage-script', 'https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/3.0.4/fullpage.min.js', array(), '3.0.4');    

    // on load style.css ici pour qu'il soit chargé après le CSS de bootstrap
    wp_enqueue_style( 'ccnbtc-parent-style', get_template_directory_uri() . '/style.css' );
    // main script of the theme
    wp_enqueue_script( 'ccnbtc-main-script', get_template_directory_uri() . '/js/main.js', array('jquery'));


    // ## 2 ## For Specific Pages
    // Home Page Festival
    wp_register_style( 'ccnbtc-festival-style', get_template_directory_uri() . '/pages/festival/page.css', array(), '20190110', 'all');

}
add_action( 'wp_enqueue_scripts', 'ccnbtc_scripts' );



/* ========================================================= */
/*               ADD CUSTOM FIELDS TO POSTS                  */
/* ========================================================= */

function ccnbtc_add_custom_fields_to_posts() {
    if (!defined('CCN_LIBRARY_PLUGIN_DIR')) {
        echo ('global var CCN_LIBRARY_PLUGIN_DIR is not defined. You should first install the plugin "CCN Library"');
        return;
    }

    // we load here some high-level functions to create custom post types
    require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-custom-post-type.php');

    $prefix = 'ccnbtc';

    // on ajoute un field "ordre" pour indiquer un ordre aux articles/posts
    $field_order = [
        'id' => 'ccnbtc_post_order',
        'type' => 'number',
        'description' => "Ordre de l'article",
        'html_label' => 'Ordre',
        'show_as_column' => "Ordre",
        'html_attributes' => ['min' => 0],
    ];
    // on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    $metabox_options = array(
        'title' => "Ordre d'affichage de l'article"
    );
    create_custom_post_fields('post', 'post', $metabox_options, $prefix, array($field_order));
}

ccnbtc_add_custom_fields_to_posts();


/* ========================================================= */
/*                  LOAD CUSTOM POST TYPES                   */
/* ========================================================= */

require_once_all_regex(get_template_directory() . '/custom post types/', "");

/* ========================================================= */
/*                   LOAD SHORTCODES                         */
/* ========================================================= */

require_once_all_regex(get_template_directory() . '/shortcodes/', "");

?>