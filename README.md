# IP Blacklisting package for Laravel

## Installation
Run the following command to install this package:
```
    composer require shahriar-siraj/laravel-ip-blacklisting
```

Then, run the following command to run database migration:
```
    php artisan migrate
```

## Usage
The package creates a database table named `blacklisted_ips`. To block an IP address, insert a record in this table 
which will restrict it from accessing the routes in `web` groups.

Table columns:

| Column                | Description                                                                      |
|-----------------------|----------------------------------------------------------------------------------|
| `ip`                  | IP Address that you want to block                                                |
| `duration_in_minutes` | How long you want to block it. Set it as 0, if you want to block it indefinitely |

## Configuration
The package allows blocking certain IP addresses for specific minutes. Therefore, a cron job runs 
`ip-blacklisting:clean` command every minute to clean the outdated IP addresses. 

However, if you do not want to use this feature, or if you only want to block IP addresses for specific hours or days, 
then you can optimize the task scheduling by modifying the cron job schedule in `config/ip_blacklisting.php` file:

```injectablephp
return [
    /**
     * Cron job schedule for cleaning outdated IP addresses
     */
    'cleaner_schedule' => '* * * * *'
];
```
