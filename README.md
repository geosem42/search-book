# What is it?
This is an exercise application built with Laravel and Vue.js

It allows the user to upload a PDF file and search inside it, displaying a list of matches while showing snippets and information about where in the document the match was found.

# Requirements
PHP 8.1 is required

# Installation
Install PHP dependencies
```
composer install
```
Install NPM packages
```
npm install
```
Generate app key
```
php artisan key:generate 
```
Link the storage directory
```
php artisan storage:link
```
Migrate the database
```
php artisan migrate
```
Build assets
```
npm run build
```
Run server
```
php artisan serve
```

# Screenshots
![Screenshot](https://i.imgur.com/PJJ3KAi.png)
