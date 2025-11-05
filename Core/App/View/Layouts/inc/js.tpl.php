<?php
foreach ($js as $key => $value) {
    echo '<script type="text/javascript" src="'.URL_PUBLIC.'js/' . $value . '.js"></script>';
    echo "\r\n";
} ?>