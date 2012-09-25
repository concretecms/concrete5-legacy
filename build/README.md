# Building Assets Required for concrete5

## css.php
### Requirements: You must have "lessc" installed and in your $PATH in order for this script to work.
Launching this script verifies all the dependencies and suggests how to install them.
This shell php script runs all .less files through lessc and places them where they need to be. It also minifies them.
Call `php css.php --help` for options.


## js.php
### Requirements: You must have "uglify-js" installed and in your $PATH in order for this script to work.
Launching this script verifies all the dependencies and suggests how to install them.
This shell php  script runs all .js files in ccm_app/ and bootstrap and builds the proper ccm.app.js and bootstrap.js files.
Call `php js.php --help` for options.