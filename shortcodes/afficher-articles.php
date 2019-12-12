<?php

require_once(get_template_directory() . '/lib/php/lib.php');

// on charge tous les fichiers php dans l'arborescence de pages/ qui vérifient la regex
require_once_all_regex(get_template_directory() . '/pages/', "/^afficher-articles-/i");

/* =============================================== */
/*          AFFICHER PLUSIEURS ARTICLES            */
/* =============================================== */

function ccnbtc_shortcode_afficher_articles() {

    $shortcode_fun = function($atts = [], $content = null, $tag = '') {

        // == 1. == on normalise les attributs
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // == 2. == on gère les valeurs par défaut des attributs
        $atts = shortcode_atts(
            array(
                'categorie' => 'unknown',
            ), $atts, 'afficher-articles' );


        // == 3. == on récupère les articles de la catégorie
        $cat_in = [];
        $cat_original = get_category_by_slug( $atts['categorie'] );
        if ($cat_original) $cat_in[] = $cat_original->term_id;
        $local_cat_slug = $atts['categorie']."-".pll_current_language();
        $cat_local = get_category_by_slug( $local_cat_slug );
        if ($cat_local) $cat_in[] = $cat_local->term_id;

        if (empty($cat_in)) return 'no category found for '.$atts['categorie'];
        $query_args = array(
            'category__in'  =>  $cat_in,
            'post_status'   => 'publish',
            'lang'          =>  pll_current_language(),
            'meta_key'      => 'ccnbtc_post_order',
            'orderby'       => 'meta_value_num', // nécessaire lorsque la meta_key est numérique
            'order'         => 'ASC',
            'limit'         => 100,
        );
        $query = new WP_Query( $query_args );
        //echo "CATEGORIE = ".$atts['categorie'].", REQUEST=".$query->request."\n<br>";
        /* global $wpdb;
        echo "PREFIX=".$wpdb->prefix."\n<br>";
        $slug = $atts['categorie'];
        $p = $wpdb->prefix;
        $sql_query = "SELECT ".$p."posts.ID, ".$p."posts.post_title, ".$p."posts.post_name 
                    FROM `".$p."posts`, `".$p."term_relationships`, `".$p."terms` 
                    WHERE ".$p."term_relationships.object_id = ".$p."posts.ID 
                        AND ".$p."terms.term_id = ".$p."term_relationships.term_taxonomy_id AND ".$p."terms.slug = '$slug'";
        $add_lang_condition = "AND ";
        echo "QUERY=".$sql_query."\n<br>";
        $results = $wpdb->get_results($sql_query, ARRAY_A);
        echo "RESULTS=".json_encode($results)."\n<br>"; */
        
        // == 4. == on construit le HTML
        $html = '';
        $compteur = 1;

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $html .= render_HTML($atts['categorie'], $query, $compteur);
                $compteur++;
            }
            /* Restore original Post Data */
            wp_reset_postdata();

        } else {
            // no posts found ($query->request permet de voir la requête SQL)
            $html .= '<section class="row section bg-green" data-index="1">
                <div class="col-lg-12">Aucun article de la catégorie '.$atts['categorie']." n'a été trouvé, sorry :(<br>".$query->request."</div>
            </section>";
        }
        
        return $html;
    };

    add_shortcode( 'afficher-articles', $shortcode_fun);
};

add_action('init', 'ccnbtc_shortcode_afficher_articles');





function render_HTML($categorie, $query, $compteur) {
    /**
     * This renders the HTML depending on the category
     */

    if ($categorie == 'festival_accueil') return render_HTML_homepage($categorie, $query, $compteur);
    if ($categorie == 'intervenant') return render_HTML_intervenant($categorie, $query, $compteur);
    if ($categorie == 'programme') return render_HTML_programme($categorie, $query, $compteur);
    if ($categorie == 'programme_accueil') return render_HTML_programme_homepage($categorie, $query, $compteur);
    //if ($categorie == 'journee-type') return render_HTML_journeetype($categorie, $query, $compteur);


    // ==================
    // PARAMS

    // ==================

    $query->the_post();
    $posttags = get_the_tags();
    $slug = basename( get_permalink() );

    // type de mise en page
    $mise_en_page = "texte-centre"; // 'texte-centre' ou 'texte-droite' ou 'texte-gauche'

    // Post title
    $title_orig = get_the_title();
    
    // Background image
    $bg_img = buildBgImg(get_the_post_thumbnail_url());
    

    // computed properties
    $bg_color = 'green';
    $titre_position = 'titre-centre';
    $titre_style = 'titre-double'; // 'titre-simple' ou 'titre-double' avec hollow title (titre + son "ombre")
    $titre_hidden = false;
    $gouttes = false;

    if ($posttags) {
        foreach($posttags as $tag) {
            // bg_image
            if(preg_match("/^fond\-(.*)$/i", $tag->name, $matches)) {
                if (count($matches) > 1) $bg_color = $matches[1];
            }
            // mise en page
            if(preg_match("/^texte\-(gauche|droite|centre)$/i", $tag->name, $matches)) {
                $mise_en_page = $matches[0];
            }
            // position titre
            if(preg_match("/^titre\-(gauche|droite|centre)$/i", $tag->name, $matches)) {
                $titre_position = $matches[0];
            }
            // style titre
            if(preg_match("/^titre\-(simple|double)$/i", $tag->name, $matches)) {
                $titre_style = $matches[0];
            }
            // hide title
            if(preg_match("/^titre\-cach[ée]$/i", $tag->name, $matches)) {
                $titre_hidden = true;
            }
            // gouttes
            if(preg_match("/^gouttes?$/i", $tag->name, $matches)) {
                $gouttes = true;
            }
        }
    }
    
    $flex_type = "d-flex flex-row";
    if ($mise_en_page == "texte-centre") $flex_type = "d-flex flex-column justify-content-around";

    


    // Render HTML

    // the title
    $html_title = (!$titre_hidden) ? render_HTML_title($title_orig, $mise_en_page, $bg_color, $titre_position, $titre_style) : '';

    // special case if no title
    $special_texte_no_title = '';//($title == '') ? "align-self-start notitle-text-left": '';

    // mb-auto
    $ifmbauto = ($mise_en_page == 'texte-centre') ? ' mb-auto' : '';

    // taille de la colonne du texte du slide
    $text_column_classes = ($mise_en_page !== 'texte-centre') ? 'col-sm-12 col-md-8 d-flex flex-column align-items-start': 'col-md-12';
    $text_column_classes = ($mise_en_page !== 'texte-centre' && $title_orig == '') ? 'col-sm-12 col-md-6': $text_column_classes;


    // lien pour éditer l'article
    $ifeditlink = (current_user_can('edit_posts')) ? '<a class="edit_post_link" href="'.get_edit_post_link(get_the_ID()).'">'.__('Éditer', 'ccnbtc').'</a>' : '';

    // content
    $my_content = apply_filters('the_content', get_the_content());

    // gouttes
    $images_svg = '';
    if ($gouttes) {
        $images_svg = '<img class="goutte goutte_rouge" src="'.get_template_directory_uri().'/img/goutte rouge.svg"/>';
        $images_svg .= '<img class="goutte goutte_jaune" src="'.get_template_directory_uri().'/img/goutte verte.svg"/>';
        $images_svg .= '<img class="goutte goutte_bleu_clair" src="'.get_template_directory_uri().'/img/goutte bleu clair.svg"/>';
        $images_svg .= '<img class="goutte goutte_rouge_petite" src="'.get_template_directory_uri().'/img/goutte rouge petite.svg"/>';
        $images_svg .= '<img class="goutte goutte_bleu_fonce" src="'.get_template_directory_uri().'/img/goutte bleu fonce.svg"/>';  
    }

    // Add everything
    $html = '
        <section id="post__'.$slug.'" data-title="'.str_replace('§', ' ', $title_orig).'" class="row section layout__'.$mise_en_page.' '.$flex_type.' bg-'.$bg_color.'" data-index="'.$compteur.'" '.$bg_img.'>
    '.$ifeditlink;
    if (in_array($mise_en_page, array('texte-droite', 'texte-centre'))) $html .= $html_title;
    $html .= '<div class="slide_text '.$text_column_classes.' '.$special_texte_no_title.$ifmbauto.' mise_en_page__'.$mise_en_page.'">' . do_shortcode($my_content) . '</div>';
    if ($mise_en_page == 'texte-gauche') $html .= $html_title;
    $html .= '
            </div>'.$images_svg.'
        </section>';

    return $html;
}




function render_HTML_homepage($categorie, $query, $compteur) {
    /**
     * This renders the homepage HTML first slide (article avec la categorie )
     */

    $query->the_post();

    // on parse le titre
    $title = get_the_title();
    $title_arr = explode(" ", $title);
    $slug = basename( get_permalink() );

    // Background image
    $bg_img = buildBgImg(get_the_post_thumbnail_url());

    // lien pour éditer l'article
    $ifeditlink = (current_user_can('edit_posts')) ? '<a class="edit_post_link" href="'.get_edit_post_link(get_the_ID()).'">'.__('Éditer', 'ccnbtc').'</a>' : '';

    $html = '
        <section id="post__'.$slug.'" class="row section bg-yellow" data-title="'.$title.'" data-index="'.$compteur.'" '.$bg_img.'>
        '.$ifeditlink.'
            <div class="col-lg-12 d-flex flex-col">
    ';

    // content
    $my_content = apply_filters('the_content', get_the_content());

    // on ajoute les gouttes
    $images_svg = '<img class="goutte goutte_rouge" src="'.get_template_directory_uri().'/img/goutte rouge.svg"/>';
    $images_svg .= '<img class="goutte goutte_jaune" src="'.get_template_directory_uri().'/img/goutte verte.svg"/>';
    $images_svg .= '<img class="goutte goutte_bleu_clair" src="'.get_template_directory_uri().'/img/goutte bleu clair.svg"/>';
    $images_svg .= '<img class="goutte goutte_rouge_petite" src="'.get_template_directory_uri().'/img/goutte rouge petite.svg"/>';
    $images_svg .= '<img class="goutte goutte_bleu_fonce" src="'.get_template_directory_uri().'/img/goutte bleu fonce.svg"/>';    

    $html .= '<h2 class="text-center title mb-auto">
                <span class="title_first_part">' . implode(' ', array_slice($title_arr, 0, count($title_arr)-1)) . '</span>
                <span class="title_second_part">' . $title_arr[count($title_arr)-1] . '</h2>';
    $html .= '' . do_shortcode($my_content) . '';
    $html .= '
            </div>'.$images_svg.'
        </section>';

    return $html;
}



function render_HTML_title($title_orig, $mise_en_page, $bg_color, $titre_position, $titre_style) {
    /**
     * Renders the HTML for the titles
     */

    
    $title_br = implode("<br>", explode('§', $title_orig));
    
    // on split le titre en 2 si besoin (trop long ou présence d'un '§')
    $title1 = $title_orig; $title2 = $title_orig;
    if ((strlen($title_orig) > 6 && $mise_en_page != 'texte-centre') || strpos($title_orig, '§') !== false) {
        if (strpos($title_orig, ' ') !== false || strpos($title_orig, '§') !== false) {
            $arr = explode('§', $title_orig);
            if (count($arr) < 2) $arr = explode(' ', $title_orig);
            $title1 = implode(' ', array_slice($arr, 0, count($arr)-1)); $title2 = $arr[count($arr)-1];
        } 
    }

    $title_black = ($bg_color == 'blanc') ? ' has-text-color has-noir-color' : '';

    // render title
    $ifmtauto = ($mise_en_page == 'texte-centre') ? ' mt-auto' : 'col-md-4' ;
    $title_align = ($titre_position == 'titre-centre') ? 'text-center' : 'text-left';
    $title_align = ($titre_position == 'titre-droite') ? 'text-right': $title_align;
    $html_title = '<div class="col-sm-12 double_title '.$ifmtauto.'">
                        <h2 class="w-100 '.$title_align.' point '.$title_black.'">' . $title1 . '</h2>
                        <h2 class="w-100 '.$title_align.' point hollow '.$title_black.'">' . $title2 . '</h2>
                    </div>';
    if ($titre_style == 'titre-simple' || $mise_en_page == "texte-centre" && strpos($title_orig, '§') !== false) $html_title = '<h2 class="w-100 col-lg-12 '.$title_align.' mt-auto">' . $title_br . '</h2>';
    if ($title_orig == '') $html_title = "";

    return $html_title;
}

?>