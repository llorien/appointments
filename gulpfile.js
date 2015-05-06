'use strict';

var gulp = require('gulp');
var gPlugins = require('gulp-load-plugins')();

var path = require('path');
var fs = require('fs');
var shell = require('shelljs');
var del = require('del');

var _ = require('lodash');

var taskConfig = {
    productCode: 'brithon.com'
};

var environments = [
    'development',
    'production'
];

var bases = {
    repo: __dirname,
    dist: path.join(__dirname, 'dist/')
};

function getDirs() {
    return {
        buildRoot: path.join(bases.dist, 'build'),
        releaseRoot: path.join(bases.dist, 'release'),
        zipRoot: path.join(bases.dist, 'zip')
    };
}

function normalizeVersion(version) {
    // '/' is allowed in branch and tag names, so escape it for path buidling.
    // we also escape bad chars for file name.
    return version.replace(/[\/\\:\?\*\|'"# ]/g, '_');
}

gulp.task('clean', function() {
    gPlugins.util.log('Removing', bases.dist);
    // Do use sync, otherwise dest files will be in trouble.
    del.sync([bases.dist]);
});

gulp.task('default', function() {
    fs.readFile('README.md', 'utf8', function (err, data) {
        if (err) {
            throw err;
        }
        console.log(data);
    });
});

function shellWrapper(cmdTemplate, data) {
    return shell.exec(_.template(cmdTemplate)(data));
}

function isVersioninRepo(version, repo) {
    var res = shellWrapper('git -C <%= repo %> show-ref <%= ref %>',
                                  {repo: repo,
                                   ref: version});
    return (0 == res.code);
}

var VERSION_TYPE = {
    DEV: 0,
    TAG: 1,
    BRANCH: 2,
    UNKNOWN: 3
};

function getVersionType(version, repo) {
    if (version === 'dev') {
        return VERSION_TYPE.DEV;
    }

    var isTag = shellWrapper('git -C <%= repo %> show-ref --tags <%= ref %>',
                             {repo: repo,
                              ref: version});
    if (isTag.code === 0) {
        return VERSION_TYPE.TAG;
    }

    var isBranch = shellWrapper('git -C <%= repo %> show-ref <%= ref %>',
                                {repo: repo,
                                 ref: version});
    if (isBranch.code === 0) {
        return VERSION_TYPE.BRANCH;
    }

    return VERSION_TYPE.UNKNOWN;
}

gulp.task('sanitycheck', function() {
    taskConfig = _.assign(taskConfig, {
        env: (gPlugins.util.env.env || 'development').toLowerCase(),
        version: (gPlugins.util.env.version || 'dev').toString().toLowerCase()
    });

    if (! _.includes(environments, taskConfig.env)) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'unknown env:',
                                                   taskConfig.env));
        process.exit(1);
    }

    if (taskConfig.version !== 'dev') {
        var repo = path.join(bases.repo);

        if (!isVersioninRepo(taskConfig.version, repo)) {
            gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                       'unknown version:',
                                                       taskConfig.version));
            process.exit(1);
        }
    }
});

function getVersionInfo(repo, version) {
    version = (typeof version !== 'undefined') ?  version : 'HEAD';

    // get SHA1
    var sha1 = shellWrapper('git -C <%= repo %> rev-parse --short=7 <%= version %>',
                            {repo: repo,
                             version: version});
    if (sha1.code) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'get SHA1 failed:',
                                                   sha1.output));
        process.exit(1);
    }

    // get branch name
    var branch = shellWrapper('git -C <%= repo %> rev-parse --abbrev-ref <%= version %>',
                              {repo: repo,
                               version: version});
    if (branch.code) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'get branch name failed:',
                                                   branch.output));
        process.exit(1);
    }

    return [branch.output.trim(), sha1.output.trim()];
};

function checkoutRev(repo, revision, target) {
    // populate the revision to build dir.
    var res = shellWrapper('mkdir -p <%= target %> && git -C <%= repo %> archive --format=tar <%= revision %> | tar -x -C <%= target %>',
                           {repo: repo,
                            revision: revision,
                            target: target});
    if (res.code) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'checkoutRev failed:',
                                                   res.output));
        process.exit(1);
    }
}

function refactorVersion() {
    var versionType = getVersionType(taskConfig.version, bases.repo);

    if (versionType === VERSION_TYPE.UNKNOWN) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'unknown version (tag or branch required):',
                                                   taskConfig.version));
        process.exit(1);
    }

    taskConfig.versionType = versionType;
    if (versionType === VERSION_TYPE.DEV) {
        return;
    }

    var res = shellWrapper('git -C <%= repo %> rev-parse --abbrev-ref <%= ref %>',
                           {repo: bases.repo,
                            ref: taskConfig.version});
    if (res.code) {
        gPlugins.util.log(gPlugins.util.colors.red('[Error]',
                                                   'refactor version failed:',
                                                   res.output));
        process.exit(1);
    }

    taskConfig.version = res.output.trim();
}

function populateConfig() {
    var res = null;

    switch (taskConfig.versionType) {
    case VERSION_TYPE.DEV:
        res = getVersionInfo(bases.repo);
        taskConfig.normalizedVersion = [res[0],
                                        res[1],
                                        'DEV',
                                        gPlugins.util.date('yyyymmdd')].join('.');
        break;
    case VERSION_TYPE.BRANCH:
        res = getVersionInfo(bases.repo, taskConfig.version);
        taskConfig.normalizedVersion = [res[0],
                                        res[1],
                                        gPlugins.util.date('yyyymmdd')].join('.');
        break;
    case VERSION_TYPE.TAG:
        taskConfig.normalizedVersion = taskConfig.version;
        break;
    default:
        break;
    }

    taskConfig.normalizedVersion = normalizeVersion(taskConfig.normalizedVersion);

    taskConfig.releaseName = [taskConfig.productCode, taskConfig.normalizedVersion].join('-');
    taskConfig.zipName = [taskConfig.releaseName, 'zip'].join('.');

    taskConfig.dirs.releaseDir = path.join(taskConfig.dirs.releaseRoot, taskConfig.releaseName);
    if (taskConfig.versionType === VERSION_TYPE.DEV) {
        taskConfig.dirs.buildDir = bases.repo;
    } else {
        taskConfig.dirs.buildDir = path.join(taskConfig.dirs.buildRoot, taskConfig.releaseName);
    }
};

function checkoutCode() {
    if (taskConfig.versionType !== VERSION_TYPE.DEV) {
        checkoutRev(bases.repo, taskConfig.version, taskConfig.dirs.buildDir);
    }
}

gulp.task('initialize', ['sanitycheck'], function() {
    taskConfig.dirs = getDirs();
    refactorVersion();
    populateConfig();
});

gulp.task('clean:current', ['initialize'], function() {
    del.sync([taskConfig.dirs.releaseDir]);
    if (taskConfig.versionType !== VERSION_TYPE.DEV) {
        del.sync([taskConfig.dirs.buildDir]);
    }
});

gulp.task('checkout', ['clean:current'], function() {
    checkoutCode();
});

gulp.task('copy:wp', ['checkout'], function() {
    return gulp.src('wp/**/*',
                    {cwd: taskConfig.dirs.buildDir})
        .pipe(gulp.dest(taskConfig.dirs.releaseDir));
});

gulp.task('copy:wp-overridden', ['copy:wp'], function() {
    var root = path.join('wp-overridden', taskConfig.env);

    return gulp.src(root + '/**/*',
                    {cwd: taskConfig.dirs.buildDir,
                     dot: true})
        .pipe(gulp.dest(taskConfig.dirs.releaseDir));
});

gulp.task('zip', ['copy:wp-overridden'], function() {
    return gulp.src('**/*',
                    {cwd: taskConfig.dirs.releaseDir,
                     dot: true})
        .pipe(gPlugins.zip(taskConfig.zipName))
        .pipe(gulp.dest(taskConfig.dirs.zipRoot));
});

gulp.task('build', ['zip'], function() {
    gPlugins.util.log('release package:', taskConfig.dirs.releaseDir);
    gPlugins.util.log('release zip:    ', path.join(taskConfig.dirs.zipRoot,
                                                    taskConfig.zipName));
});
          
gulp.task('deploy', ['build'], function() {
    var isDeployed = shellWrapper('(cd <%= releaseDir %> && eb deploy -l <%= versionLabel %>)',
                                  {releaseDir: taskConfig.dirs.releaseDir,
                                   versionLabel: taskConfig.releaseName});

    if (isDeployed.code === 0) {
        gPlugins.util.log('😎 ',
                          gPlugins.util.colors.bgBlue.white.bold(' Deploy succeeded. '));
    } else {
        gPlugins.util.log('😱 ',
                          gPlugins.util.colors.bgRed.white.bold(' Deploy failed! '));
    }
});

/*
   Hierarachy of build directories.
   Corresponding varabile names are listed after #.

brithon.com/ # pwd, bases.repo
|-- gulpfile.js
|-- package.json
`-- dist/    # bases.dist
    |-- zip/ # zipRoot
    |   `-- brithon.com-X.Y.Z.zip 
    |-- release/ # releaseRoot
    |   `-- brithon.com-X.Y.Z/ # releaseDir 
    `-- build/ # buildRoot
        `-- brithon.com@X.Y.Z/ # buildDir
*/
