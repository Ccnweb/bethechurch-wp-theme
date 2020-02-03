<!DOCTYPE html>
<html <?php language_attributes(); ?>>

  <?php
    $site_options = get_option('btc-config');
    $festival_date_from = ccn_date_format('j F', strtotime($site_options['date_festival_from']), pll_current_language());
    $festival_date_to = ccn_date_format('j F Y', strtotime($site_options['date_festival_to']), pll_current_language());
  ?>

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Le site du festival Be The Church ! Du <?php echo $festival_date_from; ?> au <?php echo $festival_date_to; ?>">
    <meta name="author" content="Communauté du Chemin Neuf">

    <link rel="profile" href="http://gmpg.org/xfn/11"> <!-- source: http://xmlns.com/foaf/spec/ -->
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico">

    <title><?php bloginfo('name'); ?></title>

    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>
    <script>console.log('<?php echo json_encode(get_option('btc-config')); ?>');</script>

  <?php require "components/nav.php"; ?>
  <?php require "components/translation_ui/translation.php"; ?>
