<?php

require_once(get_template_directory() . '/lib/php/lib.php');

function render_HTML_programme($categorie, $query, $compteur) {
    /**
     * Renvoie le rendu HTML d'un article de la catégorie "intervenant" pour la page intervenants
     */
    
    
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
        foreach ($posttags as $tag) {
            // bg_image
            if (preg_match("/^fond\-(.*)$/i", $tag->name, $matches)) {
                if (count($matches) > 1) $bg_color = $matches[1];
            }
        }
    }

    // lien pour éditer l'article
    $ifeditlink = (current_user_can('edit_posts')) ? '<a class="edit_post_link" href="'.get_edit_post_link(get_the_ID()).'">Éditer</a>' : '';


    // Render HTML

    // render title
    $html_title = '<div class="row w-100"><h2 class="col-lg-12 text-right programme">' . $title . '</h2></div>';


    // Add everything
    $html = '
        <section id="post__'.$slug.'" class="row section bg-'.$bg_color.'" data-index="'.$compteur.'" '.$bg_img.'>
            '.$ifeditlink.'
            <div class="col-lg-12 ">
    ';
    $html .= $html_title;
    $html .= '<div class="row w-100">
                <div class="col-lg-12 d-flex flex-column justify-content-start programme_section">' 
                    . do_shortcode(get_the_content()) . 
                '</div>';    
    
    $html .= '</div>
            </div>
        </section>';

    return $html;

}


// Rendu HTML du slide d'accueil del a page programme
function render_HTML_programme_homepage($categorie, $query, $compteur) {
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
    $titre_style = 'titre-double'; // 'titre-simple' ou 'titre-double' avec hollow title (titre + son "ombre")

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
            // style titre
            if(preg_match("/^titre\-(simple|double)$/i", $tag->name, $matches)) {
                $titre_style = $matches[0];
            }
        }
    }
    
    $flex_type = "d-flex flex-row";
    if ($mise_en_page == "texte-centre") $flex_type = "";

    // lien pour éditer l'article
    $ifeditlink = (current_user_can('edit_posts')) ? '<a class="edit_post_link" href="'.get_edit_post_link(get_the_ID()).'">Éditer</a>' : '';


    // Render HTML

    // render title
    $html_title = '<div class="double_title mt-auto">
                        <h2 class="text-center point">' . $title . '</h2>
                        <h2 class="text-center point hollow">' . $title . '</h2>
                    </div>';
    if ($titre_style == 'titre-simple' || $mise_en_page == "texte-centre" && strpos($title_orig, '§') !== false) $html_title = '<h2 class="text-center mt-auto">' . $title . '</h2>';
    if ($title == '') $html_title = "";


    // Add everything
    $html = '
        <section id="post__'.$slug.'" class="row section bg-'.$bg_color.'" data-index="'.$compteur.'" '.$bg_img.'>
            '.$ifeditlink.'
            <div class="col-lg-12 '.$flex_type.'">
    ';
    if (in_array($mise_en_page, array('texte-droite', 'texte-centre'))) $html .= $html_title;
    $html .= '<div class="slide_text w-100 mt-auto">' . do_shortcode(get_the_content()) . '</div>';
    if ($mise_en_page == 'texte-gauche') $html .= $html_title;
    $html .= '
            </div>
        </section>';

    return $html;
}

?>