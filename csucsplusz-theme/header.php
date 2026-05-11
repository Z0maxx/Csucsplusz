<?php
/**
 * Header template
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <header class="sticky top-0 z-50 flex justify-center font-semibold text-white bg-sky-500 lg:h-18 text-nowrap">
        <input type="checkbox" id="menu-checkbox" class="hidden">
        <label id="blocker" for="menu-checkbox" class="absolute top-full left-0 z-30 block w-full h-[calc(100vh-100%)] transition-opacity opacity-0 pointer-events-none backdrop-blur-xs"></label>
        <label id="menu-button" for="menu-checkbox" class="absolute z-50 overflow-hidden rotate-90 bg-blue-800 rounded cursor-pointer select-none size-8 right-2 top-2 lg:hidden text-cyan-300" aria-hidden="true">
            <div class="relative">
                <span class="absolute block text-center size-8 transition-all duration-200 will-change-[left] top-0 flex gap-1.5 items-center justify-center" id="burger">
                    <div class="w-0.5 h-5 bg-cyan-300 rounded-sm"></div>
                    <div class="w-0.5 h-5 bg-cyan-300 rounded-sm"></div>
                    <div class="w-0.5 h-5 bg-cyan-300 rounded-sm"></div>
                </span>
                <span class="absolute block text-center size-8 transition-all duration-200 text-3xl font-bold will-change-[left] top-0 flex items-center justify-center" id="x">
                    <svg class="fill-cyan-300 size-4" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 460.775 460.775" xml:space="preserve">
                        <path d="M285.08,230.397L456.218,59.27c6.076-6.077,6.076-15.911,0-21.986L423.511,4.565c-2.913-2.911-6.866-4.55-10.992-4.55  c-4.127,0-8.08,1.639-10.993,4.55l-171.138,171.14L59.25,4.565c-2.913-2.911-6.866-4.55-10.993-4.55  c-4.126,0-8.08,1.639-10.992,4.55L4.558,37.284c-6.077,6.075-6.077,15.909,0,21.986l171.138,171.128L4.575,401.505  c-6.074,6.077-6.074,15.911,0,21.986l32.709,32.719c2.911,2.911,6.865,4.55,10.992,4.55c4.127,0,8.08-1.639,10.994-4.55  l171.117-171.12l171.118,171.12c2.913,2.911,6.866,4.55,10.993,4.55c4.128,0,8.081-1.639,10.992-4.55l32.709-32.719  c6.074-6.075,6.074-15.909,0-21.986L285.08,230.397z" />
                    </svg>
                </span>
            </div>
        </label>
        <nav class="flex flex-col justify-between items-center w-4/5 gap-4 lg:gap-1 lg:flex-row lg:min-w-[63rem] z-40">
            <div class="pt-2 lg:pt-0">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="block">
                    <img class="h-16 overflow-hidden rounded-tr rounded-bl rounded-tl-2xl rounded-br-2xl" src="<?php echo esc_url( csucsplusz_get_logo_url() ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                </a>
            </div>
            <div id="menu-wrap" class="absolute top-0 left-0 w-full h-screen overflow-x-hidden pointer-events-none lg:overflow-x-visible lg:h-auto lg:w-auto lg:static">
                <div class="relative w-full h-full">
                    <div id="menu" class="flex-wrap lg:h-full absolute will-change-[left] left-0 top-35 flex flex-col items-center pointer-events-auto justify-start w-full text-lg transition-all lg:static duration-200 bg-sky-600 lg:bg-sky-500 lg:justify-center lg:flex-row lg:w-auto divide-y-2 lg:divide-y-0 lg:divide-x-2 divide-cyan-400 divide-solid">
                        <?php
                        $menu_items = array(
                            array( 'url' => home_url( '/kezdolap-1' ), 'label' => esc_html__( 'Kezdőlap', 'csucsplusz-theme' ) ),
                            array( 'url' => home_url( '/tanfolyami-adatok-1' ), 'label' => esc_html__( 'Tanfolyami Adatok', 'csucsplusz-theme' ) ),
                            array( 'url' => home_url( '/irasos-tajekoztato-1' ), 'label' => esc_html__( 'Írásos Tájékoztató', 'csucsplusz-theme' ) ),
                            array( 'url' => home_url( '/szerzodes-minta-1' ), 'label' => esc_html__( 'Szerződés minta', 'csucsplusz-theme' ) ),
                            array( 'url' => home_url( '/gdpr-1' ), 'label' => esc_html__( 'GDPR', 'csucsplusz-theme' ) ),
                            array( 'url' => home_url( '/statisztika-1' ), 'label' => esc_html__( 'Statisztika', 'csucsplusz-theme' ) ),
                        );

                        foreach ( $menu_items as $item ) {
                            $is_current = ( $item['url'] === get_permalink() || ( is_home() && $item['url'] === home_url( '/' ) ) );
                            $class = $is_current ? 'bg-sky-800' : '';
                            echo '<a href="' . esc_url( $item['url'] ) . '" class="w-full px-1.5 py-2 transition lg:py-1 lg:w-auto hover:bg-sky-400 ' . esc_attr( $class ) . '">' . esc_html( $item['label'] ) . '</a>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo esc_url( home_url( '/jelentkezes-1' ) ); ?>" class="px-3.5 py-1.5 mb-2 text-xl transition border-2 rounded-full border-emerald-300 bg-emerald-600 hover:scale-105 lg:mb-0 hover:bg-emerald-500 focus:scale-105 focus:bg-emerald-500">
                <?php esc_html_e( 'Online Jelentkezés', 'csucsplusz-theme' ); ?>
            </a>
        </nav>
    </header>
