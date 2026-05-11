<?php
/**
 * Main template file
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="bg-sky-200">
    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="entry-content">
                    <?php
                    the_content();
                    wp_link_pages( array(
                        'before' => '<div class="page-links">',
                        'after'  => '</div>',
                    ) );
                    ?>
                </div>
            </article>
            <?php
        }
        
        the_posts_pagination();
    } else {
        ?>
        <section class="no-results not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e( 'Nothing here', 'csucsplusz-theme' ); ?></h1>
            </header>
            <div class="page-content">
                <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'csucsplusz-theme' ); ?></p>
            </div>
        </section>
        <?php
    }
    ?>
</main>

<?php
get_footer();
