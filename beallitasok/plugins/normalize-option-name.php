<?php

function normalize_option_name(string $text)
{
  $charMap = [
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
  ];

  return strtr(str_replace(' ', '_', $text), $charMap);
}
