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

if (!function_exists('require_once_all_regex')):
function require_once_all_regex($dir_path, $regex = "") {
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
endif;

/* ==================================== */
/*     ENQUEUE STYLES IN DIR            */
/* ==================================== */
if (!function_exists('enqueue_styles_regex')):
    /**
     * Require once all files in $dir_path that have a filename matching $regex
     * 
     * @param string $dir_path
     * @param string $regex
     */
    function enqueue_styles_regex($dir_path, $regex = "") {    
        if ($regex == "") $regex = "//";
    
        $template_dir = get_template_directory();
        $template_url = get_template_directory_uri();

        foreach (scandir($dir_path) as $filename) {
            $path = $dir_path . '/' . $filename;
            $path_url = str_replace($template_dir, $template_url, $path);
            if ($filename[0] != '.' && is_file($path) && preg_match("/\.css$/i", $path) == 1 && preg_match($regex, $filename) == 1) {
                wp_enqueue_style( 'ccnbtc-'.preg_replace("/\.css$/i", "", $filename).'-style', $path_url, [], filemtime($path), 'all');
            } else if ($filename[0] != '.' && is_dir($path)) {
                enqueue_styles_regex($path, $regex);
            }
        }
    }
    endif;


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


/*
|-----------------------------------------------------------------------
| Sky Date in French by Matt - www.skyminds.net
|-----------------------------------------------------------------------
|
| Returns or echoes the date in French format (dd/mm/YYYY) for WordPress themes.
|
*/
function btc_date_format($format, $timestamp = null, $lang = 'fr', $echo = null) {
    $lang = strtolower($lang);
    if ($lang != 'fr') {
        setlocale(LC_ALL, $lang);
        return strftime($format, $timestamp);
    }
	$param_D = array('', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim');
	$param_l = array('', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
	$param_F = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre');
	$param_M = array('', 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc');
	$return = '';
	if(is_null($timestamp)) { $timestamp = mktime(); }
	for($i = 0, $len = strlen($format); $i < $len; $i++) {
		switch($format[$i]) {
			case '\\' : // fix.slashes
				$i++;
				$return .= isset($format[$i]) ? $format[$i] : '';
				break;
			case 'D' :
				$return .= $param_D[date('N', $timestamp)];
				break;
			case 'l' :
				$return .= $param_l[date('N', $timestamp)];
				break;
			case 'F' :
				$return .= $param_F[date('n', $timestamp)];
				break;
			case 'M' :
				$return .= $param_M[date('n', $timestamp)];
				break;
			default :
				$return .= date($format[$i], $timestamp);
				break;
		}
	}
	if(is_null($echo)) { return $return;} else { echo $return;}
}

?>