# absurdly_lite_framework_alf
![ALFv1](https://github.com/user-attachments/assets/e9bc555e-bfca-481d-a469-42b277bb3283)

This Readme is a work in progress...

This is the release version v1 of ALF (Absurdly Lite Framework). It has currently been tested and deployed successfully on Apache servers running on PHP8.2.
Its routing engine supports AJAX calls, function calls, as well as ─of course─ the regular GET/POST calls. Also, API endpoints can be created using this same routing engine.

In order for the Framework to function for the first time, these are the first steps:
1. The file "man.off" can be renamed to "man.on" and this will enter the Manteinance Mode and redirect any visitor to the plain HTML version of the site that lives within Public/mantenimiento to show a "Work on Progress..." or any custom temporary message to visitors.
2. Inside the .htaccess.example file find the following line "RewriteRule ^(.*)index$ /subdirectory_if_needed/ [R=301,L]" and replace "subdirectory_if_needed" for your server's subdirectory, but if your proyect it is in root, then remove "subdirectory_if_needed/" and leave just the root /. Then rename .haccess.example to .htaccess
3. Inside the .env.example and your Database credentials or any other environment variables that will be read by the Core/App/DotEnv.class.php engine, for example as instansiated in Core/App/Model/Database.class.php. Then rename .env.example to .env
4. Inside Core/autoload.php choose the autoloader type you will be using; Option 1 is for loading just the baerbones ALFv1 autoloader, or Option 2 for also loading the Composer autoloader that lives inside Public/vendor for third party plugins
5. Inside Core/init.php.example you must configure error_reporting(E_ALL) or error_reporting(0) (in case of disabling error reporting here, remember also to comment out the "php_flag display_startup_errors php_flag display_errors" in your .htaccess file); Enabling or disabling any custom developer functions with "include 'devtools.php'"; Configure all the important PATH constants with PROYECTO, APP_SUBFOLDER (if your project is in root directory then leave this constant completely blank), URL_SUB and URL_BASE; Make sure that the autoloader section here matches your Core/autoload.php configuration, by enabling or disabling the "APP_PUBLIC . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'" line; Finally, enable or disable the ERror Logger with this line "require_once 'errlog.php'". Then rename .init.php.example to .init.php
6. Now to registering some routes in Core/routes.php

ROUTES

  lineal structure: add( HTTP method, URI(path), callback, parameters: [layout, [window-title , [css]], GET [[módulos], [local]], POST], [javascript] )

formatted structure:

  HTTP method,
  URI(path),
  callback,
  [layout, 
         [window-title , [css]], 
         [[módulos], [local]], 
         POST],
  [javascript]

Always leave the first route alone: /error/
After that you must start with the Index route, or Home or Root which is the same.