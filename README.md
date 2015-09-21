## How to run locally

TODO: you may fail by following this instructions, since WPMU need several
steps. Let me polish it later.

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
$ gulp {action} [--version {version}]
```
+ {action}: gulp task names: [build | deploy].
+ {version}: [dev | refname]. Default: dev
  + `dev`: the working tree. So, do not use `dev` to name branch and tag.
  + `refname`: a valide git branch (either local or remote branch) or tag name.
