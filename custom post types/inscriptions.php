<?php

// we need to have the CCN Library plugin activated
if (!defined('CCN_LIBRARY_PLUGIN_DIR')) {
    echo ('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
    die('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
}

// we load here some high-level functions to create custom post types
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-custom-post-type.php');
// on charge la librairie pour créer des REST POST backend
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-rest-backend.php');
// on charge la libraire pour créer des formulaires HTML
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-html-forms.php');


function ccnbtc_custom_post_type_inscriptions() {

    $prefix = "ccnbtc";
    $cp_name = 'inscription';

    // == 1. == on crée le custom post type 'inscription'
	$args = create_custom_post_info(
        $cp_name, 
        $genre = "f", 
        $post_icon = 'dashicons-tickets-alt', 
        $supports = array( 'title', 'custom-fields') // 'custom-fields' is required if we want to retrieve the meta_keys from the rest api
    );
    register_post_type( $cp_name, $args );
    $cp_slug = $args['rewrite']['slug'];
    
    // == 2. == on définit les fields
    $fields = array(
        array( // Je suis
            'id' => $prefix.'_key_jesuis',
            'description'  => "D'où vient la personne (paroisse, communautaire, ...)",
            'html_label' => 'Je suis',
            'type' => "radio",
            'options' => array(
                'paroisse' => "Membre d'une paroisse (préciser)",
                'frat_paroissiale' => "Membre des Fraternités Paroissiales Missionnaires du Chemin-Neuf",
                'communautaire' => "Membre de la Communauté ou de la Communion du Chemin Neuf",
                'autre' => 'Autre à préciser :',
            ),
            'options_preciser' => array('autre', 'paroisse'),
        ),

    );

    // == 3. == on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    $metabox_options = array(
        'title' => 'Données inscription'
    );
    create_custom_post_fields($cp_name, $cp_slug, $metabox_options, $prefix, $fields);

    // == 4. == on crée le backend REST pour POSTer des nouvelles inscriptions ($action_name = 'ccnbtc_inscrire')
    create_POST_backend($cp_name, $prefix, 'inscrire', $accepted_users = 'all', $fields); // the final action_name of the backend will be $prefix.'inscrire'
    // ... et le formulaire HTML que l'on enregistre comme un shortcode
    $html_form_options = array(
        'title' => '',
        'submit_btn_text' => 'Je m\'inscris !',
        'required' => array('@ALL'),
        'computed_fields' => array(
            'post_title' => "() => getVal('{$prefix}_key_name_field')",
        ),
    );
    create_HTML_form_shortcode($cp_name, $prefix.'_inscrire', $html_form_options, $fields); // shortcode will be $action_name.'-show-form' = "ccnbtc_inscrire-show-form"
}

add_action( 'init', 'ccnbtc_custom_post_type_inscriptions', 0 );

?>