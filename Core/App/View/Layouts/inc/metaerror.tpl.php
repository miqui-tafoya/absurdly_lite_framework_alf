<?php
$error = http_response_code();
?>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
<link rel="icon" type="image/png" sizes="96x96" href="<?php echo URL_PUBLIC ?>recursos/96.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo URL_PUBLIC ?>recursos/32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo URL_PUBLIC ?>recursos/16.png">
<?php
foreach ($metaParams[1] as $key => $value) {
    echo '<link rel="stylesheet" type="text/css" href="' . URL_PUBLIC . 'styles/' . $value . '.css">' . PHP_EOL;
}
?>
<title><?php echo SITE_NAME; ?> - Error <?php echo $error ?></title>