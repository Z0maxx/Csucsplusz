<?php
/**
 * Footer template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
    <footer class="flex justify-center py-2 bg-olive-200">
        <div class="flex w-9/10 lg:w-4/5 lg:min-w-[63rem] gap-4 flex-col sm:flex-row">
            <div class="text-slate-600">
                &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
            </div>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
