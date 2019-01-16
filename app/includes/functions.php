<?php

function num2per($number, $total, $precision = 0) {
  if ($number < 0) {
    return 0;
  }

  try {
    $percent = ($number / $total) * 100;
    return round($percent, $precision);
  } catch (Exception $e) {
    return 0;
  }

}