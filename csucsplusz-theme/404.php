<?php
/**
 * 404 template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<main class="bg-sky-200">
    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'csucsplusz-theme' ); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e( 'It looks like nothing was found at this location.', 'csucsplusz-theme' ); ?></p>
            <?php
            get_search_form();
            ?>
        </div>
    </section>
</main>

<?php
get_footer();
