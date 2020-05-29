<?php
/**
 * Template Name: Site en construction
 */


setlocale(LC_ALL, 'fr');
// get_header();
wp_enqueue_style( 'ccnbtc-festival-style');
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Le site du festival Be The Church ! Du <?php echo $festival_date_from; ?> au <?php echo $festival_date_to; ?>">
    <meta name="author" content="Communauté du Chemin Neuf">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico">
    
    <title><?php bloginfo('name'); ?></title>
    
    <?php wp_head(); ?>
  </head>

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
    :root {
        --green: #31D2AE;
        --red: #EA5E6B;
        --yellow: #FCC300;
        --blue-klein: #242148;
        --blue-light: #35ADB7; /*#34ACB6;*/
    }
  body {padding: 0; margin: 0;}
  main {
    background: center / cover no-repeat url(<?php echo get_the_post_thumbnail_url(); ?>); /* get_template_directory_uri() . '/img/work-in-progress.jpg' */
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center; 
  }

  h3 {
      max-width: 1000px;
  }

  h3 > span {
    background: black;
    color: white;
    line-height: 2rem;
    padding: 4px 7px;
    font-family: monospace;
  }

    .bg-green, .bg-vert, .has-vert-background-color {background: var(--green)}
    .bg-red, .bg-rouge, .has-rouge-background-color {background: var(--red)}
    .bg-yellow, .bg-jaune, .has-jaune-background-color {background: var(--yellow)}
    .bg-blue-klein, .bg-bleu-klein, .has-bleu-klein-background-color {background: var(--blue-klein) !important}
    .bg-blue-light, .bg-bleu-clair, .has-bleu-clair-background-color {background: var(--blue-light)}

    .has-text-color.has-blanc-color, .has-text-color.has-blanc-color a, .txt-white, .txt-blanc {color:white}
    .has-text-color.has-noir-color, .txt-black, .txt-noir {color:black}
    .has-text-color.has-vert-color, .txt-green, .txt-vert {color:var(--green)}
    .has-text-color.has-rouge-color, .txt-red, .txt-rouge {color:var(--red)}
    .has-text-color.has-jaune-color, .txt-yellow, .txt-jaune {color:var(--yellow)}
    .has-text-color.has-bleu-klein-color, .txt-blue-klein, .txt-bleu-klein {color:var(--blue-klein)}
    .has-text-color.has-bleu-clair-color, .txt-blue-light, .txt-bleu-clair {color:var(--blue-ligth)}

    
</style>

<?php get_footer(); ?>