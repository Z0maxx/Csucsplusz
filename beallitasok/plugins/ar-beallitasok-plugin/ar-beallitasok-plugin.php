<?php
/*
Plugin Name: Ár Beállítások
Description: Admin oldal az árakhoz
Version: 1.0
Author: Nyári Zoltán
*/

include_once plugin_dir_path(__FILE__) . '../normalize-option-name.php';

/**
 * @param array<ValueWrap> $standalone
 * @param array<ValueWrap> $constant
 * @param array<Calculated> $calculated
 * @param array<string> $deleted
 */
class Prices
{
  public array $standalone;
  public array $constant;
  public array $calculated;
  public array $deleted;

  public function __construct(array $standalone, array $constant, array $calculated, array $deleted)
  {
    $this->standalone = $standalone;
    $this->constant = $constant;
    $this->calculated = $calculated;
    $this->deleted = $deleted;
  }

  public static function create(object $data): self
  {
    $standalone = array_map(
      fn($item) => new ValueWrap(
        $item->name ?? '',
        $item->type ?? '',
        $item->value ?? 0
      ),
      $data->standalone ?? []
    );

    $constant = array_map(
      fn($item) => new ValueWrap(
        $item->name ?? '',
        $item->type ?? '',
        $item->value ?? 0
      ),
      $data->constant ?? []
    );

    $calculated = array_map(
      fn($item) => new Calculated(
        $item->name ?? '',
        $item->type ?? '',
        $item->startingValue ?? '',
        array_map(
          fn($var) => new Variable($var->operation ?? '', $var->value ?? ''),
          $item->variables ?? []
        )
      ),
      $data->calculated ?? []
    );

    return new self($standalone, $constant, $calculated, $data->deleted ?? []);
  }
}

/**
 * @param string $name
 * @param string $type
 */
abstract class PriceClass
{
  public string $name;
  public string $type;

  public function __construct(string $name, string $type)
  {
    $this->name = $name;
    $this->type = $type;
  }
}

/**
 * @param string $name
 * @param string $type
 * @param int $value
 */
class ValueWrap extends PriceClass
{
  public int $value;

  public function __construct(string $name, string $type, int $value)
  {
    parent::__construct($name, $type);
    $this->value = $value;
  }
}

/**
 * @param string $name
 * @param string $type
 * @param string $startingValue
 * @param array<Variable> $variables
 */
class Calculated extends PriceClass
{
  public string $startingValue;
  public array $variables;

  public function __construct(string $name, string $type, string $startingValue, array $variables = [])
  {
    parent::__construct($name, $type);
    $this->startingValue = $startingValue;
    $this->variables = $variables;
  }
}

/**
 * @param string $operation
 * @param string $value
 */
class Variable
{
  public string $operation;
  public string $value;

  public function __construct(string $operation, string $value)
  {
    $this->operation = $operation;
    $this->value = $value;
  }
}

function render_ar_beallitasok()
{
  require 'ar-beallitasok.php';
}

function get_ar(string $ar)
{
  return (int)(get_option($ar . '_ar') ?: 0);
}

add_action('admin_menu', function () {
  add_menu_page(
    'Árak',
    'Árak',
    'manage_options',
    'ar-beallitasok',
    'render_ar_beallitasok',
    'dashicons-money-alt
',
    22
  );
});

/**
 * @param array<PriceClass> $merged
 * @param string $name
 */
function find_by_name(array $merged, string $name): PriceClass
{
  return array_find($merged, fn($v) => $v->name === $name);
}

/**
 * @param array<PriceClass> $merged
 * @param Calculated $price
 */
function get_calculated_price(array $merged, Calculated $price)
{
  $startingValueWrap = find_by_name($merged, $price->startingValue);
  $startingValue = 0;
  if ($startingValueWrap->type === 'calculated') {
    $startingValue = get_calculated_price($merged, $startingValueWrap);
  } else {
    /** @var ValueWrap $startingValueWrap */
    $startingValue = $startingValueWrap->value;
  }

  $values = array($startingValue);
  array_push($values, ...array_map(function ($variable) use ($merged) {
    $valueWrap = find_by_name($merged, $variable->value);
    if ($valueWrap->type === 'calculated') {
      return get_calculated_price($merged, $valueWrap);
    } else {
      /** @var ValueWrap $valueWrap */
      return $valueWrap->value;
    }
  }, $price->variables));

  $operations = array_map(fn($variable) => $variable->operation, $price->variables);
  for ($i = 0; $i < count($operations); $i++) {
    if ($operations[$i] === '*') {
      $values[$i + 1] = $values[$i + 1] * $values[$i];
      $values[$i] = 0;
    }
  }

  return array_sum($values);
}

function get_price_option_name(string $price_name) {
  return normalize_option_name($price_name) . '_price';
}

function update_price_option(ValueWrap $valueWrap)
{
  update_option(get_price_option_name($valueWrap->name), $valueWrap->value);
}

add_action('admin_init', function () {
  if (
    isset($_POST['ar_beallitasok_nonce']) &&
    wp_verify_nonce($_POST['ar_beallitasok_nonce'], 'ar_beallitasok_action') &&
    isset($_POST['prices_json']) &&
    current_user_can('manage_options')
  ) {
    $json = str_replace('\"', '"', $_POST['prices_json']);
    $prices = Prices::create(json_decode($json));
    /** @var string $deleted */
    foreach ($prices->deleted as $deleted) {
      delete_option(get_price_option_name($deleted));
    }

    /** @var ValueWrap $standalone */
    foreach ($prices->standalone as $standalone) {
      $standalone->name = sanitize_text_field($standalone->name);
      update_price_option($standalone);
    }

    /** @var ValueWrap $constant */
    foreach ($prices->constant as $constant) {
      $constant->name = sanitize_text_field($constant->name);
      update_price_option($constant);
    }

    /** @var array<PriceClass> $merged */
    $merged = array_merge($prices->standalone, $prices->constant, $prices->calculated);
    /** @var Calculated $calculated */
    foreach ($prices->calculated as $calculated) {
      $calculated->name = sanitize_text_field($calculated->name);
      $calculated->startingValue = sanitize_text_field($calculated->startingValue);
      /** @var Variable $variable */
      foreach ($calculated->variables as $variable) {
        $variable->value = sanitize_text_field($variable->value);
      }

      update_option(get_price_option_name($calculated->name), get_calculated_price($merged, $calculated));
    }

    update_option('prices_json', json_encode($prices));
    add_action('admin_notices', function () {
      echo '
        <div class="notice notice-success is-dismissible">
          <p>Sikeres beállítás</p>
        </div>';
    });
  }
});
