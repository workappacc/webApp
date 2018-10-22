# webApp

Wep Application

Requirements:
 - PHP 7.1+
 - apache2 or something like webserver
 - redis server
 - rabbitMQ
 - mysql 5.7

First run:
 - run from root folder:

   to download and setup vendor with autoload
    - composer install

   create database
    - php bin/console doctrine:database:create

   creates tables
    - php bin/console doctrine:schema:update --force

   create admin user with login=admin password=admin
    - php bin/console app:create-admin-user

   for tests run
    - ./vendor/bin/simple-phpunit

   for add feedbacks from contact form to your database use console this
    - php bin/console app:get-feedbacks





