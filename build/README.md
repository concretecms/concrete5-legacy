# Building Assets Required for concrete5


## Requirements
In order to build assets for concrete5 you need:
- [Node.js](http://nodejs.org/)
- [npm](http://npmjs.org/) (may be bundled with Node.js)

Once you have node.js and npm, you have to install the [grunt](http://gruntjs.com/) client.
You can install it globally with `npm install -g grunt-cli`. This requires that you may need to use sudo (for OS X, *nix, BSD, …) or run your command shell as Administrator (for Windows).
If you don't have administrator rights, you may need to install the grunt client locally to your project using `npm install grunt-cli`.
Unfortunately, this will not put the grunt executable in your PATH, so you'll need to specify its explicit location when executing it (for OS X, *nix, BSD `./node_modules/.bin/grunt`, for Windows `node_modules\.bin\grunt`).

Once you have installed the grunt client, you need to install the project dependencies: simply launch the following command: `npm install grunt grunt-contrib-uglify grunt-contrib-less grunt-contrib-cssmin` 


## Building .css files

For production: `grunt less:production`
For debugging: `grunt less:debug`


## Building .js files

`grunt uglify`


## Building everything

Simply with `grunt`


## Debugging JavaScript with source maps

If you have installed concrete5 in a sub-directory and you want to debug JavaScript with sourcemaps, you should update the `Gruntfile.js` file, changing the line `config.rootWeb = '';`.
For instance, if your concrete5 installation is at http://www.domain.com/c5subfolder, you should have `config.rootWeb = '/c5subfolder';`.