<a href="https://www.carloyi.com"><img src="https://node.carloyi.com/carloyi@3x.svg" alt="" width="190"/></a>

# API Documentation

>Carloyi is not just a motoring website but a virtual car dealership - It makes buying a car new and easy. Our aim is to give
 the buyer unbiased and practical advice based on their decision making criteria. [Application base route here.](https://api.carloyi.com/)

## Environment and Requirements

The below is a set of the environment the application runs on:

### Environment

- CentOS `release 6.1 final`
- Server version: `Apache/2.2.15 (Unix)`
- Host: [api.carloyi.com](https://api.carloyi.com/)

### Base Language

- `PHP >= 7.2.0`

### Framework

- Laravel version 5.8 [Laravel documentation here](https://laravel.com/)

- When deplogin to dev environment
````
$ composer install
$ php artisan config:cache
$ php artisan route:cache
````
- When deploying to production
````
$ composer install --optimize-autoloader --no-dev
$ php artisan config:cache
$ php artisan route:cache
````
- [Get Composer here](https://getcomposer.org)

#### Database

- MySQL version 14.14 - distribution 5.5.6

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
