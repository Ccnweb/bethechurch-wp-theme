<?php 
get_header();
wp_enqueue_style('ccnbtc-festival-style');
wp_enqueue_style('ccnbtc-intervenants-style');
?>

    <!-- Page Content -->
    <div class="container-fluid h-100 page_intervenants mt-5" id="fullpage">
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
    <div class="fleche_slide_suivant has-text-color has-noir-color"><i class="fas fa-chevron-down"></i></div>


    <svg width="0" height="0">
      <defs>
        <clipPath id="myCurve" clipPathUnits="objectBoundingBox">
          <path d="M 1,0
									L 1,1
									L 0,1
									L 0,0
                  C .1 .3 .5 .23, 0.8 0
                  L 1, 0
									Z" />
        </clipPath>
      </defs>
    </svg>

<?php get_footer(); ?>
