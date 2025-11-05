<?php
function dd($d, $opt = 0) {
  if ($opt == 0) {
    echo '<pre>';
    print_r($d);
    echo '</pre>';
  } else {
    echo '<pre>';
    var_dump($d);
    echo '</pre>';
  }
}