'use strict';

var gulp = require('gulp');
var gPlugins = require('gulp-load-plugins')();

var path = require('path');
var fs = require('fs');
var shell = require('shelljs');
var del = require('del');

var _ = require('lodash');

var environments = {
  dev: {
    url_suffix: '-dev'
  },
  prod: {
    url_suffix: ''
  },
  local: {
    url_suffix: ''
  }
};

var bases = {
  repo: __dirname
};

var dirs = {
  src: path.join(bases.repo, 'src'),
  app: path.join(bases.repo, 'app'),
  wordpress: path.join(bases.repo, 'app', 'wordpress')
};

var taskConfig = {};

function shellWrapper(cmdTemplate, data) {
  var _data = data || {};

  return shell.exec(_.template(cmdTemplate)(_data));
}

function normalizeVersion(version) {
  // normalize the version for GAE.
  // '/' is allowed in branch and tag names, so escape it for path buidling.
  // we also escape bad chars for file name.
  return version.replace(/[\/\\:\.\?\*\|'"# ]/g, '-').toLowerCase();
}

function getVersion() {
  var res = shellWrapper('git name-rev --tags --name-only --no-undefined HEAD');

  if (res.code) {
    gPlugins.util.log(gPlugins.util.colors.yellow('[Warn]',
      'no tag attached to current HEAD, try to use commit# instead.'));

    res = shellWrapper('git rev-parse --short HEAD');
    if (res.code) {
      gPlugins.util.log(gPlugins.util.colors.red('[Error]',
        'get commit# failed,', res.output));
      process.exit(1);
    }
    taskConfig.version = ['v', gPlugins.util.date("UTC:yyyymmdd'T'HHMMss'Z'"), res.output.trim()].join('-');
  } else {
    taskConfig.version = res.output.trim();
  }

  taskConfig.version = normalizeVersion(taskConfig.version);
}

gulp.task('default', function() {
  fs.readFile('README.md', 'utf8', function(err, data) {
    if (err) {
      throw err;
    }
    console.log(data);
  });
});

gulp.task('sanitycheck', function(callback) {
  // supress shell command output
  shell.config.silent = true;

  taskConfig.environment = (gPlugins.util.env.environment || 'local').toLowerCase();

  if (!(_.has(environments, taskConfig.environment))) {
    gPlugins.util.log(gPlugins.util.colors.red('[Error]',
      'unknown environment:',
      taskConfig.environment));
    process.exit(1);
  }

  taskConfig.url_suffix = environments[taskConfig.environment].url_suffix;

  callback();
});

gulp.task('clean', function(callback) {
  gPlugins.util.log('Removing', gPlugins.util.colors.blue(dirs.app));
  del.sync([dirs.app]);

  callback();
});

gulp.task('get:version', ['sanitycheck', 'clean'], function(callback) {
  getVersion();

  callback();
});

gulp.task('copy:config', ['get:version'], function() {
  return gulp.src(['app.yaml', 'cron.yaml', 'php.ini'],
    {
      cwd: dirs.src
    })
    .pipe(gPlugins.template(taskConfig))
    .pipe(gulp.dest(dirs.app));
});

gulp.task('copy:wp', ['copy:config'], function() {
  var srcRoot = path.join(dirs.src, 'wp');

  return gulp.src('**/*',
    {
      cwd: srcRoot,
      dot: true
    })
    .pipe(gulp.dest(dirs.wordpress));
});

gulp.task('copy:wp-overridden', ['copy:wp'], function() {
  var srcRoot = path.join(dirs.src, 'wp-overridden');

  return gulp.src('**/*',
    {
      cwd: srcRoot,
      dot: true
    })
    .pipe(gulp.dest(dirs.wordpress));
});

gulp.task('build', ['copy:wp-overridden'], function(callback) {
  callback();
});

gulp.task('deploy', ['build'], function(callback) {
  if (taskConfig.environment === 'local') {
    gPlugins.util.log(gPlugins.util.colors.red('[Error]',
      'no need to deploy for local environment.'));
  } else {
    shellWrapper('appcfg.py update <%= app %>', dirs);
  }

  callback();
});
