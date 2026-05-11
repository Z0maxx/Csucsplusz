<?php

/**
 * Csúcsplusz Autósiskola Theme Functions
 */

if (! defined('ABSPATH')) {
  exit;
}

/**
 * Set the content width
 */
if (! isset($content_width)) {
  $content_width = 1200;
}

/**
 * Theme Setup
 */
function csucsplusz_theme_setup()
{
  // Add theme support
  add_theme_support('title-tag');
  add_theme_support('post-thumbnails');
  add_theme_support('html5', array(
    'search-form',
    'comment-form',
    'comment-list',
    'gallery',
    'caption',
    'style',
    'script',
  ));

  // Register menus
  register_nav_menus(array(
    'primary' => esc_html__('Primary Menu', 'csucsplusz-theme'),
  ));

  // Add custom image sizes if needed
  add_image_size('banner', 1920, 500, true);
}
add_action('after_setup_theme', 'csucsplusz_theme_setup');

/**
 * Enqueue Styles and Scripts
 */
function csucsplusz_enqueue_assets()
{
  // Enqueue Tailwind CSS
  wp_enqueue_style('csucsplusz-tailwind', get_template_directory_uri() . '/assets/css/tw.css', array(), '1.0');

  // Enqueue main stylesheet
  wp_enqueue_style('csucsplusz-style', get_stylesheet_uri(), array('csucsplusz-tailwind'), '1.0');

  // Localize script variables
  wp_localize_script('csucsplusz-navigation', 'csucsplusszData', array(
    'templateUrl' => get_template_directory_uri(),
  ));
}
add_action('wp_enqueue_scripts', 'csucsplusz_enqueue_assets');

/**
 * Get current page slug for active menu highlighting
 */
function csucsplusz_get_current_page_slug()
{
  if (is_home() || is_front_page()) {
    return 'home';
  } elseif (is_page()) {
    return get_page_template_slug(get_the_ID());
  } elseif (is_single()) {
    return get_post_type();
  }
  return '';
}

/**
 * Check if menu item should be active
 */
function csucsplusz_is_menu_active($page_slug)
{
  return $page_slug === csucsplusz_get_current_page_slug();
}

/**
 * Contact Form 7 Support
 */
function csucsplusz_cf7_customization()
{
  // Custom CSS class for form
  add_filter('wpcf7_form_elements', 'csucsplusz_wpcf7_form_elements');
}
add_action('wp_footer', 'csucsplusz_cf7_customization');

function csucsplusz_wpcf7_form_elements($form)
{
  // Add custom classes to form elements if needed
  return $form;
}

/**
 * Get site logo URL
 */
function csucsplusz_get_logo_url()
{
  return get_template_directory_uri() . '/assets/images/icon.jpg';
}

/**
 * Register widget areas
 */
function csucsplusz_widgets_init()
{
  register_sidebar(array(
    'name'          => esc_html__('Primary Sidebar', 'csucsplusz-theme'),
    'id'            => 'primary-sidebar',
    'description'   => esc_html__('Primary Sidebar', 'csucsplusz-theme'),
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="widget-title">',
    'after_title'   => '</h3>',
  ));
}
add_action('widgets_init', 'csucsplusz_widgets_init');

add_editor_style(get_template_directory_uri() . '/assets/css/tw.css');

function normalize_shortcodes(string $content)
{
  $map = [
    'á' => 'a',
    'é' => 'e',
    'í' => 'i',
    'ó' => 'o',
    'ö' => 'o',
    'ő' => 'o',
    'ú' => 'u',
    'ü' => 'u',
    'ű' => 'u',
    'Á' => 'A',
    'É' => 'E',
    'Í' => 'I',
    'Ó' => 'O',
    'Ö' => 'O',
    'Ő' => 'O',
    'Ú' => 'U',
    'Ü' => 'U',
    'Ű' => 'U',
    ' ' => '_'
  ];

  $csucsplusz_shortcodes = [
    'képzések',
    'képzés indulás',
    'ár',
    'statisztika',
    'oktatók',
    'telefonszám',
    'email',
    'cím',
    'ügyfélfogadási idők',
    'jelentkezési lap'
  ];

  $shortcode_replaced = preg_replace_callback(
    '/\[(.*?)="(.*?)"\]/u',
    function ($matches) use ($map, $csucsplusz_shortcodes) {
      if (!in_array($matches[1], $csucsplusz_shortcodes)) {
        return sprintf(
          '[%s="%s"]',
          $matches[1],
          $matches[2]
        );
      }

      $shortcode = strtr($matches[1], $map);
      $value = str_replace(' ', '_', strtr($matches[2], $map));
      return sprintf(
        '[%s="%s"]',
        $shortcode,
        $value
      );
    },
    $content
  );

  return preg_replace_callback(
    '/\[(.*?)\]/u',
    function ($matches) use ($map, $csucsplusz_shortcodes) {
      if (!in_array($matches[1], $csucsplusz_shortcodes)) {
        return sprintf(
          '[%s]',
          $matches[1]
        );
      }

      $shortcode = strtr($matches[1], $map);
      return sprintf(
        '[%s]',
        $shortcode
      );
    },
    $shortcode_replaced
  );
}
add_filter('the_content', 'normalize_shortcodes', 1);

function get_attr_value($atts)
{
  $map = [
    '"' => '',
    '=' => ''
  ];

  return strtr($atts[0], $map);
}

function trainings_shortcode()
{
  $json = get_option('trainings_json');
  $data = json_decode($json);
  $trainings = $data->trainings;
  $no_training_start_text = $data->noTrainingStartText;
  $content = array_map(function ($training) use ($no_training_start_text) {
    $start_date_text = '';
    if ($training->hasStartDate) {
      if ($training->startDate !== '') {
        $start_date_text = 'következő indulási dátum ' . $training->startDate;
      } else {
        $start_date_text = $no_training_start_text;
      }
    }

    return '<div class="training-row">' . $training->name . ': ' . str_replace('\\n', '<br>', $training->description) . $start_date_text .  '</div>';
  }, $trainings);

  return join('', $content);
}
add_shortcode('kepzesek', 'trainings_shortcode');

function training_start_shortcode($atts)
{
  $option_name = get_attr_value($atts);
  $kepzes = get_option($option_name . '_training_start');
  if (!$kepzes) {
    return esc_html(get_option('no_training_start_text') ?: '');
  }

  return esc_html('indul ' . $kepzes);
}
add_shortcode('kepzes_indulas', 'training_start_shortcode');

function ar_shortcode($atts)
{
  $option_name = get_attr_value($atts);
  $ar = get_option($option_name . '_price');
  if (!$ar) {
    return '---Hiányzó ár: ' . $option_name . '---';
  }

  return esc_html(number_format($ar, 0, ',', '.') . ' Ft');
}
add_shortcode('ar', 'ar_shortcode');

function stat_shortcode()
{
  $json = get_option('stat_json');
  $data = json_decode($json);
  $ako = $data->ako;
  $ako = $ako ? $ako . '%' : 'nincs adat';
  $kk = $data->kk;
  $kk = $kk ? number_format($kk, 0, ',', '.') . ' Ft' : 'nincs adat';
  $vsm = $data->vsm;
  $vsmContent = join('', array_map(function ($statWrap) {
    $statsContent = count($statWrap->stats) > 0 ?
      '<div>' .
      join('', array_map(fn($stat) => '
          <div style="margin-left: 1rem">' .
        $stat->year .
        '-' .
        $stat->month .
        ': ' .
        $stat->percent .
        '%</div>
        ', $statWrap->stats)) .
      '</div>' :
      '<span>nincs adat</span>';

    return '<div><span style="margin-left: 0.5rem">' . strtoupper($statWrap->name) . ': </span>' . $statsContent . '</div>';
  }, $vsm));

  return '
    <div class="stat-wrap">
      <div>ÁKÓ: ' . $ako . '</div>
      <div>KK: ' . $kk . '</div>
      <div>VSM:</div>' .
    $vsmContent .
    '</div>';
}
add_shortcode('statisztika', 'stat_shortcode');

function instructors_shortcode()
{
  $json = get_option('instructors_json');
  $instructors = json_decode($json);
  $chunks = array_chunk($instructors, 2);
  return join('', array_map(fn($chunk) => '
    <div class="instructor-group">' .
    join('', array_map(fn($instructor) => '
        <div class="instructor">
          <span class="instructor-name">' . $instructor->name . '</span>
          <span class="instructor-titles">' . str_replace('\n', '<br>', $instructor->titles) . '</span>
        </div>
      ', $chunk)) .
    '</div>
  ', $chunks));
}
add_shortcode('oktatok', 'instructors_shortcode');

function phone_shortcode()
{
  return get_option('phone_contact');
}
add_shortcode('telefonszam', 'phone_shortcode');

function email_shortcode()
{
  return get_option('email_contact');
}
add_shortcode('email', 'email_shortcode');

function address_shortcode()
{
  return get_option('address_contact');
}
add_shortcode('cim', 'address_shortcode');

function crt_shortcode()
{
  $json = get_option('crt_json');
  $crt = json_decode($json);
  return '
    <table class="crt-table">' .
    join('', array_map(fn($time) => '
        <tr>
          <td class="crt-day">' . $time->day . ':</td>
          <td class="crt-time">' . $time->startTime . '-' . $time->endTime . '</td>
        </tr>
      ', array_filter($crt, fn($time) => $time->startTime && $time->endTime))) .
    '</table>';
}
add_shortcode('ugyfelfogadasi_idok', 'crt_shortcode');

function registration_form_shortcode()
{
  return do_shortcode('[contact-form-7 id="64" title="Jelentkezési lap"]');
}
add_shortcode('jelentkezesi_lap', 'registration_form_shortcode');
