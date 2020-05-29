<?php
/**
 * Template Name: Site en construction
 */


setlocale(LC_ALL, 'fr');
// get_header();
wp_enqueue_style( 'ccnbtc-festival-style');
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

<style>
  @import url('https://fonts.googleapis.com/css2?family=Caveat+Brush&display=swap');
  body {padding: 0; margin: 0;}
  main {
    background: center / cover no-repeat url(<?php echo get_template_directory_uri() . '/img/work-in-progress.jpg'; ?>);
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; 
  }

  h3 {
    display: inline-block;
    background: black;
    color: white;
    line-height: 2rem;
    padding: 4px 7px;
    font-family: monospace;
  }
</style>

<?php get_footer(); ?>