<?php
/*
Plugin Name: Tanfolyami Adatok Oldal
Description: Admin felület a tanfolyami adatok oldalhoz
Version: 1.0
Author: Nyári Zoltán
*/

include_once plugin_dir_path(__FILE__) . '../normalize-option-name.php';

function render_tanfolyam_oldal()
{
  require 'tanfolyam-oldal-beallitasok.php';
}

add_action('admin_menu', function () {
  add_menu_page(
    'Tanfolyam Oldal',
    'Tanfolyam Oldal',
    'manage_options',
    'tanfolyam_oldal',
    'render_tanfolyam_oldal',
    'dashicons-list-view',
    24
  );
});

add_action('admin_init', function () {
  if (
    isset($_POST['course_page_form_nonce']) &&
    wp_verify_nonce($_POST['course_page_form_nonce'], 'course_page_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['course_page_json'])
  ) {
    $json = wp_unslash($_POST['course_page_json']);
    $data = json_decode($json);
    $sectionGroups = (array)$data;
    foreach ($sectionGroups as $sectionGroup) {
      foreach ($sectionGroup as $section) {
        $section->name = sanitize_text_field($section->name);
      }
    }

    foreach ($data->sections as $section) {
      foreach ($section->subSections as $subSection) {
        $subSection->name = sanitize_text_field($subSection->name);
      }
    }

    update_option('course_page_json', json_encode($data));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});
