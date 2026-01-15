# absurdly_lite_framework_alf
![ALFv1](https://github.com/user-attachments/assets/e9bc555e-bfca-481d-a469-42b277bb3283)

This README is a work in progress...

This is the release version v1 of ALF (Absurdly Lite Framework). It has been tested and successfully deployed on Apache servers running PHP 8.2.
Its routing engine supports AJAX calls, function calls, as well as regular GET/POST requests. API endpoints can also be created using this routing engine.
The project includes the mvc-generator-1.0.0, a Java CLI app that works for unix-like systems (Linux, Mac) by executing it through the ./build script, or on Windows systems by running the build.bat script.

## Initial Setup

To get the framework running for the first time, follow these steps:

1. **Maintenance Mode**: The file `man.off` can be renamed to `man.on` to enable Maintenance Mode. This will redirect all visitors to the plain HTML version of the site located in `Public/mantenimiento`, displaying a "Work in Progress..." message or any custom temporary message.

2. **Configure .htaccess**: Inside the `.htaccess.example` file, find the line `RewriteRule ^(.*)index$ /subdirectory_if_needed/ [R=301,L]` and replace `subdirectory_if_needed` with your project's subdirectory on your server. If your project is located directly in the root, remove `subdirectory_if_needed/` and leave just `/`. Then rename `.htaccess.example` to `.htaccess`.

3. **Configure Environment Variables**: Inside `.env.example`, add your database credentials and any other environment variables that will be read by the `Core/App/DotEnv.class.php` engine (for example, as instantiated in `Core/App/Model/Database.class.php`). Then rename `.env.example` to `.env`.

4. **Choose Autoloader**: Inside `Core/init.php.example`, choose the autoloader type by setting `define('COMPOSER', false)` to either `true` or `false`. Use `false` for the barebones ALFv1 autoloader, or `true` to load the Composer autoloader located in `Public/vendor` for third-party plugins.

5. **Configure Debug Mode**: Inside `Core/init.php.example`, configure `define('DEBUG', true)` to either `true` or `false` to show or hide error printing and enable/disable user-defined development tools. Finally, rename `init.php.example` to `init.php`.

6. **Register Routes**: Now you can start registering routes in `Core/routes.php`.