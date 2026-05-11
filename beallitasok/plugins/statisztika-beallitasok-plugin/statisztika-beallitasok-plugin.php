<?php
/*
Plugin Name: Statisztika Beállítások
Description: Admin felület a statisztikához
Version: 1.0
Author: Nyári Zoltán
*/

function render_statisztika_beallitasok()
{
  require 'statisztika-beallitasok.php';
}

add_action('admin_menu', function () {
  add_menu_page(
    'Statisztika',
    'Statisztika',
    'manage_options',
    'statisztika-beallitasok',
    'render_statisztika_beallitasok',
    'dashicons-chart-line',
    21
  );
});

add_action('admin_init', function () {
  if (
    isset($_POST['stat_form_nonce']) &&
    wp_verify_nonce($_POST['stat_form_nonce'], 'stat_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['stat_json'])
  ) {
    update_option('stat_json', str_replace('\"', '"', $_POST['stat_json']));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});
