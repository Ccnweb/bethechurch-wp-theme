<?php 
get_header();
?>

    <!-- Page Content -->
    <div class="container-fluid h-100" id="fullpage">
      <!-- <div class="row">
        <div class="col-lg-12 text-center"> -->
          
          <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <!-- <h1><?php the_title(); ?></h1> -->
            <?php echo apply_filters( 'the_content', $post->post_content ); ?>
          <?php endwhile;  endif;?>
          
        <!-- </div>
      </div> -->
    </div>


    <!-- Flèche vers le bas pour changer de slide -->
    <div class="fleche_slide_suivant"><i class="fas fa-chevron-down"></i></div>

<?php get_footer(); ?>
