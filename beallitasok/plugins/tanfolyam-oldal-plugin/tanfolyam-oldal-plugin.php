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
    isset($_POST['training_form_nonce']) &&
    wp_verify_nonce($_POST['training_form_nonce'], 'training_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['trainings_json'])
  ) {
    $json = str_replace('\"', '"', $_POST['trainings_json']);
    $data = json_decode($json);
    $trainings = $data->trainings;
    /** @var Training $t */
    foreach ($trainings as $training) {
      $training->name = sanitize_text_field($training->name);
      $training->description = sanitize_textarea_field($training->description);
      $option_name = normalize_option_name($training->name) . '_training_start';
      if ($training->hasStartDate) {
        update_option($option_name, $training->startDate);
      }
      else {
        delete_option($option_name);
      }
    }

    $data->noTrainingStartText = sanitize_text_field($data->noTrainingStartText);
    update_option('trainings_json', json_encode($data));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});
