<?php
/**
 * Template Name: Page d'Accueil avec Menu
 * Description: Template personnalisé pour la page d'accueil avec un menu spécial
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

        <!-- Contenu de la page -->
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

        <!-- Section de contenu supplémentaire -->
        <section class="homepage-content-section">
            <div class="av-container">
                <div class="row">
                    <div class="col-12">
                        <div class="welcome-message">
                            <h2>Bienvenue sur notre site</h2>
                            <p>Découvrez nos services et explorez notre site grâce au menu ci-dessus.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
</div>

<?php
get_sidebar();
get_footer(); 