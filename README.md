## How to run locally

Basically, you need to read
[this](http://googlecloudplatform.github.io/appengine-php-wordpress-starter-project/)
and then
[this](http://www.frankie.bz/blog/developers/wordpress-multisite-on-google-app-engine-php-beta)
to get a full understanding of WPMU local installation for GAE.

1. make sure the password of `root` for mysql on `localhost` is empty
2. create a database named `brithon-appointments`
3. download [Google App Engine SDK for PHP](https://cloud.google.com/appengine/downloads?hl=en) and install it
4. `cd` into the repo dir and run `npm install`
5. run `gulp build` and the `app` dir will be created.
6. run `sudo dev_appserver.py --port 80 app`
7. visit `http://localhost` to go through the wp installation.

## Gulp

### clean `app`

```shell
$ gulp clean
```
### build package

```shell
$ gulp {action}
```
+ {action}: gulp task names: [build | deploy].
