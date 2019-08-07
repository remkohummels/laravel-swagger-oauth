# Laravel + Swagger + oAuth Admin Portal

***

Key Technologies:

- PHP v7.2

- Laravel v5.8

- Swagger v2.0

- Oauth2 + Laravel Passport

- PostgreSQL v10.8

- MongoDB v3.6


## Prerequisites for LAPP(Linux/Apache/PostgreSQL/PHP) stack

1). Install PHP 7.2 (reference: [installation in ubuntu](https://thishosting.rocks/install-php-on-ubuntu/))

2). Install PostgreSQL v10.8 (reference: [installation in ubuntu](https://www.liquidweb.com/kb/install-and-connect-to-postgresql-10-on-ubuntu-16-04/))

3). Install MongoDB v3.6 (reference: [installation in ubuntu](https://www.technologist.site/2018/06/18/how-to-install-mongodb-3-6-on-ubuntu/))

4). Install Apache2 (reference: [installation in ubuntu](https://www.digitalocean.com/community/tutorials/how-to-install-the-apache-web-server-on-ubuntu-18-04))

5). Install Composer (reference: [installation in ubuntu](https://websiteforstudents.com/how-to-install-php-composer-on-ububuntu-16-04-17-10-18-04/))

6). Install Redis (reference: [installation in ubuntu](https://tecadmin.net/install-redis-ubuntu/))

```composer
$ curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

7) Install Node.js v10.x & Npm v6.9.0 (reference: [installation in ubuntu](https://askubuntu.com/questions/594656/how-to-install-the-latest-versions-of-nodejs-and-npm-for-ubuntu-14-04-lts))

```node
$ curl -sL https://deb.nodesource.com/setup_10.x | sudo -E bash -
$ sudo apt-get update
$ sudo apt-get install -y nodejs
```

8) If the installed npm version is lower than v6.9.0, upgrade it to the latest one.

```npm
$ sudo npm install npm -g
```

9). Install php 7.2 extensions 

```php
$ sudo apt install php7.2-dev php7.2-curl php7.2-mbstring php7.2-xmlrpc php7.2-soap php7.2-gd php7.2-xml php7.2-cli php7.2-zip php7.2-pgsql php7.2-mongodb php7.2-sqlite3 php7.2-redis
```


## Create User and Database

1). PostgreSQL

```psql
$ sudo -u postgres createuser <username>
$ sudo -u postgres createdb <dbname>
$ sudo -u postgres psql
postgres=# alter user <username> with encrypted password '<password>';
postgres=# alter user <username> with superuser;
postgres=# grant all privileges on database <dbname> to <username> ;
postgres=# \q
```

- Test:

```psql
$ sudo -u postgres psql
postgres=# \list
postgres=# \q
```

2). MongoDB

```mongod
$ mongo
> use example_db
> db.new_collection.insert({ "example_key": "example_value" })
> db.createUser({ user: "example_user", pwd: "password", roles: [{ role: "readWrite", db: "example_db" }]})
> quit()
```

- Test:

```mongod
$ mongo -u example_user -p --authenticationDatabase example_db
> Show dbs
> quit()
```


## Application Setup in the LAPP stack Environment

Clone git repository to /var/www/html folder, checkout your dev branch (or staging/master branch)

In the project folder,

1). Create a .env file copying from .env.example.

2). Run `$ composer install` to install the necessary dependencies.

3) Run `$ npm install` to install the node modules & libraries by Npm. 

4) Run `$ npm run dev` to publish the assets(fonts/images/js/css/scss) by Laravel Mix + Webpack.

5). Run `$ php artisan key:generate` to generate a new key.

6). Run `$ php artisan config:cache` to reflect the .env configuration.

7). Run `$ php artisan migrate --seed` to migrate and seed the data in DB.

8). Follow up the below supervisor configuration.

9). To start the scheduler itself, we only need to add one cron job on the server (using the `crontab -e` command), which executes `php /path/to/artisan schedule:run` every minute in the day. 
To discard the cron output we put `/dev/null 2>&1` at the end of the cronjob expression. `* * * * * php /path/to/artisan schedule:run 1>> /dev/null 2>&1`


## Supervisor Configuration

1). Run `$ sudo apt-get install supervisor` to install supervisor.

2). Open `$ sudo nano /etc/supervisor/conf.d/laravel-worker.conf` to create a config file in /etc/supervisor/conf.d directory.

3). Copy the followings in the `laravel-worker.conf` file and save it.

```supervisor
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/laravel_app/artisan queue:listen --sleep=3 --tries=3
autostart=true
autorestart=true
user={sysuser}
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/laravel_app/storage/logs/worker.log
```

4). Update the Supervisor configuration and start the processes using the following commands.

`$ sudo supervisorctl reread`

`$ sudo supervisorctl update`

`$ sudo supervisorctl start laravel-worker:*`

***


## Coding Style

- Use [PSR-1](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md) and [PSR-4](http://www.php-fig.org/psr/psr-4/) coding standards.

- PHP tab size is 4.

- Blade/HTML tab size is 2.

- CSS/SCSS/JavaScript tab size is 2.

- Translate tabs to spaces.

- Ensure newline at end of file.

- Trim trailing white space.
