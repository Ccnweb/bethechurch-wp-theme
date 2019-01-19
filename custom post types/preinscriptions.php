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


function ccnbtc_custom_post_type_preinscriptions() {

    $prefix = "ccnbtc";
    $cp_name = 'preinscription';

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
        array( // Prénom
            'id' => $prefix.'_key_firstname', // le nom de la meta key
            'description'  => "Person first name for custom post",
            'html_label' => 'Prénom',
            'type' => "text"
        ),
        array( // Nom
            'id' => $prefix.'_key_name', // le nom de la meta key
            'description'  => "Person name for custom post",
            'html_label' => 'Nom',
            'type' => "text"
        ),
        array( // EMAIL
            'id' => $prefix.'_key_email', // le nom de la meta key
            'description'  => "Email address for custom post",
            'unique' => true,           // tells if this field must have unique values
            'show_as_column' => "Email", // shows this field as a column in the "list" view in admin panel
            'html_label' => 'Email',
            'type' => "email"
        ),
        array( // Téléphone
            'id' => $prefix.'_key_telephone', // le nom de la meta key
            'description'  => "Telephone for custom post",
            'html_label' => 'Téléphone',
            'type' => "tel"
        ),
        array( // Paroisse
            'id' => $prefix.'_key_paroisse', // le nom de la meta key
            'description'  => "Paroisse for custom post",
            'html_label' => 'Paroisse',
            'type' => "text"
        )
    );

    // == 3. == on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    $metabox_options = array(
        'title' => 'Données de pré-inscription'
    );
    create_custom_post_fields($cp_name, $cp_slug, $metabox_options, $prefix, $fields);

    // == 4. == on crée le backend REST pour POSTer des nouvelles inscriptions ($action_name = 'ccnbtc_preinscrire')
    $backend_options = array(
        'send_email' => array(
            array(
                'addresses' => array('web@chemin-neuf.org', 'contact@bethechurch.fr'),
                'subject' => 'Nouvelle Préinscription',
                'model' => 'simple_contact.html',
                'model_args' => array(
                    'title' => 'Que le Seigneur te donne sa paix !',
                    'subtitle' => 'Pré-inscription au festival paroisses "Be The Church" ',
                    'body' => 'Bonjour {{'.$prefix.'_key_firstname}},<br>Votre pré-inscription est bien validée ! Lorsque vous souhaiterez vous inscrire définitivement, n’hésitez pas à revenir sur le site pour nous donner toutes les informations nécessaires.<br><br>Dans la joie de vous accueillir cet été !<br><br>L’équipe du Festival des paroisses'
                ),
            ),
            array(
                'addresses' => array($prefix.'_key_email'),
                'subject' => 'Votre pré-inscription est confirmée !',
                'model' => 'simple_contact.html',
                'model_args' => array(
                    'title' => 'Festival des Paroisses',
                    'subtitle' => 'Pré-inscription au Festival des Paroisses Be The Church',
                    'body' => 'Bonjour,<br>Votre pré-inscription est bien validée. Nous vous enverrons un mail dès que les inscriptions seront
                    ouvertes en ligne pour que vous puissiez finaliser votre inscription.<br><br>Dans la joie de vous accueillir cet été !<br><br>L’équipe du Festival des paroisses'
                ),
            )
        ),
    );
    create_POST_backend($cp_name, $prefix, 'preinscrire', $accepted_users = 'all', $fields, $backend_options); // the final action_name of the backend will be $prefix.'inscrire'
    $html_form_options = array(
        'title' => '',
        'submit_btn_text' => 'Je me pré-inscris !',
        'required' => array('@ALL'),
        'computed_fields' => array(
            'post_title' => "() => getVal('{$prefix}_key_firstname_field') + ' ' + getVal('{$prefix}_key_name_field')",
        ),
    );
    // ... et le formulaire HTML que l'on enregistre comme un shortcode
    create_HTML_form_shortcode($cp_name, $prefix.'_preinscrire', $html_form_options, $fields); // shortcode will be $action_name.'-show-form' = "ccnbtc_preinscrire-show-form"
}

add_action( 'init', 'ccnbtc_custom_post_type_preinscriptions', 0 );

?>