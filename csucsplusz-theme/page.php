<?php
/**
 * Page template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="bg-sky-200">
    <?php
    while ( have_posts() ) {
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <?php
                remove_filter('the_content', 'wptexturize');
                the_content();
                wp_link_pages( array(
                    'before' => '<div class="page-links">',
                    'after'  => '</div>',
                ) );
                ?>
            </div>
        </article>

        <?php
        if ( comments_open() || get_comments_number() ) {
            comments_template();
        }
    }
    ?>
</main>

<?php
get_footer();
