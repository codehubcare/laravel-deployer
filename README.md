Laravel Deployer

A powerful tool for automating and streamlining Laravel application deployments to remote servers via FTP/SFTP.
Features

    Automated directory and file uploads
    Configurable deployment paths
    Environment-specific settings
    Simple command-line interface

Prerequisites

    PHP 7.4 or higher
    Composer
    Laravel 8.x or higher
    FTP/SFTP server access

Installation
Step 1: Install the Package

Add Laravel Deployer to your project as a development dependency:
bash
composer require codehubcare/laravel-deployer --dev

The --dev flag ensures it's only included in development environments.
Step 2: Publish Configuration

Publish the configuration file to customize deployment settings:
bash
php artisan vendor:publish --provider="Codehubcare\LaravelDeployer\LaravelDeployerServiceProvider"

This command creates a config/laravel-deployer.php file in your Laravel application where you can modify default settings.
Configuration
Environment Variables

Update your .env file with the following variables:
bash
# Local source directory (relative to project root)
LARAVEL_DEPLOYER_SRC_PATH=src

# Remote public directory
LARAVEL_DEPLOYER_PUBLIC_PATH=public_html

# FTP/SFTP connection details
LARAVEL_DEPLOYER_FTP_HOST=your.ftp.host.com
LARAVEL_DEPLOYER_FTP_USERNAME=your_username
LARAVEL_DEPLOYER_FTP_PASSWORD=your_password
LARAVEL_DEPLOYER_FTP_PORT=21
Variable Descriptions:

    LARAVEL_DEPLOYER_SRC_PATH: Local directory containing your Laravel application files
    LARAVEL_DEPLOYER_PUBLIC_PATH: Remote directory where files will be deployed
    LARAVEL_DEPLOYER_FTP_HOST: Remote server hostname or IP address
    LARAVEL_DEPLOYER_FTP_USERNAME: FTP/SFTP account username
    LARAVEL_DEPLOYER_FTP_PASSWORD: FTP/SFTP account password
    LARAVEL_DEPLOYER_FTP_PORT: Port number (default: 21 for FTP, 22 for SFTP)

Configuration File

The config/laravel-deployer.php file allows you to:

    Override environment variables
    Set additional deployment options
    Configure multiple deployment targets

Usage

Deploy your application with a single command:
bash
php artisan laravel-deployer:deploy
What Happens During Deployment

    Connects to the remote server using provided credentials
    Uploads the source directory contents to the remote destination
    Creates necessary directories if they don't exist
    Maintains directory structure

Troubleshooting

    Connection Failed: Verify FTP credentials and network accessibility
    Permission Denied: Check remote directory permissions
    Files Not Visible: Confirm the correct remote path is set

Additional Notes

    Always backup your application before deployment
    Test deployment in a staging environment first
    Keep sensitive credentials out of version control by using .env

For more advanced configuration options, refer to the config/laravel-deployer.php file comments.