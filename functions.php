<?php

require_once(get_template_directory() . '/lib/php/lib.php');
require_once(CCN_LIBRARY_PLUGIN_DIR . '/lib.php'); use \ccn\lib as lib;
require_once(CCN_LIBRARY_PLUGIN_DIR . '/log.php'); use \ccn\lib\log as log;

//define('BTC_CONFIG', yaml_parse(file_get_contents(__DIR__."/BTC_CONFIG.yml"))); 

/**
 * =============================================
 * SUMMARY
 * =============================================
 * 
 * theme prefix = 'ccnbtc'
 * 
 * 1. THEME SETUP
 *    - custom-logo
 *    - menus setup
 *    - editor color palette
 * 
 * 2. LOAD STYLES AND SCRIPTS
 *    - All the frontent js and css files (including external libraries)
 *    - code to add the page slug to the body class
 * 
 * 3. TRANSLATION
 *    - a first approach to translate the inscription form (not very good TODO improve that)
 * 
 * 4. ADD CUSTOM FIELDS TO POSTS
 *    - for articles : field "order" that defines the display order of articles in the page
 * 
 * 5. CUSTOM TEXT PARSER
 *    - a parser that parses any text of the editor to replace text between "{}" with a site option
 *      this is used for ex for the start and end dates of the festival
 * 
 * 6. LOAD CUSTOM POST TYPES
 *    - defined in the "custom post types" folder
 * 
 * 7. LOAD SHORTCODES
 *    - defined in the "shortcodes" folder
 * 
 * 8. LOAD ADMIN PAGES
 *    - defined in the "admin-pages" folder
 * 
 * 9. LOAD CONTACT FORM SHORTCODE
 *    - the contact form shortcode (a priori sent to web@chemin-neuf.org and contact@bethechurch.fr)
 * 
 * 
 */

/* ========================================================= */
/*                       THEME SETUP                         */
/* ========================================================= */

// import de la classe qui permet de gérer les navbar bootstrap de la bonne façon avec wordpress
require_once get_template_directory() . '/class-wp-bootstrap-navwalker.php';

// Load translations
$b = load_theme_textdomain( 'ccnbtc', get_template_directory().'/languages' );
/* if (!$b) {
    lib\php_console_log('cannot load translations for BTC theme', 'err');
    log\error('cannot load translations for BTC theme');
} */

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
        'default-color' => '#ffffff',
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
        'menu-footer' => esc_html__( 'Pied de page', 'ccnbtc' ),
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
            'color'	=> '#39CD9E',
        ),
        array(
            'name'  => __( 'Rouge', 'ccnbtc' ),
            'slug'  => 'rouge',
            'color'	=> '#FF6D53',
        ),
        array(
            'name'  => __( 'Jaune', 'ccnbtc'),
            'slug'  => 'jaune',
            'color' => '#FCC300', 
        ),
        array(
            'name'  => __( 'Orange', 'ccnbtc'),
            'slug'  => 'orange',
            'color' => '#ea955d', 
        ),
        array(
            'name'  => __( 'Bleu Klein', 'ccnbtc'),
            'slug'  => 'bleu-klein',
            'color' => '#242148', 
        ),
        array(
            'name'  => __( 'Bleu clair pelorous', 'ccnbtc'),
            'slug'  => 'bleu-clair',
            'color' => '#3864FF', 
        ),
    ) );

}
add_action( 'after_setup_theme', 'ccnbtc_setup' );


/* ========================================================= */
/*                 DEFINE WIDGET AREAS                       */
/* ========================================================= */

function ccnbtc_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Important info (COVID)', 'ccnbtc' ),
        'id'            => 'sidebar-important-info',
        'description'   => __( 'Place to show important info (created originally for COVID info)', 'ccnbtc' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="important-info-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'ccnbtc_widgets_init' );




/* ========================================================= */
/*                 LOAD STYLES AND SCRIPTS                   */
/* ========================================================= */

function ccnbtc_scripts() {
    $ccn_enqueue = function($name, $path, $dependencies, $displays = 'all') {
        if (!file_exists($path)) $path = get_template_directory_uri() . '/' . $path;
        $vers = date("ymd-Gis", filemtime( $path ));
        if (substr($vers, strlen($vers)-4) == '0000') $vers = '010';
        if (preg_match("/.+\.js$/", $path)) wp_enqueue_script($name, $path, $dependencies, $vers, $displays);
        if (preg_match("/.+\.css$/", $path)) wp_enqueue_style($name, $path, $dependencies, $vers, $displays);
    };
    
    // ## 1 ## For all pages
    wp_enqueue_script('jquery');
    
    // Bootstrap
    wp_enqueue_style('ccnbtc-bootstrap', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css', array(), '4.1.3', 'all');
    wp_enqueue_script('ccnbtc-popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array('jquery'));
    wp_enqueue_script('ccnbtc-bootstrap-script', 'https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array('jquery', 'ccnbtc-popper'));

    // FontAwesome pour les icônes
    wp_enqueue_style( 'ccnbtc-fa', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css');

    // we load style.css here because it should be loaded after bootstrap css
    //$ccn_enqueue( 'ccnbtc-parent-style', '/style.css', [], filemtime(get_template_directory() . '/style.css'), 'all');
    enqueue_styles_regex(get_template_directory() . '/styles');
    wp_enqueue_style( 'ccnbtc-parent-style', get_template_directory_uri() . '/style.css', [], filemtime(get_template_directory() . '/style.css'), 'all');
    // main script of the theme
    wp_enqueue_script( 'ccnbtc-main-script', get_template_directory_uri() . '/js/main.js', array('jquery'), filemtime(get_template_directory() . '/js/main.js'));


    // ## 2 ## For Specific Pages
    // Home Page Festival
    wp_register_style( 'ccnbtc-festival-style', get_template_directory_uri() . '/pages/festival/page.css', array(), filemtime(get_template_directory() . '/pages/festival/page.css'), 'all');
    wp_register_style( 'ccnbtc-intervenants-style', get_template_directory_uri() . '/pages/intervenants/intervenants.css', array(), filemtime(get_template_directory() . '/pages/intervenants/intervenants.css'), 'all');
    wp_register_script( 'typedjs', 'https://cdn.jsdelivr.net/npm/typed.js@2.0.11', [], true);
    wp_register_script( 'ccnbtc-typedjs', get_template_directory_uri() . '/js/ccn-typed.js', ['jquery', 'typedjs'], filemtime(get_template_directory() . '/js/ccn-typed.js'), true);

    // ## 3 ## For connected users
    // Translation tools
    wp_register_style( 'ccnbtc-translation-style', get_template_directory_uri() . '/components/translation_ui/translation.css', array(), '001', 'all');
    wp_register_script('ccnbtc-translation-script', get_template_directory_uri() . '/components/translation_ui/translation.js', array());
    wp_localize_script('ccnbtc-translation-script', 'translationAjaxData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest') //secret value created every time you log in and can be used for authentication to alter content 
    ));
    
    // ## 4 ## For the program COVID page
    // wp_register_style('ccnbtc-programme-covid', get_template_directory_uri() . '/pages/programme-covid/programme-covid.css', array(), filemtime(get_template_directory() . '/pages/programme-covid/programme-covid.css'), 'all');
    // wp_register_script('ccnbtc-programme-covid-script', get_template_directory_uri() . '/pages/programme-covid/programme-covid.js', array(), filemtime(get_template_directory() . '/pages/programme-covid/programme-covid.js'));

    // Magic templates
    $root_dir = get_template_directory();
    foreach (scandir($root_dir) as $filename) {
        // $fullpath = $root_dir . '/' . $filename;
        if (preg_match("/\-template\.php$/", $filename) == 1) {
            preg_match("/^(.+)\-template\.php$/", $filename, $matches);
            $page_name = $matches[1];
            $fcss = get_template_directory() . '/pages/'.$page_name.'/'.$page_name.'.css';
            $fjs = get_template_directory() . '/pages/'.$page_name.'/'.$page_name.'.js';
            if (file_exists($fcss)) wp_register_style('ccnbtc-'.$page_name, get_template_directory_uri() . '/pages/'.$page_name.'/'.$page_name.'.css', array(), filemtime($fcss), 'all');
            if (file_exists($fjs)) wp_register_script('ccnbtc-'.$page_name.'-script', get_template_directory_uri() . '/pages/'.$page_name.'/'.$page_name.'.js', array(), filemtime($fjs));
        }
    }

}
add_action( 'wp_enqueue_scripts', 'ccnbtc_scripts' );




/**
 * Adds all blocks style variants
 */
add_action( 'enqueue_block_editor_assets', function() {

    // Columns, image variants
    wp_enqueue_script(
        'block-variants',
        get_template_directory_uri().'/js/admin/block-variants.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-rich-text'],
        filemtime(__DIR__.'/js/admin/block-variants.js')
    );

});







// Add Page Slug to Body Class
function add_slug_body_class( $classes ) {
    global $post;
    if ( isset( $post ) ) {
        $classes[] = $post->post_type . '__' . $post->post_name;
    }
    return $classes;
}
add_filter( 'body_class', 'add_slug_body_class' );


/* ========================================================= */
/*                   TRANSLATION                             */
/* ========================================================= */

// sources : 
// https://premium.wpmudev.org/blog/how-to-localize-a-wordpress-theme-and-make-it-translation-ready/
// https://github.com/fxbenard/Blank-WordPress-Pot

//load_theme_textdomain( 'ccnbtc', get_template_directory().'/languages' );

// source : https://polylang.pro/doc/function-reference/#pll_register_string

add_action('init', function() {

    // define strings to be translated
    $strings = [
      'forms' => ['Prénom', 'Nom', 'Date de naissance', 'Code postal'],
    ];
    
    // actually register strings in polylang plugin
    foreach ($strings as $cat => $vals) {
      foreach ($vals as $val) {
          // pll_register_string( $sorting_name, $string_to_be_translated, $group, $multiline );
          if (is_array($val)) pll_register_string($val[0], $val[1], $cat, false);  
          else pll_register_string($val, $val, $cat, false);  
        }
      }

});

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
        array('title' => "Ordre d'affichage de l'article", 'fields' => 'ALL')
    );
    create_custom_post_fields('post', 'post', $metabox_options, $prefix, array($field_order));
}

ccnbtc_add_custom_fields_to_posts();

/* ========================================================= */
/*                  CUSTOM TEXT PARSER                       */
/* ========================================================= */

// source of WP_Block_Parser class : https://github.com/WordPress/gutenberg/blob/master/packages/block-serialization-default-parser/parser.php
class CcnParser extends WP_Block_Parser {
    public function parse( $post_content ) {
        $options = get_option('btc-config');
        if (!is_array($options) || empty($options)) return parent::parse($post_content);

        foreach ($options as $k => $v) {
            preg_match_all("/\{\s*".$k."\s*(\|[^\}]+)?\}/i", $post_content, $matches);
            for ($i = 0; $i < count($matches[0]); $i++) {
                // special case for dates
                if (!empty($matches[1][$i]) && preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $v)) {
                    $date_format = trim($matches[1][$i], " \t\n\r\0\x0B|");
                    $d = strtotime($v);
                    $v = ccn_date_format($date_format, $d, pll_current_language());
                }
                $post_content = str_replace($matches[0][$i], $v, $post_content);
            }
        }
        return parent::parse($post_content);
    }
}
function my_plugin_select_empty_parser( $prev_parser_class ) {
    return 'CcnParser';
}
add_filter( 'block_parser_class', 'my_plugin_select_empty_parser', 10, 1 ); 


/* ========================================================= */
/*                Social Network logos in menu               */
/* ========================================================= */

add_filter( 'nav_menu_link_attributes', 'ccnbtc_open_external_nav_link_new_window' );
function ccnbtc_open_external_nav_link_new_window( $atts ) {
	
	$regex_prefix = "/^https?\:\/\/([^\.]+\.)?";
	$regex_suffix = "\./";
	$social_medias = ['facebook', 'instagram', 'twitter', 'youtube'];
	$icons = ['facebook' => 'fa-facebook-square', 'instagram' => 'fa-instagram', 'twitter' => 'fa-twitter', 'youtube' => 'fa-youtube'];

	if ( preg_match( $regex_prefix."(".implode('|', $social_medias).")".$regex_suffix, $atts['href'], $match ) ) {
		$atts['target'] = '_blank';
		$social = $match[2];
		$atts['class']  = "mr-1 fab ".$icons[$social];
	}
	return $atts;
}


/* ========================================================= */
/*                  LOAD CUSTOM POST TYPES                   */
/* ========================================================= */

require_once_all_regex(get_template_directory() . '/custom post types/', "");

/* ========================================================= */
/*                   LOAD SHORTCODES                         */
/* ========================================================= */

require_once_all_regex(get_template_directory() . '/shortcodes/', "");

/* ========================================================= */
/*                   LOAD ADMIN PAGES                        */
/* ========================================================= */

require_once_all_regex(get_template_directory() . '/admin-pages/', "");


/* ========================================================= */
/*               LOAD CONTACT FORM SHORTCODE                 */
/* ========================================================= */

// load contact form shortcode
ccnlib_register_contact_form(array(
        'title' => '',
        'text_btn_submit' => __('Envoyer', 'ccnbtc'),
        'fields' => array('nom', 'prenom', 'email', array(
            'id' => 'ccnbtc_paroisse',
            'type' => 'text',
            'html_label' => __('Ma paroisse', 'ccnbtc'),
        ), 'message'),
        'required' => array('@ALL'),
        'send_email' => array(
            array(
                'addresses' => array('web@chemin-neuf.org', 'contact@bethechurch.fr'), // adresses email à qui envoyer le mail
                'subject' => '['.__('Contact', 'ccnbtc').' BTC] '.sprintf(__('Nouvelle demande de contact de %s', 'ccnbtc'), '{{ccnlib_key_firstname}} {{ccnlib_key_name}}'), // sujet du mail
                'model' => 'simple_contact.html', // le chemin vers le modèle/template HTML à utiliser
                'model_args' => array( // les arguments qui permettent de populer le model/template HTML du message
                    'title' => __('Que le Seigneur te donne sa paix', 'ccnbtc').' !',
                    'subtitle' => sprintf(__('Nouvelle demande de contact de %s', 'ccnbtc'), '{{ccnlib_key_firstname}} {{ccnlib_key_name}}'),
                    'body' => 'Coucou ! Une nouvelle demande de contact vient d\'arriver du site Be The Church, 
                                voici les détails de la demande :<br>
                                <b>Prénom: </b>{{ccnlib_key_firstname}}<br>
                                <b>Nom: </b>{{ccnlib_key_name}}<br>
                                <b>Email: </b>{{ccnlib_key_email}}<br>
                                <b>Paroisse: </b>{{ccnbtc_paroisse}}<br>
                                <b>Corps du message:</b><br><br>
                                {{message_HTML}}<br>',
                ),
            ),
            array(
                'addresses' => array('ccnlib_key_email'),
                'subject' => 'Votre demande de contact pour le festival Be The Church',
                'model' => 'simple_contact.html',
                'model_args' => array(
                    'title' => 'Accusé de réception',
                    'subtitle' => 'Votre demande de contact pour le festival Be The Church a bien été prise en compte',
                    'body' => 'Bonjour {{ccnlib_key_firstname}},<br>
                                Votre demande de contact à propos du festival Be The Church a bien été prise en compte.<br>
                                Nous vous répondrons dans les meilleurs délais. Pour rappel, vous trouverez une copie de votre message ci-dessous<br>
                                <br>
                                L’équipe du Festival des paroisses<br>
                                <br><br>
                                <b>Copie de votre message :</b><br>{{message_HTML}}',
                ),
            ),
        ),
    )
);

?>