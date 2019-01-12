<?php 

require_once(get_template_directory() . '/lib/php/lib.php');

// afficher-liste-carres

function ccnbtc_shortcode_afficher_liste_carres() {

    $shortcode_fun = function($atts = [], $content = null, $tag = '') {
        
        // == 1. == on normalise les attributs
        $atts = array_change_key_case((array)$atts, CASE_LOWER);

        // == 2. == on gère les valeurs par défaut des attributs
        $atts = shortcode_atts(
            array(
                'categorie' => 'unknown',
            ), $atts, 'afficher-liste-carres' );


        // == 3. == on récupère les articles de la catégorie
        $query_args = array(
            'category_name' => $atts['categorie'],
            'meta_key' => 'ccnbtc_post_order',
            'orderby' => 'meta_value_num', // nécessaire lorsque la meta_key est numérique
            'order' => 'ASC'
        );
        $query = new WP_Query( $query_args );
        
        // == 4. == en construit le HTML
        $html = '<div class="d-flex flex-row carre_container">';
        $compteur = 1;

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $html .= render_HTML_carre($atts['categorie'], $query, $compteur);
                $compteur++;
            }
            /* Restore original Post Data */
            wp_reset_postdata();

        } else {
            // no posts found
            $html .= '<div>Aucun article de la catégorie '.$atts['categorie'].' n\'a été trouvé :(</div>';
        }

        $html .= "</div>";
        
        return $html;
    };

    add_shortcode( 'afficher-liste-carres', $shortcode_fun);
};

add_action('init', 'ccnbtc_shortcode_afficher_liste_carres');






function render_HTML_carre($categorie, $query, $compteur) {

    $query->the_post();
    $posttags = get_the_tags();
    $slug = basename( get_permalink() );

    // title
    $title = get_the_title();
    if (strpos($title, '§') !== false) {
        $title = implode("<br>", explode('§', $title));
    } else {
        $name_limit = 1;
        $arr = explode(' ', $title);
        while (count($arr) > $name_limit && strlen($arr[count($arr)-$name_limit-1]) < 3) $name_limit++;
        $title = '<span class="has-text-color has-vert-color">' . implode(' ', array_slice($arr, 0, count($arr) - $name_limit)) . '</span>
                    <br><span>' . implode(' ', array_slice($arr, count($arr) - $name_limit)) . '</span>';
    }

    // Featured image
    $img_html = buildBgImg(get_the_post_thumbnail_url());


    // Render HTML
    $html = '
        <div class="d-flex align-items-end carre" '.$img_html.'>
            <div class="d-flex justify-content-center align-items-center gradient">
                <h4 class="text-center">'.$title.'</h4>
            </div>
        </div>';

    return $html;
}










/* function test_shortcode() {
    $shortcode_fun = function($atts = [], $content = null, $tag = '') {
        return "<h1>Lodate Dio voi tutti servi suoi !</h1>";
    };
    add_shortcode( 'test-shortcode', $shortcode_fun);
};
add_action('init', 'test_shortcode'); */
?>