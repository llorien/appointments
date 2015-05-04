# Usage

    $ gulp clean

      remove `dist` directory.

    $ gulp {action} [--env {env}] [--version {version}]

    {action}: gulp task names: [build | deploy].
    {env}:    [development | production]. Default: development.
    {version}: [dev | refname]. Default: dev
               `dev`: the working tree. So, do not use `dev` to name branch and tag.
               `refname`: a valide git branch (either local or remote branch) or tag name.
