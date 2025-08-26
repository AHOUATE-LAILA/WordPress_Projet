<?php
/**
 * Template pour la page d'accueil
 *
 * @package Websy
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <!-- Menu personnalisé de la page d'accueil -->
        <section class="homepage-menu-section">
            <div class="av-container">
                <div class="row">
                    <div class="col-12">
                        <div class="homepage-menu-wrapper">
                            <?php do_action('websy_homepage_menu'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contenu principal de la page d'accueil -->
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'websy' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <article class="no-results not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e( 'Rien trouvé', 'websy' ); ?></h1>
                </header>

                <div class="page-content">
                    <p><?php esc_html_e( 'Il semble qu\'il n\'y ait rien ici. Peut-être essayez un lien ci-dessous ou une recherche ?', 'websy' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            </article>
        <?php endif; ?>

    </main>
</div>

<?php
get_sidebar();
get_footer(); 