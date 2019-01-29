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
    /**
     * Crée tout ce qu'il faut pour gérer les inscriptions
     * 
     * ## SOMMAIRE
     * 1. On crée un custom post type 'inscription'
     * 2.a On définit tous les fields/champs du formulaire d'inscription
     * 2.b On définit toutes les grandes étapes du formulaire
     * 3. On crée les metaboxes dans l'admin ui pour gérer/créer des inscriptions depuis l'admin ui
     * 4. On crée le backend pour récupérer les requêtes POST du formulaire HTML
     * 5. On crée le shortcode pour afficher le formulaire HTML où l'on veut
     */

    $prefix = "ccnbtc";
    $cp_name = 'inscription';

    // =====================================================
    // == 1. == on crée le custom post type 'inscription'
    // =====================================================
	$args = create_custom_post_info(
        $cp_name, 
        $genre = "f", 
        $post_icon = 'dashicons-tickets-alt', 
        $supports = array( 'title', 'custom-fields') // 'custom-fields' is required if we want to retrieve the meta_keys from the rest api
    );
    register_post_type( $cp_name, $args );
    $cp_slug = $args['rewrite']['slug'];
    

    // =====================================================
    // == 2.a == on définit les fields
    // =====================================================
    $fields = array(
        array( // Je suis (paroissien, communautaire, ...)
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
            'options_preciser' => ['autre', 'paroisse'],
            'wrapper' => [
                'start' => '<p class="form-label">Je suis</p>',
                'end' => ''
            ],
        ),
        array( // Je viens comme (couple, famille, ...)
            'id' => $prefix.'_key_persontype',
            'description'  => "Le type de personne (individuel, couple, famille, ...)",
            'html_label' => 'Je viens comme',
            'type' => "dropdown",
            'options' => array(
                'individuel' => "Individuel",
                'couple_sans_enfants' => "Couple sans enfants",
                'famille' => "Famille",
                'parent_seul' => 'Parent seul avec enfants',
            ),
        ),
        // SPECIFIQUE CAS 1 - individuel ou parent_seul
        array( // Nom et Prénom
            'id' => $prefix.'_key_indiv', // le nom de la meta key (sera complété par _firstname et _name)
            'description'  => "Person first name and name for inscription",
            'html_label' => array(
                'prenom' => 'Prénom',
                'nom' => 'Nom'
            ),
            'type' => "nom_prenom",
        ),
        array('id' => $prefix.'_key_indiv_elle', 'copy' => $prefix.'_key_indiv', 'wrapper' => array('start' => '<p class="form-label">Elle</p>', 'end' => '')), // une copie de Nom prénom pour "Elle"
        array('id' => $prefix.'_key_indiv_lui', 'copy' => $prefix.'_key_indiv', 'wrapper' => array('start' => '<p class="form-label">Lui</p>', 'end' => '')), // une copie de Nom prénom pour "Lui"
        array( // Homme/Femme
            'id' => $prefix.'_key_genre',
            'description'  => "Person gender for inscription",
            'html_label' => 'Genre',
            'type' => "radio",
            'options' => array(
                'homme' => 'Homme',
                'femme' => 'Femme',
            ),
            'layout' => 'row',
        ),
        array( // Date de naissance
            'id' => $prefix.'_key_birthdate',
            'description'  => "Person birth date",
            'html_label' => 'Date de naissance',
            'type' => "date", // TODO restreindre aux personnes majeures
            'label' => 'placeholder',
            'style' => 'bootstrap',
        ),
        array('id' => $prefix.'_key_birthdate_elle', 'copy' => $prefix.'_key_birthdate'), // une copie de birth date pour "Elle"
        array('id' => $prefix.'_key_birthdate_lui', 'copy' => $prefix.'_key_birthdate'), // une copie de birth date pour "Lui"
        array( // Email
            'id' => $prefix.'_key_email',
            'description'  => "Person email address",
            'html_label' => 'Email',
            'type' => "email",
            'label' => 'placeholder',
            'wrapper' => 'bootstrap',
        ),
        array('id' => $prefix.'_key_email_elle', 'copy' => $prefix.'_key_email'), // une copie de birth date pour "Elle"
        array('id' => $prefix.'_key_email_lui', 'copy' => $prefix.'_key_email'), // une copie de birth date pour "Elle"
        array( // Adresse
            'id' => $prefix.'_key_address',
            'description'  => "Person postal address",
            'html_label' => array(
                'street' => 'Rue',
                'postalcode' => 'Code postal',
                'city' => 'Ville'
            ),
            'type' => "address",
            'label' => 'placeholder',
            'wrapper' => array('start' => '<p class="form-label">Adresse</p>', 'end' => ''),
        ),
        array( // Repeat group children
            'type' => 'REPEAT-GROUP',
            'id' => $prefix.'_childrenGR',
            'fields' => array(
                array( // Nom et Prénom
                    'id' => $prefix.'_key_child', // le nom de la meta key (sera complété par _firstname et _name)
                    'description'  => "Child first name and name for inscription",
                    'html_label' => array(
                        'prenom' => 'Prénom',
                        'nom' => 'Nom (si différent)'
                    ),
                    'type' => "nom_prenom",
                    'required' => [true, false],
                ),
                array( // Date de naissance
                    'id' => $prefix.'_child_birthdate',
                    'description'  => "Child birth date",
                    'html_label' => 'Date de naissance',
                    'type' => "date",
                    'label' => 'placeholder',
                ),
                array( // Homme/Femme
                    'id' => $prefix.'_child_genre',
                    'description'  => "Child gender for inscription",
                    'html_label' => 'Genre',
                    'type' => "dropdown",
                    'options' => array(
                        'homme' => 'Homme',
                        'femme' => 'Femme',
                    ),
                    'layout' => 'row',
                ),
            ),
        ),
        array( // Logement
            'id' => $prefix.'_key_logement',
            'description'  => "Le type de logement",
            'html_label' => 'Logement',
            'type' => "radio",
            'options' => array(
                'tente_perso' => "Tente personnelle",
                'caravane_perso' => "Caravane personnelle",
                'camping_car_perso' => "Camping-car personnel",
                'tente_co' => 'Tente de la Communauté',
                'autre' => 'Autre (Je me loge par mes propres moyens)'
            ),
            'options_preciser' => array('autre'),
            'wrapper' => array('start' => '<p class="form-label">Logement</p>', 'end' => ''),
        ),
        array( // Logement remarques
            'id' => $prefix.'_key_logement_remarques',
            'type' => 'textarea',
            'required' => false,
            'html_label' => 'Remarques',
            'description' => 'Remarques sur le logement',
        ),
        array( // moyen de transport aller
            'id' => $prefix.'_key_moyen_transport_aller',
            'description'  => "Le moyen de transprot à l'aller",
            'html_label' => 'Moyen de transport',
            'type' => "dropdown",
            'options' => array(
                'avion' => "Avion",
                'train' => "Train",
                'voiture' => "Voiture",
                'ne_sais_pas' => 'Ne sais pas encore',
            ),
            'wrapper' => array('start' => '<p class="form-label">Aller</p>', 'end' => ''),
        ),
        array( // moyen de transport retour
            'id' => $prefix.'_key_moyen_transport_retour',
            'description'  => "Le moyen de transprot au retour",
            'html_label' => 'Moyen de transport',
            'type' => "dropdown",
            'options' => array(
                'avion' => "Avion",
                'train' => "Train",
                'voiture' => "Voiture",
                'ne_sais_pas' => 'Ne sais pas encore',
            ),
            'wrapper' => array('start' => '<p class="form-label">Retour</p>', 'end' => ''),
        ),
        array( // Date aller (si avion ou train)
            'id' => $prefix.'_date_aller',
            'description'  => "Date d'arrivée à l'aller",
            'html_label' => "Date d'arrivée",
            'type' => "date",
            'required' => false, // TODO faire mieux que ça : required uniquement si transport = avion ou train !
        ),
    );


    // =====================================================
    // == 3. == on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    // =====================================================
    $metabox_options = array(
        array(
            'title' => 'Informations préliminaires',
            'fields' => array($prefix.'_key_jesuis', $prefix.'_key_persontype'),
        ),
        array(
            // condition permet de dire quand afficher cette metabox
            'condition' => '{{'.$prefix.'_key_persontype}} == "individuel" || {{'.$prefix.'_key_persontype}} == "parent_seul"', // condition qui doit être compréhensible par PHP et JS !
            'title' => 'Informations personnelles',
            'fields' => array($prefix.'_key_indiv', $prefix.'_key_genre', $prefix.'_key_birthdate', $prefix.'_key_email', $prefix.'_key_address'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => 'Informations du couple',
            'fields' => array($prefix.'_key_address'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => 'Lui',
            'fields' => array($prefix.'_key_indiv_lui', $prefix.'_key_birthdate_lui', $prefix.'_key_email_lui'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => 'Elle',
            'fields' => array($prefix.'_key_indiv_elle', $prefix.'_key_birthdate_elle', $prefix.'_key_email_elle'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "parent_seul"',
            'title' => 'Enfants',
            'fields' => array($prefix.'_childrenGR'),
        ),
        array( // Logement
            'title' => 'Logement',
            'fields' => array($prefix.'_key_logement', $prefix.'_key_logement_remarques'),
        ),
        array( // Transport ALLER
            'title' => 'Transport aller',
            'fields' => array($prefix.'_key_moyen_transport_aller', $prefix.'_date_aller'),
            'field_conditions' => array(
                $prefix.'_date_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} == "avion" || {{'.$prefix.'_key_moyen_transport_aller}} == "train"',
            ),
        ),
    );
    create_custom_post_fields($cp_name, $cp_slug, $metabox_options, $prefix, $fields);


    // =====================================================
    // == 4. == on crée le backend REST pour POSTer des nouvelles inscriptions ($action_name = 'ccnbtc_inscrire')
    // =====================================================
    create_POST_backend($cp_name, $prefix, 'inscrire', $accepted_users = 'all', $fields); // the final action_name of the backend will be $prefix.'inscrire'
    

    // =====================================================
    // == 5. ==... et le formulaire HTML que l'on enregistre comme un shortcode
    // =====================================================
    $html_form_options = array(
        'title' => 'INSCRIPTIONS',
        'submit_btn_text' => 'Je m\'inscris !',
        'required' => array('@ALL'),
        'computed_fields' => array(
            'post_title' => "() => getVal('{$prefix}_key_name_field')",
        ),
        //'custom_logic_path' => get_template_directory() . '/custom post types/inscriptions_logic.js', // la logique complexe du formulaire
    );
    $steps = array(
        array(
            'id' => 'je-suis',
            'title' => 'Présentation',
            'fields' => array(
                $prefix.'_key_jesuis', 
                $prefix.'_key_persontype'
            ),
        ),
        array(
            'id' => 'infos-personnelles',
            'title' => 'Informations personnelles',
            'switch' => array(
                array(
                    'id' => 'infos-personnelles-individuel',
                    'condition' => '{{'.$prefix.'_key_persontype}} == "individuel" || {{'.$prefix.'_key_persontype}} == "parent_seul"',
                    'title' => 'Informations personnelles',
                    'fields' => array($prefix.'_key_indiv', $prefix.'_key_genre', $prefix.'_key_birthdate', $prefix.'_key_email', $prefix.'_key_address'),
                ),
                array(
                    'id' => 'infos-personnelles-couple',
                    'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
                    'title' => 'Informations du couple',
                    'fields' => array($prefix.'_key_address', 
                                $prefix.'_key_indiv_lui', $prefix.'_key_birthdate_lui', $prefix.'_key_email_lui',
                                $prefix.'_key_indiv_elle', $prefix.'_key_birthdate_elle', $prefix.'_key_email_elle'
                    ),
                ),
                array(
                    'id' => 'infos-enfants',
                    'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "parent_seul"',
                    'fields' => array($prefix.'_childrenGR'),
                ),
            ),
        ),
        array(
            'id' => 'logement-transport',
            'title' => 'Logement & transport',
            'fields' => array(
                $prefix.'_key_logement', $prefix.'_key_logement_remarques',
                $prefix.'_key_moyen_transport_aller', $prefix.'_date_aller',
                $prefix.'_key_moyen_transport_retour', $prefix.'_date_retour',
            ),
            'field_conditions' => array(
                $prefix.'_date_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} == "avion" || {{'.$prefix.'_key_moyen_transport_aller}} == "train"',
            ),
        ),
    );
    create_HTML_form_shortcode($cp_name, $prefix.'_inscrire', $html_form_options, $fields, $steps); // shortcode will be $action_name.'-show-form' = "ccnbtc_inscrire-show-form"
}

add_action( 'init', 'ccnbtc_custom_post_type_inscriptions', 0 );

?>