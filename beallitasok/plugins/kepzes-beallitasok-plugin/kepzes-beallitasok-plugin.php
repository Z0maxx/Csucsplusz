<?php
/*
Plugin Name: Képzés Beállítások
Description: Admin felület a képzésekhez
Version: 1.0
Author: Nyári Zoltán
*/

include_once plugin_dir_path(__FILE__) . '../normalize-option-name.php';

class Training
{
  public string $name;
  public bool $hasStartDate;
  public string $startDate;
  public string $description;
}

function render_kepzes_beallitasok()
{
  require 'kepzes-beallitasok.php';
}

add_action('admin_menu', function () {
  add_menu_page(
    'Képzések',
    'Képzések',
    'manage_options',
    'kepzes-beallitasok',
    'render_kepzes_beallitasok',
    'dashicons-welcome-learn-more',
    20
  );
});

add_action('admin_init', function () {
  if (
    isset($_POST['training_form_nonce']) &&
    wp_verify_nonce($_POST['training_form_nonce'], 'training_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['trainings_json'])
  ) {
    $json = wp_unslash($_POST['trainings_json']);
    $data = json_decode($json);
    $trainings = $data->trainings;
    /** @var Training $t */
    foreach ($trainings as $training) {
      $training->name = sanitize_text_field($training->name);
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
