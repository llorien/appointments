# Usage

## How to build
```shell
$ gulp clean
$ gulp {action} [--environment {environments}]
```

* {action}:  `[build | deploy]`.
  + `build`: builds release package in `app`.
  + `deploy`: deploy to GAE.
* {environments}: `[local | dev | prod]`. Default: `local`
  + `local`: local development version.
  + `dev`: developement version deployed on GAE.
  + `prod`: production version on GAE.

## How to run locally
1. Add the following line to your `/etc/hosts` (for Windows, it's `%systemroot%\system32\drivers\etc\hosts`)

  > 127.0.0.1 appointments-local.brithon.com
1. Install MySQL 5.5+ and make sure the password of `root` on `localhost` is empty.
   It's easy to install MySQL in local box, while installation in virtual machine is recommended, which is also very easy. And you only need to forward port `3306` of guest to `3306` of host. Actually, it would be much simpler with docker like this:

   ```shell
    # install msyql docker image, and create a local container `mysql-5.6`, with empty root password.
    # local data volume /var/lib/mysql is created for data persistence.
    $ docker run --name mysql-5.6 -p 3306:3306 -v /var/lib/mysql -e MYSQL_ALLOW_EMPTY_PASSWORD=yes -d mysql:5.6

    # use this after
    $ docker start mysql-5.6

    # (optional for mac only), forward port vm `3306` to host `3306`.

    # remove the container
    $ docker rm -v mysql-5.6
   ```
1. Create a database named `brithon_appointments`.

  ```shell
$ echo 'CREATE DATABASE IF NOT EXISTS brithon_appointments;' | mysql -u root
  ```
1. Download and install [Google App Engine SDK for PHP](https://cloud.google.com/appengine/downloads).
1. Clone this repo and `cd` into the working dir.
1. Run `npm install`.
1. Run `gulp build` and the `app` dir will be created.
1. Read [this](http://googlecloudplatform.github.io/appengine-php-wordpress-starter-project/) and then [this](http://www.frankie.bz/blog/developers/wordpress-multisite-on-google-app-engine-php-beta) to get a full understanding of WPMU local installation for GAE.
   - You have to first comment out mark 1 and 2 at first, and then uncomment them during the installation.
1. Start the service with `npm start`.
1. Visit `http://appointments-local.brithon.com` to go through the wp installation.
