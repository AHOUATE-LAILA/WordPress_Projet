<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package NewsCard
 */

get_header(); ?>
<div id="content" class="site-content">
	<div class="container">
		<div class="row justify-content-center site-content-row">
			<div id="primary" class="content-area<?php echo esc_attr(newscard_layout_primary()); ?>">
				<main id="main" class="site-main">
					<div class="type-page">
						<div class="error-404 not-found">
							<header class="entry-header">
								<h1 class="entry-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'newscard' ); ?></h1>
							</header><!-- .entry-header -->

							<div class="page-content">
								<p><?php esc_html_e( 'It looks like nothing was found at this location. May be please check the URL for typing errors or start a new search to find the page you are looking for.', 'newscard' ); ?></p>

								<?php get_search_form(); ?>

							</div><!-- .page-content -->
						</div><!-- .error-404 -->
					</div><!-- .type-page -->
				</main><!-- #main -->
			</div><!-- #primary -->
		</div><!-- row -->
	</div><!-- .container -->
</div><!-- #content .site-content-->
<?php get_footer();