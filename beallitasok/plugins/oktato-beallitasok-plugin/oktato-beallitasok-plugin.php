<?php
/*
Plugin Name: Oktató Beállítások
Description: Admin oldal az oktatókhoz
Version: 1.0
Author: Nyári Zoltán
*/

class Instructor
{
  public string $name;
  public string $titles;
}

function render_oktato_beallitasok()
{
  require 'oktato-beallitasok.php';
}

add_action('admin_menu', function () {
  add_menu_page(
    'Oktatók',
    'Oktatók',
    'manage_options',
    'oktato-beallitasok',
    'render_oktato_beallitasok',
    'dashicons-groups',
    24
  );
});

add_action('admin_init', function () {
  if (
    isset($_POST['instructors_form_nonce']) &&
    wp_verify_nonce($_POST['instructors_form_nonce'], 'instructors_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['instructors_json'])
  ) {
    $json = str_replace('\"', '"', $_POST['instructors_json']);
    $instructors = json_decode($json);
    /** @var Instructor $instructor */
    foreach ($instructors as $instructor) {
      $instructor->name = sanitize_text_field($instructor->name);
      $instructor->titles = sanitize_textarea_field($instructor->titles);
    }

    update_option('instructors_json', json_encode($instructors));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});
