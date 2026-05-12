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
  wp_enqueue_style('csucsplusz-tailwind', get_template_directory_uri() . '/assets/css/tw.css');

  wp_enqueue_script('csucsplusz-copy-content', get_template_directory_uri() . '/assets/js/copy-content.js');
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
add_filter('use_block_editor_for_post', '__return_false');

if (current_user_can('manage_options')) {
  add_action('admin_print_footer_scripts', function () {
    $prices_json = get_option('prices_json');
    $price_names = '[]';
    if ($prices_json) {
      $prices = json_decode($prices_json);
      $standalone_names = array_map(fn($price) => $price->name, $prices->standalone);
      $calculated_names = array_map(fn($price) => $price->name, $prices->calculated);
      $price_names = json_encode(array_merge($standalone_names, $calculated_names));
    }
?>
    <script>
      window.priceNames = JSON.parse('<?php echo $price_names ?>')
      window.contacts = ['telefon', 'email', 'cím']
    </script>
<?php
  });
}

add_filter('mce_external_plugins', 'csucsplusz_tinymce_plugin');
add_filter('mce_buttons', 'csucsplusz_tinymce_buttons');
add_filter('mce_buttons_2', 'csucsplusz_tinymce_buttons_2');

function csucsplusz_tinymce_plugin($plugins)
{
  $plugins['csucsplusz_shortcodes'] = 'plugins/csucsplusz_shortcodes/plugin.min.js';
  return $plugins;
}

function csucsplusz_tinymce_buttons($buttons)
{
  $buttons = array(
    'undo', 'redo',
    'bold', 'italic',
    'underline', 'strikethrough',
    'bullist', 'numlist',
    'link', 'fullscreen');

  return $buttons;
}

function csucsplusz_tinymce_buttons_2($buttons)
{
  $buttons = array('price_shortcodes', 'contact_shortcodes', 'create_copy_shortcode');
  return $buttons;
}

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
    'jelentkezési lap',
    'másolható szöveg'
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
      $value = str_replace(' ', '¤', $matches[2]);
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

$html_codes = array(
  '&lt;' => '<',
  '&gt;' => '>',
  '&quot;' => '"'
);

function trainings_shortcode()
{
  $json = get_option('trainings_json');
  $data = json_decode($json);
  $trainings = $data->trainings;
  $no_training_start_text = $data->noTrainingStartText;
  $content = array_map(function ($training) use ($no_training_start_text) {
    $start_date = '';
    if ($training->hasStartDate) {
      if ($training->startDate !== '') {
        $start_date = '<div class="training-start-date">következő indulási dátum ' . $training->startDate . '</div>';
      } else {
        $start_date = '<div class="training-no-start-date">' . $no_training_start_text . '</div>';
      }
    }

    $description = normalize_shortcodes(strtr($training->description, $GLOBALS['html_codes']));
    return '
      <div class="training">
        <h2 class="training-header">' . $training->name . '</h2>'
      . $start_date .
      '<div class="training-description">' . do_shortcode($description) . '</div>
      </div>';
  }, $trainings);

  return join('', $content);
}
add_shortcode('kepzesek', 'trainings_shortcode');

function ar_shortcode($atts)
{
  $option_name = get_attr_value($atts);
  $ar = get_option(str_replace('¤', '_', $option_name) . '_price');
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
  $phone = get_option('phone_contact');
  return '<a href="tel:' . $phone . '">' . $phone . '</a>';
}
add_shortcode('telefonszam', 'phone_shortcode');

function email_shortcode()
{
  $email = get_option('email_contact');
  return '<a href="mailto:' . $email . '">' . $email . '</a>';
}
add_shortcode('email', 'email_shortcode');

function address_shortcode()
{
  $map_link = 'https://umap.openstreetmap.fr/hu/map/csucsplusz-autosiskola_1404279';
  $address = get_option('address_contact');
  return '<a href="' . $map_link . '">' . $address . '</a>';
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
          <td class="crt-time">' . $time->startTime . ' - ' . $time->endTime . '</td>
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

function copy_content_shortcode($atts) {
  $content = get_attr_value($atts);
  return '
    <span class="copy-content">
      <span>' . str_replace('¤', ' ', $content) . '</span>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" class="inline-block -mt-1 size-4 fill-slate-700">
        <g>
          <path fill-rule="evenodd" d="M3.25 2.5H4v.25C4 3.44 4.56 4 5.25 4h5.5C11.44 4 12 3.44 12 2.75V2.5h.75a.75.75 0 01.75.75v3a.75.75 0 001.5 0v-3A2.25 2.25 0 0012.75 1h-.775c-.116-.57-.62-1-1.225-1h-5.5c-.605 0-1.11.43-1.225 1H3.25A2.25 2.25 0 001 3.25v10.5A2.25 2.25 0 003.25 16h9.5A2.25 2.25 0 0015 13.75v-1a.75.75 0 00-1.5 0v1a.75.75 0 01-.75.75h-9.5a.75.75 0 01-.75-.75V3.25a.75.75 0 01.75-.75zm2.25-1v1h5v-1h-5z" clip-rule="evenodd" />
          <path d="M4.75 5.5a.75.75 0 000 1.5h3a.75.75 0 000-1.5h-3zM4 12.25a.75.75 0 01.75-.75h3a.75.75 0 010 1.5h-3a.75.75 0 01-.75-.75zM4.75 8.5a.75.75 0 000 1.5h2a.75.75 0 000-1.5h-2zM16 9.25a.75.75 0 01-.75.75h-4.19l1.22 1.22a.75.75 0 11-1.06 1.06l-2.5-2.5a.752.752 0 010-1.06l2.5-2.5a.75.75 0 111.06 1.06L11.06 8.5h4.19a.75.75 0 01.75.75z" />
        </g>
      </svg>
    </span';
}
add_shortcode('masolhato_szoveg', 'copy_content_shortcode');