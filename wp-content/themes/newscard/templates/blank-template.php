<?php
/**
 * Template Name: Blank Template
 *
 * Blank Template for the page builder
 *
 * @package Theme Horse
 * @subpackage NewsCard
 * @since NewsCard 1.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class('theme-body'); ?>>

<main id="main" class="">
	<?php
	if ( have_posts() ) :
		/* Start the Loop */
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile; 
		/* End of the loop. */
	endif;
	?>
</main><!-- #main -->

<?php wp_footer(); ?>
</body>
</html>