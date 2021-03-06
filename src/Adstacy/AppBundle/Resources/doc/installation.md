# Prasyarat
Pastikan php, apache, postgresql, [elasticsearch](http://www.elasticsearch.org/guide/reference/setup/installation/) dan git telah terinstall pada sistem. (saat ini baru mendukung sistem operasi linux)

```sh
sudo apt-get install php5 apache2 postgresql git php5-intl
```

# Instalasi
Instalasi membutuhkan beberapa langkah:

1.  Clone adstacy.git
2.  Install node.js dan npm
3.  Update package npm
4.  Install less
5.  Setting virtual host dan environment variables
6.  Update package composer
7.  Update parameters.yml
8.  Command
9.  Test

## Langkah 1: Clone adstacy.git
Download project Adstacy dengan perintah `git clone https://bitbucket.org/wlzch/adstacy.git` kemudian masuk ke folder tersebut

## Langkah 2: Update package composer
Jalankan perintah `php composer.phar install` untuk mengupdate library-library yang dibutuhkan. Jika terjadi timeout jalankan `COMPOSER_PROCESS_TIMEOUT=300 ./composer.phar install`

## Langkah 3: Install node.js dan npm
```sh
git clone https://github.com/joyent/node.git
cd node
git checkout master #Try checking nodejs.org for what the stable version is
./configure && make && sudo make install
```

Buka command line dan jalankan perintah `node --help` dan `npm --help` untuk memastikan instalasi berhasil

## Langkah 4: Update package npm
Jalankan perintah `npm install` untuk menginstall uglifyjs dan uglifycss

## Langkah 5: Install less
Jalankan perintah `npm install -g less` untuk menginstall coffeescript dan less

## Langkah 6: Setting virtual host 
Buka file konfigurasi apache (httpd.conf atau 000-default) dan masukkan konfigurasi sebagai berikut

```apache
NameVirtualHost *:80

<VirtualHost *:80>
	ServerAdmin webmaster@localhost
	DocumentRoot /var/www
	ServerName localhost
	<Directory /var/www>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride None
		Order allow,deny
		allow from all
	</Directory>
	ErrorLog ${APACHE_LOG_DIR}/error.log

	# Possible values include: debug, info, notice, warn, error, crit,
	# alert, emerg.
	LogLevel warn

	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>

<VirtualHost *:80>
    DocumentRoot /path/to/adstacy/web
    ServerName adstacy.dev
    DirectoryIndex app.php
    <Directory "/path/to/adstacy/web">
        AllowOverride All
        allow from all
    </Directory>
</VirtualHost>
```

buka file /etc/hosts dan tambahkan `127.0.0.1 adstacy.dev`. Kemudian restart apache.

## Langkah 7: Update parameters.yml
Copy file parameters.yml.dist ke parameters.yml kemudian sesuaikan settingan dengan settingan mesin masing-masing

```yml
parameters:
    database_driver:   pdo_mysql
    database_host:     localhost
    database_port:     null
    database_name:     adstacy
    database_user:     adstacy
    database_password: adstacy

    mailer_transport:  smtp
    mailer_host:       localhost
    mailer_port:       24
    mailer_encryption: null
    mailer_user:       ~
    mailer_password:   ~

    node_path:         "/usr/local/bin/node"
    node_modules_path: "/usr/local/lib/node_modules"

    locale:            en
    secret:            ThisTokenIsNotSoSecretChangeIt
```

## Langkah 8: Command
Jalankan perintah `php app/console doctrine:database:create` untuk membuat database

Jalankan perintah `php app/console doctrine:migrations:migrate` untuk mengupdate skema tabel ke database

Jalankan perintah `php app/console assetic:dump` untuk dump assets yang memakai assetic.

Jalankan perintah `php app/console doctrine:fixtures:load --purge-with-truncate` untuk mengload data dummy.

Jalankan perintah `php app/console fos:elastica:populate` untuk mengpopulate data kedalam elasticsearch

Jalankan perintah `php app/console fos:js-routing:dump` untuk dump routing pada javascript

## Langkah 9: Test
Buka http://adstacy.dev/app_dev.php dan pastikan website bekerja dengan baik.
