<?php
/**
 * Archive template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="bg-sky-200">
    <header class="page-header">
        <h1 class="page-title">
            <?php
            if ( is_category() ) {
                single_cat_title();
            } elseif ( is_tag() ) {
                single_tag_title();
            } elseif ( is_author() ) {
                the_author();
            } elseif ( is_date() ) {
                echo esc_html( get_the_archive_title() );
            } else {
                esc_html_e( 'Archives', 'csucsplusz-theme' );
            }
            ?>
        </h1>
        <?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>
    </header>

    <?php
    if ( have_posts() ) {
        while ( have_posts() ) {
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h2 class="entry-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                </header>
                <div class="entry-summary">
                    <?php the_excerpt(); ?>
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
                <p><?php esc_html_e( 'Sorry, but nothing matched your search terms.', 'csucsplusz-theme' ); ?></p>
            </div>
        </section>
        <?php
    }
    ?>
</main>

<?php
get_footer();
