# Building Assets Required for concrete5

## css.php
### Requirements: php command line 5.1, `lessc` installed and in your $PATH (but the script verifies all the dependencies and suggests how to proceed).
This shell php script runs all .less files through lessc and places them where they need to be. It also minifies them.
Launch it `php css.php --help` for help & options.


## js.php
### Requirements: php command line 5.1, `uglify-js` installed and in your $PATH (but the script verifies all the dependencies and suggests how to proceed).
This shell php script runs all .js files in ccm_app and bootstrap and builds the proper ccm.app.js and bootstrap.js files.
Launch it `php js.php --help` for help & options.

## i18n.php
### Requirements: php command line 5.3, gettext tools (namely `xgettext`, `msgmerge` and `msgfmt`) installed and in your $PATH (but the script verifies all the dependencies and suggests how to proceed).
This shell php script manage localization files (.pot templates, .po translations and .mo compiled translations), for both the concrete5 core and for custom packages.
Launch it with `php i18n.php --help` for help & options, or with `php i18n.php --interactive` for an interactive session.