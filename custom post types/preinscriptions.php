<?php

// we need to have the CCN Library plugin activated
if (!defined('CCN_LIBRARY_PLUGIN_DIR')) {
    echo ('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
    die('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
}

require_once(CCN_LIBRARY_PLUGIN_DIR . '/log.php'); use \ccn\lib\log as log;

// we load here some high-level functions to create custom post types
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-custom-post-type.php');
// on charge la librairie pour créer des REST POST backend
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-rest-backend.php');
// on charge la libraire pour créer des formulaires HTML
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-html-forms.php');


function ccnbtc_custom_post_type_preinscriptions() {

    $prefix = "ccnbtc_preinscr";
    $cp_name = 'preinscription';

    $btc_options = array_merge(array(
        'date_festival_from' => '',
        'date_festival_to' => '',
        'etat_inscriptions' => '',
        'contact_nom' => '',
        'contact_email' => '',
        'contact_tel' => '',
    ), get_option('btc-config'));

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
            'html_label' => __('Prénom', 'ccnbtc'),
            'type' => "text"
        ),
        array( // Nom
            'id' => $prefix.'_key_name', // le nom de la meta key
            'description'  => "Person name for custom post",
            'html_label' => __('Nom', 'ccnbtc'),
            'type' => "text"
        ),
        array( // EMAIL
            'id' => $prefix.'_key_email', // le nom de la meta key
            'description'  => "Email address for custom post",
            'unique' => true,           // tells if this field must have unique values
            'show_as_column' => "Email", // shows this field as a column in the "list" view in admin panel
            'html_label' => __('Email', 'ccnbtc'),
            'type' => "email"
        ),
        array( // Téléphone
            'id' => $prefix.'_key_telephone', // le nom de la meta key
            'description'  => "Telephone for custom post",
            'html_label' => __('Téléphone', 'ccnbtc'),
            'type' => "tel"
        ),
        array( // Paroisse
            'id' => $prefix.'_key_paroisse', // le nom de la meta key
            'description'  => "Paroisse for custom post",
            'html_label' => __('Paroisse', 'ccnbtc'),
            'type' => "text"
        )
    );

    // == 3. == on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    $metabox_options = array(
        array('title' => 'Données de pré-inscription', 'fields' => 'ALL')
    );
    create_custom_post_fields($cp_name, $cp_slug, $metabox_options, $prefix, $fields);

    // == 4. == on crée le backend REST pour POSTer des nouvelles inscriptions ($action_name = 'ccnbtc_preinscrire')
    $backend_options = array(
        'post_status' => 'private', // 'private' because inscriptions should be private and therefore not available through the rest api without authentication !
        'computed_fields' => array(
            'post_title' => function($pv) use ($prefix) {
                //log\info('POST TITLE', $pv[$prefix.'_key_firstname'] . ' ' . $pv[$prefix.'_key_name']);
                return $pv[$prefix.'_key_firstname'] . ' ' . $pv[$prefix.'_key_name'];
            },
        ),
        'send_email' => array(
            array(
                'addresses' => array('web@chemin-neuf.org', 'contact@bethechurch.fr'), 
                'subject' => 'Pré-inscription - {{'.$prefix.'_key_firstname}} {{'.$prefix.'_key_name}}',
                'model' => 'simple_contact.html',
                'model_args' => array(
                    'title' => '',
                    'subtitle' => '',
                    'body' => '<table style="border-collapse:collapse;">
                            <tr><td style="padding: 4px 12px;border:1px solid #447;background-color:#dedede;">Prénom </td><td style="padding: 4px 12px;border:1px solid #447;">{{'.$prefix.'_key_firstname}}</td></tr>
                            <tr><td style="padding: 4px 12px;border:1px solid #447;background-color:#dedede;">Nom </td><td style="padding: 4px 12px;border:1px solid #447;">{{'.$prefix.'_key_name}}</td></tr>
                            <tr><td style="padding: 4px 12px;border:1px solid #447;background-color:#dedede;">Email </td><td style="padding: 4px 12px;border:1px solid #447;">{{'.$prefix.'_key_email}}</td></tr>
                            <tr><td style="padding: 4px 12px;border:1px solid #447;background-color:#dedede;">Téléphone </td><td style="padding: 4px 12px;border:1px solid #447;">{{'.$prefix.'_key_telephone}}</td></tr>
                            <tr><td style="padding: 4px 12px;border:1px solid #447;background-color:#dedede;">Paroisse </td><td style="padding: 4px 12px;border:1px solid #447;">{{'.$prefix.'_key_paroisse}}</td></tr>
                        </table>'
                ),
            ),
            array(
                'addresses' => array($prefix.'_key_email'),
                'subject' => 'Votre pré-inscription est confirmée !',
                'model' => 'simple_contact.html',
                'model_args' => array(
                    'title' => 'Festival des Paroisses',
                    'subtitle' => 'Pré-inscription au Festival des Paroisses Be The Church',
                    'body' => 'Bonjour,<br>
                            Votre pré-inscription est bien validée. N\'hésitez pas à nous contacter pour toute question.<br>
                            Pour finaliser votre inscription vous pouvez remplir le formulaire complet sur <a href="https://www.bethechurch.fr">le site</a>.<br>
                            <br>
                            Dans la joie de vous accueillir cet été !<br>
                            <br>
                            Pour l’équipe du Festival des paroisses<br>
                            '.$btc_options['contact_nom'].'<br>
                            <a href="mailto:'.$btc_options['contact_email'].'">'.$btc_options['contact_email'].'</a><br>
                            <a href="tel:'.preg_replace("/[^0-9\+]/", "", $btc_options['contact_tel']).'">'.$btc_options['contact_tel'].'</a>'
                ),
            )
        ),
    );
    create_POST_backend($cp_name, $prefix, 'preinscrire', $accepted_users = 'all', $fields, $backend_options); // the final action_name of the backend will be $prefix.'inscrire'
    $html_form_options = array(
        'title' => '',
        'text_btn_submit' => 'Je me pré-inscris !',
        'required' => array('@ALL'),
    );
    // ... et le formulaire HTML que l'on enregistre comme un shortcode
    create_HTML_form_shortcode($cp_name, $prefix.'_preinscrire', $html_form_options, $fields); // shortcode will be $action_name.'-show-form' = "ccnbtc_preinscrire-show-form"
}

add_action( 'init', 'ccnbtc_custom_post_type_preinscriptions', 0 );

?>