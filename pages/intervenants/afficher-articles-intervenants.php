<?php

require_once(get_template_directory() . '/lib/php/lib.php');

function render_HTML_intervenant($categorie, $query, $compteur) {
    /**
     * Renvoie le rendu HTML d'un article de la catégorie "programme" pour la page programme
     */
    $query->the_post();
    $posttags = get_the_tags();
    $myclasses = '';
    if ($posttags) {
        foreach ($posttags as $tag) $myclasses .= $tag->name.' ';
    }

    // Post title
    $title_orig = get_the_title();
    $title = implode("<br>", explode('§', $title_orig));

    // Featured image
    $img_html = buildBgImg(get_the_post_thumbnail_url());
    $img_square = '<div class="d-none d-md-block col-md-4 img_intervenant" '.$img_html.'"></div>';

    // lien pour éditer l'article
    $ifeditlink = (current_user_can('edit_posts')) ? '<a class="edit_post_link" href="'.get_edit_post_link(get_the_ID()).'">Éditer</a>' : '';

    $offset = 0;//random_int(1,6);
    $html = '
        <section class="row section translate-appear '.$myclasses.'" 
            style="position:relative; left:'.(($compteur % 2 == 0) ? -1 * $offset : $offset).'rem;border-radius:'.(($compteur % 2 == 0) ? '0 7rem' : '7rem 0' ).';"
            data-title="'.str_replace('§', ' ', $title_orig).'" 
            data-index="'.$compteur.'">
    '.$ifeditlink;

    // on parse le titre
    $title = get_the_title();
    $title_arr = explode(" ", $title);

    if ($compteur % 2 == 0) $html .= $img_square;
    
    $html .= '<div class="col-md-8 d-flex flex-column justify-content-center intervenant_content">';
    $html .= '<div class="d-md-none w-50 img_intervenant_mobile" '.$img_html.'></div>';
    $html .= '<h2 class="text-center title">'.$title.'</h2>';
    $html .= '<p>' . do_shortcode(get_the_content()) . '</p>';
    $html .= '</div>';
    
    if ($compteur % 2 == 1) $html .= $img_square;

    $html .= '</section>';

    return $html;
}

?>