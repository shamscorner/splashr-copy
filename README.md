![Screenshot 1](https://raw.githubusercontent.com/shamscorner/images/main/splashr-0.png)
![Screenshot 1](https://raw.githubusercontent.com/shamscorner/images/main/splashr-1.png)
![Screenshot 1](https://raw.githubusercontent.com/shamscorner/images/main/splashr-2.png)

# Local Development

You can setup this project as you like, but the [Laradock](https://laradock.io/) is recommended, if you are interested to setup using `Docker`.

## Setup Using Laradock

**Step 1** - Clone this project into a directory

```
https://github.com/didieruzan/splashr.git
```

**Step 2** - Clone the **Laradock** module in that same directory

```
https://github.com/laradock/laradock.git
```

So the directory structure will be something similar like this,

-   SomeDirectory
    -   laradock
    -   splashr

**Step 3** - Copy the `env-example` file to `.env` in `laradock` directory

```
cd laradock
cp env-example .env
```

**Step 4** - Change some project specific configurations in the `laradock/.env` file

Change compose path separator if the operating system is Windows in `.env` file inside the `laradock` folder. Search for `COMPOSE_PATH_SEPARATOR` inside the `.env` file.

```
# Change the separator from : to ; on Windows (Only for Windows Users)
COMPOSE_PATH_SEPARATOR=;
```

Update the php version 7.3 to 7.4. Search for `PHP_VERSION` in the `.env` file

```
PHP_VERSION=7.4
```

Accept the installation of the EXIF data for Spatie/Media-Library package in laradock. Change the `PHP_FPM_INSTALL_EXIF` false to true

```
PHP_FPM_INSTALL_EXIF=true
```

**Step 5** - Change the prefix of container names. This is useful if you have multiple projects that use laradock to have separate containers per project. Search for `COMPOSE_PROJECT_NAME` inside the `.env` file.

```
COMPOSE_PROJECT_NAME=splashr
```

**Step 6** - Add the Site URL to the `host` file in the local machine

```
127.0.0.1	splashr.com
```

**Step 7** - Setup `nginx` server

Go to the folder mentioned below,

```
laradock > nginx > sites
```

and change the default names `*.conf`, removing the `.example` suffix similar like below:

```
- app.conf.example > app.conf
- laravel.conf.example > splashr.conf
- symfony.conf.example > symfony.conf
```

Then open up the `splashr.conf` file and edit like below,

```
server_name laravel.test;
root /var/www/laravel/public;
```

to,

```
server_name splashr.com;
root /var/www/splashr/public;
```

**Step 8** - Run the `Docker` containers

```
sudo docker-compose up -d nginx postgres
```

**Step 9** - Execute the `postgres` container and setup the database

```
sudo docker-compose exec postgres bash
```

Then,

```
psql -U default
create database splashr;
```

and then exit from `postgres` container

```
exit
exit
```

**Step 10** - Execute the workspace container

```
sudo docker-compose exec --user=laradock workspace bash
```

**Step 11** - Copy the `.env.example` file to `.env` in

```
cd splashr
cp .env.example .env
```

**Step 12** - Install `Composer`

```
composer install
```

**Step 13** - Generate application key

```
php artisan key:generate
```

**Step 14** - Update the .env file

```
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=splashr
DB_USERNAME=default
DB_PASSWORD=secret
```

**Step 15** - Run Migration

```
php artisan migrate
```

**Step 16** - Install node and dev command

```
npm install
npm run dev
or,
npm run watch
```

Finally, open the site in the browser [splashr.com](http://splashr.com/)

Happy Coding 😃

## To setup the development environment with database seeder

```
php artisan dev:setup
```
