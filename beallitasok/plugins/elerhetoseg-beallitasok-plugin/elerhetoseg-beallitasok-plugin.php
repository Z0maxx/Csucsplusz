<?php
/*
Plugin Name: Elérhetőség Beállítások
Description: Admin felület az elérhetőségekhez
Version: 1.0
Author: Nyári Zoltán
*/

function render_elerhetoseg_beallitasok()
{
  require 'elerhetoseg-beallitasok.php';
}

add_action('admin_menu', function () {
  add_menu_page(
    'Elérhetőségek',
    'Elérhetőségek',
    'manage_options',
    'elerhetoseg-beallitasok',
    'render_elerhetoseg_beallitasok',
    'dashicons-buddicons-pm',
    23
  );
});

add_action('admin_init', function () {
  if (
    isset($_POST['contact_info_form_nonce']) &&
    wp_verify_nonce($_POST['contact_info_form_nonce'], 'contact_info_form_action') &&
    current_user_can('manage_options') &&
    isset($_POST['crt_json']) &&
    isset($_POST['phone']) &&
    isset($_POST['email']) &&
    isset($_POST['address'])
  ) {
    $crt_json = wp_unslash($_POST['crt_json']);
    update_option('crt_json', $crt_json);
    update_option('phone_contact', sanitize_text_field($_POST['phone']));
    update_option('email_contact', sanitize_text_field($_POST['email']));
    update_option('address_contact', sanitize_text_field($_POST['address']));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});