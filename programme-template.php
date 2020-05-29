<?php 
/**
 * Template Name: Page de programme COVID
 */


setlocale(LC_ALL, 'fr');
get_header();
wp_enqueue_style('ccnbtc-programme-covid');
$root_url = get_template_directory_uri();
?>

    <!-- Page Content -->
    <main>
      <!-- <div class="row">
        <div class="col-lg-12 text-center"> -->
          
          <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <!-- <h1><?php the_title(); ?></h1> -->
            <?php echo apply_filters( 'the_content', $post->post_content ); ?>
          <?php endwhile;  endif;?>
          
        <!-- </div>
      </div> -->
    </main>

    <!-- Flèche vers le bas pour changer de slide -->
    <div class="fleche_slide_suivant"><i class="fas fa-chevron-down"></i></div>

    <img class="goutte goutte_bleu_fonce" src="<?php echo $root_url; ?>/img/goutte bleu fonce.svg" style="top: 0rem; left: 0px;">
    <img class="goutte goutte_rouge_petite" src="<?php echo $root_url; ?>/img/goutte rouge petite.svg" style="top: 21rem; left: 72.7969px;">
    <img class="goutte goutte_rouge" src="<?php echo $root_url; ?>/img/goutte rouge.svg" style="top: 33rem; right: 0">
    <img class="goutte goutte_jaune" src="<?php echo $root_url; ?>/img/goutte verte.svg" style="top: 6rem;right: 14rem;">

<script>
  let featured_image = "<?php echo get_the_post_thumbnail_url(); ?>";
  jQuery(document).ready(function($) {
    $('.image-mise-en-avant').css({
      'background': "center / cover no-repeat url('" + featured_image + "')",
    })
  })
</script>

<?php 
get_footer();
wp_enqueue_script('ccnbtc-programme-covid-script');
?>

<style>
    
</style>