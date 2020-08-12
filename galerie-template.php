<?php 
/**
 * Template Name: Page de Galerie Photo
 */


setlocale(LC_ALL, 'fr');
get_header();
wp_enqueue_style('ccnbtc-galerie');
wp_enqueue_style('fancybox', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css', [], '3.5.7', 'all');
wp_enqueue_script('fancybox-script', 'https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js', ['jquery'], '3.5.7', true);
wp_enqueue_script('ccnbtc-galerie-script');
$root_url = get_template_directory_uri();

$custom_classes = '';
if ($posttags) {
    foreach($posttags as $tag) {
        $custom_classes .= $tag->name . ' ';
    }
}
?>

<!-- Page Content -->
<main class="<?php echo $custom_classes; ?>">
    <!-- <div class="row">
    <div class="col-lg-12 text-center"> -->
        
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <!-- <h1><?php the_title(); ?></h1> -->
        <?php echo apply_filters( 'the_content', $post->post_content ); ?>
        <?php endwhile;  endif;?>
        
    <!-- </div>
    </div> -->
</main>

<?php 
get_footer();
?>