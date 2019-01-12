<?php 

/**
 * Quelques fonctions utiles pour notre thème
 */

/* ==================================== */
/*              RENDU HTML              */
/* ==================================== */

function buildBgImg($img_url, $options = array()) {
    /**
     * Renvoie une string pour insérer une image en bg
     */

    $default_options = array(
        'style_tag' => true,
        'size' => 'cover', // background-size: cover ou contain etc.
        'position' => 'center', // background-position: center etc.
    );
    $options = assign_default($default_options, $options);

    $img_style = ($img_url) ?  'background:url(\''.$img_url.'\');background-position:'.$options['position'].';background-size:'.$options['size'].';' : '';

    // on wrap éventuellement aussi style=""
    if ($options['style_tag']) $img_style = 'style="' . $img_style . '"';

    return $img_style;
}

/* ==================================== */
/*     CHARGEMENT DE FICHIERS PHP       */
/* ==================================== */

function require_once_all_regex($dir_path, $regex) {
    /**
     * Require once all files in $dir_path that have a filename matching $regex
     * 
     * @param string $dir_path
     * @param string $regex
     */

    if ($regex == "") $regex = "//";

    foreach (scandir($dir_path) as $filename) {
        $path = $dir_path . '/' . $filename;
        if ($filename[0] != '.' && is_file($path) && preg_match("/\.php$/i", $path) == 1 && preg_match($regex, $filename) == 1) {
            require_once $path;
        } else if ($filename[0] != '.' && is_dir($path)) {
            require_once_all_regex($path, $regex);
        }
    }
}

/* ==================================== */
/*     LOW-LEVEL USEFUL FUNCTIONS       */
/* ==================================== */

if (!function_exists('assign_default')):
function assign_default($el1, $el2) {
    // It assigns values of $el2 to $el1.
    // $el1 and $el2 are assoc arrays
    
    foreach ($el2 as $k2 => $v2) {
        $el1[$k2] = $v2;
    }
    return $el1;
}
endif;

?>