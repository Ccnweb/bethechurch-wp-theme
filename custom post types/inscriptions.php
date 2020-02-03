<?php

// we need to have the CCN Library plugin activated
if (!defined('CCN_LIBRARY_PLUGIN_DIR')) {
    echo ('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
    die('global var CCN_LIBRARY_PLUGIN_DIR is not defined');
}

require_once(CCN_LIBRARY_PLUGIN_DIR . '/log.php'); use \ccn\lib\log as log;
require_once(CCN_LIBRARY_PLUGIN_DIR . '/lib.php'); use \ccn\lib as lib;

// we load here some high-level functions to create custom post types
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-custom-post-type.php');
// on charge la librairie pour créer des REST POST backend
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-rest-backend.php');
// on charge la libraire pour créer des formulaires HTML
require_once(CCN_LIBRARY_PLUGIN_DIR . 'create-cp-html-forms.php');
require_once(CCN_LIBRARY_PLUGIN_DIR . '/forms/lib.forms.php'); use \ccn\lib\html_fields as fields;


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

    $btc_options = array_merge(array(
        'date_festival_from' => '',
        'date_festival_to' => '',
        'etat_inscriptions' => '',
        'contact_nom' => '',
        'contact_email' => '',
        'contact_tel' => '',
    ), get_option('btc-config'));

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
            'html_label' => __('Je suis', 'ccnbtc'),
            'type' => "radio",
            'options' => array(
                'paroisse' => __("Membre d'une paroisse animée par la Communauté du Chemin Neuf", 'ccnbtc'),
                'frat_paroissiale' => "Membre des Fraternités Paroissiales Missionnaires du Chemin-Neuf",
                'communautaire' => "Membre de la Communauté ou de la Communion du Chemin Neuf",
            ),
            //'options_preciser' => ['autre*', 'paroisse*'], // * veut dire que c'est requis
            'wrapper' => [
                'start' => '<p class="form-label">'.__('Je suis', 'ccnbtc').'</p>',
                'end' => ''
            ],
        ),
        array(
            'id' => $prefix.'key_ma_paroisse',
            'html_label' => __('Ma paroisse', 'ccnbtc'),
            'type' => 'text',
        ),
        array( // Je viens comme (couple, famille, ...)
            'id' => $prefix.'_key_persontype',
            'description'  => "Le type de personne (individuel, couple, famille, ...)",
            'html_label' => __('Je viens comme', 'ccnbtc'),
            'type' => "dropdown",
            'options' => array(
                'individuel' => __("Individuel", 'ccnbtc'),
                'couple_sans_enfants' => __("Couple sans enfants", 'ccnbtc'),
                'famille' => __("Famille", 'ccnbtc'),
                'parent_seul' => __('Parent seul avec enfants', 'ccnbtc'),
            ),
        ),
        // SPECIFIQUE CAS 1 - individuel ou parent_seul
        array( // Nom et Prénom
            'id' => $prefix.'_key_indiv', // le nom de la meta key (sera complété par _firstname et _name)
            'description'  => "Person first name and name for inscription",
            'html_label' => array(
                'prenom' => __('Prénom', 'ccnbtc'),
                'nom' => __('Nom', 'ccnbtc')
            ),
            'type' => "nom_prenom",
        ),
        array( // Nom prénom - Elle
            'id' => $prefix.'_key_indiv_elle', 
            'copy' => $prefix.'_key_indiv', 
            'required' => [true, false],
            'html_label' => array('prenom' => __('Prénom', 'ccnbtc'), 'nom' => __('Nom si différent', 'ccnbtc')), 
            'wrapper' => array('start' => '<p class="form-label">Elle</p>', 'end' => '')), // une copie de Nom prénom pour "Elle"
        array('id' => $prefix.'_key_indiv_lui', 'copy' => $prefix.'_key_indiv', 'wrapper' => array('start' => '<p class="form-label">'.__('Lui', 'ccnbtc').'</p>', 'end' => '')), // une copie de Nom prénom pour "Lui"
        array( // Homme/Femme
            'id' => $prefix.'_key_genre',
            'description'  => "Person gender for inscription",
            'html_label' => __('Genre', 'ccnbtc'),
            'type' => "radio",
            'options' => array(
                'homme' => __('Homme', 'ccnbtc'),
                'femme' => __('Femme', 'ccnbtc'),
            ),
            'layout' => 'row',
        ),
        array( // Date de naissance
            'id' => $prefix.'_key_birthdate',
            'description'  => "Person birth date",
            'html_label' => __('Date de naissance', 'ccnbtc'),
            'type' => "date", // TODO restreindre aux personnes majeures
            'label' => 'placeholder',
            'wrapper' => 'bootstrap',
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
        array('id' => $prefix.'_key_email_elle', 'copy' => $prefix.'_key_email'), // une copie de email pour "Elle"
        array('id' => $prefix.'_key_email_lui', 'copy' => $prefix.'_key_email'), // une copie de email pour "Lui"
        array( // Téléphone
            'id' => $prefix.'_key_phone',
            'description'  => "Person phone number",
            'html_label' => 'Portable',
            'type' => "tel",
            'regex_pattern' => '^\+?[0-9\-\s\.]{6,}$',
            'label' => 'placeholder',
            'wrapper' => 'bootstrap',
        ),
        array('id' => $prefix.'_key_phone_elle', 'copy' => $prefix.'_key_phone'), // une copie de phone pour "Elle"
        array('id' => $prefix.'_key_phone_lui', 'copy' => $prefix.'_key_phone'), // une copie de phone pour "Lui"
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
                    'wrapper' => 'bootstrap',
                ),
                array( // Homme/Femme
                    'id' => $prefix.'_child_genre',
                    'description'  => "Child gender for inscription",
                    'html_label' => 'Genre',
                    'type' => "dropdown",
                    'options' => array(
                        'homme' => 'Garçon',
                        'femme' => 'Fille',
                    ),
                    'layout' => 'row',
                ),
            ),
            'wrapper' => ['start' => '<p class="form-label">Enfants</p>', 'end' => ''],
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
            'options_preciser' => array('autre*'),
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
            'description'  => "Le moyen de transport à l'aller",
            'html_label' => 'Moyen de transport',
            'type' => "dropdown",
            'options' => array(
                'avion' => __("Avion"),
                'train' => __("Train"),
                'voiture' => __("Voiture"),
                'ne_sais_pas' => __('Ne sais pas encore'),
            ),
            'wrapper' => array('start' => '<p class="form-label">Transport aller</p>', 'end' => ''),
        ),
        array( // moyen de transport retour
            'id' => $prefix.'_key_moyen_transport_retour',
            'description'  => "Le moyen de transport au retour",
            'html_label' => 'Moyen de transport',
            'type' => "dropdown",
            'options' => array(
                'avion' => __("Avion"),
                'train' => __("Train"),
                'voiture' => __("Voiture"),
                'ne_sais_pas' => __('Ne sais pas encore'),
            ),
            'wrapper' => array('start' => '<p class="form-label">Transport retour</p>', 'end' => ''),
        ),
        array( // Date aller (si avion ou train)
            'id' => $prefix.'_date_aller',
            'description'  => "Date d'arrivée à l'aller",
            'html_label' => "Date d'arrivée",
            'type' => "datetime",
            'required' => false, // TODO faire mieux que ça : required uniquement si transport = avion ou train !
        ),
        array( // Date retour (si avion ou train)
            'id' => $prefix.'_date_retour',
            'description'  => "Date de départ",
            'html_label' => "Date de départ",
            'type' => "datetime",
            'required' => false, // TODO faire mieux que ça : required uniquement si transport = avion ou train !
        ),
        array( // Gare/aéroport aller
            'id' => $prefix.'_gare_aller',
            'html_label' => "Gare/aéroport d'arrivée",
            'type' => 'text',
            "required" => false,
        ),
        array( // Gare/aéroport retour
            'id' => $prefix.'_gare_retour',
            'html_label' => "Gare/aéroport de départ",
            'type' => 'text',
            "required" => false,
        ),
        array(
            'id' => $prefix.'_paiement_modalite',
            'html_label' => 'Je paye',
            "type" => 'dropdown',
            "options" => array(
                /* "now_all" => "maintenant la totalité",
                "now_partial" => "maintenant une partie", */
                "on_site" => "sur place",
            ),
            "wrapper" => array('start' => '<p class="form-label">Je paye (<a href="/infos-pratiques/#post__tarifs-2" target="_blank">Détail des prix</a>)</p>', 'end' => ''),
        ),
        array(
            'id' => $prefix.'_paiement_moyen',
            'type' => 'radio',
            'html_label' => 'Moyen de paiement',
            'options' => array(
                'cb' => 'Carte Bleue (disponible prochainement)',
                'cheque' => 'Chèque',
            ),
            /* "wrapper" => array('start' => '<p class="form-label">Moyen de paiement</p>', 'end' => ''), */
        ),
        array(
            'id' => $prefix.'_html_paiement_description',
            'type' => 'html',
            'html' => '<p class="form-description">
                Chèque à l\'ordre de la <u>Communauté du Chemin Neuf</u>.
                </p>
                <p class="form-description">
                    <p class="form-description">À envoyer à l\'adresse suivante :</p>
                    <p class="form-description bg-green p-2 txt-white rounded"><b>Secrétariat Festival Be The Church</b><br>
                    Abbaye d\'Hautecombe<br>3700 route de l\'Abbaye<br>73310 ST PIERRE DE CURTILLE</p>
                </p>',
        ),
        // checkbox I accept the privacy policy
        [
            'id' => $prefix.'_rgpd_check',
            'type' => 'checkbox',
            'label' => 'J\'accepte <a href="'.lib\get_image_url_by_title('Politique de confidentialité').'" target="_blank">la politique de confidentialité de la Communauté du Chemin Neuf</a>',
            'value_true' => 'true',
        ],
    );


    // =====================================================
    // == 3. == on crée tous les : metakeys, metabox/champs html, save callbacks, ...
    // =====================================================
    $metabox_options = array(
        array(
            'title' => __('Informations préliminaires'),
            'fields' => array($prefix.'_reference', $prefix.'_key_jesuis', $prefix.'_key_persontype'),
        ),
        array(
            // condition permet de dire quand afficher cette metabox
            'condition' => '{{'.$prefix.'_key_persontype}} == "individuel" || {{'.$prefix.'_key_persontype}} == "parent_seul"', // condition qui doit être compréhensible par PHP et JS !
            'title' => __('Informations personnelles'),
            'fields' => array($prefix.'_key_indiv', $prefix.'_key_genre', $prefix.'_key_birthdate', $prefix.'_key_email', $prefix.'_key_phone', $prefix.'_key_address'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => __('Informations du couple'),
            'fields' => array($prefix.'_key_address'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => __('Lui'),
            'fields' => array($prefix.'_key_indiv_lui', $prefix.'_key_birthdate_lui', $prefix.'_key_email_lui', $prefix.'_key_phone_lui'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
            'title' => __('Elle'),
            'fields' => array($prefix.'_key_indiv_elle', $prefix.'_key_birthdate_elle', $prefix.'_key_email_elle', $prefix.'_key_phone_elle'),
        ),
        array(
            'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "parent_seul"',
            'title' => __('Enfants'),
            'fields' => array($prefix.'_childrenGR'),
        ),
        array( // Logement
            'title' => __('Logement'),
            'fields' => array($prefix.'_key_logement', $prefix.'_key_logement_remarques'),
        ),
        array( // Transport ALLER
            'title' => __('Transport aller'),
            'fields' => array($prefix.'_key_moyen_transport_aller', $prefix.'_date_aller', $prefix.'_gare_aller'),
            'field_conditions' => array(
                $prefix.'_date_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} != "ne_sais_pas"',
                $prefix.'_gare_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} == "avion" || {{'.$prefix.'_key_moyen_transport_aller}} == "train"',
            ),
        ),
        array( // Transport RETOUR
            'title' => __('Transport retour'),
            'fields' => array($prefix.'_key_moyen_transport_retour', $prefix.'_date_retour', $prefix.'_gare_retour'),
            'field_conditions' => array(
                $prefix.'_date_retour' => '{{'.$prefix.'_key_moyen_transport_retour}} != "ne_sais_pas"',
                $prefix.'_gare_retour' => '{{'.$prefix.'_key_moyen_transport_retour}} == "avion" || {{'.$prefix.'_key_moyen_transport_retour}} == "train"',
            ),
        ),
        [
            'title' => __('RGPD'),
            'fields' => [$prefix.'_rgpd_check'],
        ],
    );
    create_custom_post_fields($cp_name, $cp_slug, $metabox_options, $prefix, $fields);

    // =====================================================
    // == 4. ==... et le formulaire HTML que l'on enregistre comme un shortcode
    // =====================================================
    $html_form_options = array(
        'title' => __('INSCRIPTIONS'),
        'text_btn_submit' => __("Je m'inscris !"),
        'custom_classes' => ['step' => 'w-100'], // adds custom css classes to form elements
        'required' => array('@ALL'),
        //'custom_logic_path' => get_template_directory() . '/custom post types/inscriptions_logic.js', // la logique complexe du formulaire
    );
    $steps = array(
        array(
            'id' => 'je-suis',
            'title' => __('Présentation'),
            'fields' => array(
                $prefix.'_key_jesuis', 
                $prefix.'key_ma_paroisse',
                $prefix.'_key_persontype'
            ),
        ),
        array(
            'id' => 'infos-personnelles',
            'title' => __('Informations personnelles'),
            'switch' => array(
                array(
                    'id' => 'infos-personnelles-individuel',
                    'condition' => '{{'.$prefix.'_key_persontype}} == "individuel" || {{'.$prefix.'_key_persontype}} == "parent_seul"',
                    'title' => __('Informations personnelles'),
                    'fields' => array($prefix.'_key_indiv', $prefix.'_key_genre', $prefix.'_key_birthdate', $prefix.'_key_email', $prefix.'_key_phone'),
                ),
                array(
                    'id' => 'infos-personnelles-adresse',
                    'fields' => array($prefix.'_key_address'),
                ),
                array(
                    'id' => 'infos-personnelles-couple',
                    'condition' => '{{'.$prefix.'_key_persontype}} == "famille" || {{'.$prefix.'_key_persontype}} == "couple_sans_enfants"',
                    'title' => __('Informations du couple'),
                    'fields' => array(
                                $prefix.'_key_indiv_lui', $prefix.'_key_birthdate_lui', $prefix.'_key_email_lui', $prefix.'_key_phone_lui',
                                $prefix.'_key_indiv_elle', $prefix.'_key_birthdate_elle', $prefix.'_key_email_elle', $prefix.'_key_phone_elle'
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
            'title' => __('Logement & transport'),
            'fields' => array(
                $prefix.'_key_logement', $prefix.'_key_logement_remarques',
                $prefix.'_key_moyen_transport_aller', $prefix.'_date_aller', $prefix.'_gare_aller',
                $prefix.'_key_moyen_transport_retour', $prefix.'_date_retour', $prefix.'_gare_retour'
            ),
            'field_conditions' => array(
                $prefix.'_date_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} != "ne_sais_pas"',
                $prefix.'_gare_aller' => '{{'.$prefix.'_key_moyen_transport_aller}} == "avion" || {{'.$prefix.'_key_moyen_transport_aller}} == "train"',
                $prefix.'_date_retour' => '{{'.$prefix.'_key_moyen_transport_retour}} != "ne_sais_pas"',
                $prefix.'_gare_retour' => '{{'.$prefix.'_key_moyen_transport_retour}} == "avion" || {{'.$prefix.'_key_moyen_transport_retour}} == "train"',
            ),
        ),
        /* array(
            'id' => 'paiement',
            'title' => __('Confirmation'),
            'fields' => array($prefix.'_paiement_modalite', $prefix.'_paiement_moyen', $prefix.'_html_paiement_description', $prefix.'_rgpd_check'),
            'field_conditions' => array(
                $prefix.'_paiement_moyen' => '{{'.$prefix.'_paiement_modalite}} == "now_all" || {{'.$prefix.'_paiement_modalite}} == "now_partial"',
                $prefix.'_html_paiement_description' => '{{'.$prefix.'_paiement_moyen}} == "cheque"',
            ),
        ), */
    );
    create_HTML_form_shortcode($cp_name, $prefix.'_inscrire', $html_form_options, $fields, $steps); // shortcode will be $action_name.'-show-form' = "ccnbtc_inscrire-show-form"

    // =====================================================
    // == 5. == on crée le backend REST pour POSTer de nouvelles inscriptions ($action_name = 'ccnbtc_inscrire')
    // =====================================================

    $backend_options = array(
        'post_status' => 'private', // 'private' because inscriptions should be private and therefore not available through the rest api without authentication !
        'steps' => $steps,
        'computed_fields' => array(
            'post_title' => function($post_values) use ($prefix) { 
                if (!isset($post_values[$prefix.'_key_persontype'])) return 'unknown';
                if (in_array($post_values[$prefix.'_key_persontype'], array('individuel', 'parent_seul'))) return $post_values[$prefix.'_key_indiv_firstname'] . ' ' . $post_values[$prefix.'_key_indiv_name'];
                if (in_array($post_values[$prefix.'_key_persontype'], array('couple_sans_enfants', 'famille'))) return $post_values[$prefix.'_key_indiv_lui_firstname'] . ' & ' . $post_values[$prefix.'_key_indiv_elle_firstname'] . ' ' . $post_values[$prefix.'_key_indiv_lui_name'];
                return 'inconnu';
            },
            'email_contact' => function($post_values) use ($prefix) {
                if (!isset($post_values[$prefix.'_key_persontype'])) return '__IGNORE__';
                if (in_array($post_values[$prefix.'_key_persontype'], array('individuel', 'parent_seul'))) return $post_values[$prefix.'_key_email'];
                if (in_array($post_values[$prefix.'_key_persontype'], array('couple_sans_enfants', 'famille'))) return $post_values[$prefix.'_key_email_lui'];
            },
            'email_contact2' => function($post_values) use ($prefix) {
                if (!isset($post_values[$prefix.'_key_persontype'])) return '__IGNORE__';
                if (in_array($post_values[$prefix.'_key_persontype'], array('individuel', 'parent_seul'))) return '__IGNORE__';
                if (in_array($post_values[$prefix.'_key_persontype'], array('couple_sans_enfants', 'famille'))) return $post_values[$prefix.'_key_email_elle'];
            },
        ),
        'custom_validations' => array(
            'all_emails_unique' => function($fields, $sanitized, $existing_posts) use ($prefix) {
                $old_emails = lib\array_flatten(lib\array_map_attr($existing_posts, $prefix.'_key_email'));
                $old_emails = array_merge($old_emails, lib\array_flatten(lib\array_map_attr($existing_posts, $prefix.'_key_email_elle')));
                $old_emails = array_merge($old_emails, lib\array_flatten(lib\array_map_attr($existing_posts, $prefix.'_key_email_lui')));

                if (in_array($sanitized[$prefix.'_key_persontype'], array('individuel', 'parent_seul'))) {
                    if (in_array($sanitized[$prefix.'_key_email'], $old_emails)) 
                        return array('success' => false, 'errno' => 'DUPLICATE_EMAIL_ADDRESS', 'descr' => 'L\'adresse email '.$sanitized[$prefix.'_key_email'].' est déjà utilisée dans une inscription existante');
                } else if (in_array($sanitized[$prefix.'_key_persontype'], array('couple_sans_enfants', 'famille'))) {
                    $emails = [$sanitized[$prefix.'_key_email_elle'], $sanitized[$prefix.'_key_email_lui']];
                    foreach ($emails as $email)
                        if (in_array($email, $old_emails)) 
                            return array('success' => false, 'errno' => 'DUPLICATE_EMAIL_ADDRESS', 'descr' => 'L\'adresse email '.$email.' est déjà utilisée dans une inscription existante');
                }
                return true;
            },
        ),
        'on_before_save_post' => array(
            function($new, $old) {
                $res = array('success' => 'true');

                return $res;
            }
        ),
        'send_email' => array(
            array(
                'addresses' => array('web@chemin-neuf.org', 'contact@bethechurch.fr'),
                'subject' => 'Inscription - {{post_title}}',
                'model' => get_template_directory() . '/custom post types/inscription_email.html',
                'model_args' => array(
                    'title' => '',
                    'subtitle' => '',
                    'body' => 'Bonjour,<br>
                            Votre inscription est bien validée selon les informations ci-dessous.<br>
                            Pour toute question, n’hésitez pas à nous contacter.<br>
                            Pour les membres des fraternités paroissiales et pour les frères et sœurs de la Communauté, merci de bien noter que nous nous retrouvons dès le dimanche 26 juillet (accueil à partir de 16h).<br>
                            <br>
                            Dans la joie de vous accueillir cet été&nbsp;!<br>
                            <br><br>
                            Pour l’équipe du Festival des paroisses<br>
                            '.$btc_options['contact_nom'].'<br>
                            <a href="mailto:'.$btc_options['contact_email'].'">'.$btc_options['contact_email'].'</a><br>
                            <a href="tel:'.preg_replace("/[^0-9\+]/", "", $btc_options['contact_tel']).'">'.$btc_options['contact_tel'].'</a>',
                ),
            ),
            array(
                'addresses' => array('email_contact', 'email_contact2'),
                'subject' => 'Félicitations ! Votre inscription au Festival Be The Church est confirmée !',
                'model' => get_template_directory() . '/custom post types/inscription_email.html',
                'model_args' => array(
                    'title' => 'Festival des Paroisses',
                    'subtitle' => 'Inscription au Festival des Paroisses Be The Church',
                    'welcome_msg' => 'Bonjour,<br>
                            Votre inscription est bien validée selon les informations ci-dessous.<br>
                            Pour toute question, n’hésitez pas à nous contacter.<br>
                            Pour les membres des fraternités paroissiales et pour les frères et sœurs de la Communauté, merci de bien noter que nous nous retrouvons dès le dimanche 26 juillet (accueil à partir de 16h).<br>
                            <br>
                            Dans la joie de vous accueillir cet été&nbsp;!<br>
                            <br><br>
                            Pour l’équipe du Festival des paroisses<br>
                            '.$btc_options['contact_nom'].'<br>
                            <a href="mailto:'.$btc_options['contact_email'].'">'.$btc_options['contact_email'].'</a><br>
                            <a href="tel:'.preg_replace("/[^0-9\+]/", "", $btc_options['contact_tel']).'">'.$btc_options['contact_tel'].'</a>',
                ),
            )
        ),
    );
    create_POST_backend($cp_name, $prefix, 'inscrire', $accepted_users = 'all', $fields, $backend_options); // the final action_name of the backend will be $prefix.'inscrire'
    
}

add_action( 'init', 'ccnbtc_custom_post_type_inscriptions', 0 );

?>