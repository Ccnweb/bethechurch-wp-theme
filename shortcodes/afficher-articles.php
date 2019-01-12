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
        // to debug via REST : http://www.bethechurch.fr/wp-json/wp/v2/posts?categories=47
        $query_args = array(
            'category_name' => $atts['categorie'],
            'post_status'   => 'publish',
            'lang'          => 'en,'.pll_current_language(),
            'meta_key'      => 'ccnbtc_post_order',
            'orderby'       => 'meta_value_num', // nécessaire lorsque la meta_key est numérique
            'order'         => 'ASC',
            'limit'         => 100,
        );
        $query = new WP_Query( $query_args );
        
        // == 4. == en construit le HTML
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
            $html .= '<section class="row section" data-index="1">
                <div class="col-lg-12 bg-green">Aucun article de la catégorie '.$atts['categorie']." n'a été trouvé, sorry :(<br>".$query->request."</div>
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
    $title = implode("<br>", explode('§', $title_orig));

    // Background image
    $bg_img = buildBgImg(get_the_post_thumbnail_url());
    // Background color
    $bg_color = 'green';
    

    // computed properties
    if ($posttags) {
        foreach($posttags as $tag) {
            // bg_image
            if(preg_match("/^fond\-(.*)$/i", $tag->name, $matches)) {
                if (count($matches) > 1) $bg_color = $matches[1];
            }
            // mise en page
            if(preg_match("/^texte\-(gauche|droite)$/i", $tag->name, $matches)) {
                $mise_en_page = $matches[0];
            }
        }
    }
    
    $flex_type = "d-flex flex-row";
    if ($mise_en_page == "texte-centre") $flex_type = "";

    $title_black = ($bg_color == 'blanc') ? ' has-text-color has-noir-color' : '';


    // Render HTML

    // render title
    $html_title = '<div class="double_title">
                        <h2 class="text-center point '.$title_black.'">' . $title . '</h2>
                        <h2 class="text-center point hollow '.$title_black.'">' . $title . '</h2>
                    </div>';
    if ($mise_en_page == "texte-centre" && strpos($title_orig, '§') !== false) $html_title = '<h2 class="text-center point">' . $title . '</h2>';
    if ($title == '') $html_title = "";

    // special case if no title
    $special_texte_no_title = ($title == '') ? "align-self-start notitle-text-left": '';

    // flex-grow = 2 if !texte-centre
    $ifflexgrow = ($mise_en_page == 'texte-centre') ? '' : ' flex-grow-2';


    // Add everything
    $html = '
        <section id="post__'.$slug.'" class="row section" data-index="'.$compteur.'">
            <div class="col-lg-12 '.$flex_type.' bg-'.$bg_color.'" '.$bg_img.'>
    ';
    if (in_array($mise_en_page, array('texte-droite', 'texte-centre'))) $html .= $html_title;
    $html .= '<div class="slide_text '.$special_texte_no_title.$ifflexgrow.'">' . do_shortcode(get_the_content()) . '</div>';
    if ($mise_en_page == 'texte-gauche') $html .= $html_title;
    $html .= '
            </div>
        </section>';

    return $html;
}




function render_HTML_homepage($categorie, $query, $compteur) {
    /**
     * This renders the homepage HTML first slide (article avec la categorie )
     */

    $html = '
        <section class="row section" data-index="'.$compteur.'">
            <div class="col-lg-12 d-flex flex-col bg-green">
    ';
    $query->the_post();

    // on parse le titre
    $title = get_the_title();
    $title_arr = explode(" ", $title);

    $html .= '<h2 class="text-center title">
                <span class="title_first_part">' . implode(' ', array_slice($title_arr, 0, count($title_arr)-1)) . '</span>
                <span class="title_second_part">' . $title_arr[count($title_arr)-1] . '</h2>';
    $html .= '' . do_shortcode(get_the_content()) . '';
    $html .= '
            </div>
        </section>';

    return $html;
}


?>