# Laravel Deployer
Easily set up Laravel Deployer for seamless deployment of your Laravel applications.

## Installation
Install Laravel Deployer as a development dependency using the following command:
```bash
composer require codehubcare/laravel-deployer --dev
```

To publish the Laravel Deployer configuration file, run the following command:
```bash
php artisan vendor:publish --provider="Codehubcare\LaravelDeployer\LaravelDeployerServiceProvider"
```
This will create a laravel-config.php file in your application’s config directory.