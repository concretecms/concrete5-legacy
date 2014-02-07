/* jshint -W002 */
module.exports = function(grunt, config, parameters, done) {
	try {
		var fs = require('fs'), path = require('path');
		var fnf;
		// Check webroot
		var webRoot;
		try {
			webRoot = fs.realpathSync(config.DIR_BASE);
			fnf = !fs.lstatSync(webRoot).isDirectory();
		}
		catch(e) {
			if(e.code === 'ENOENT') {
				fnf = true;
			}
			else {
				process.stderr.write(e);
				done(false);
				return;
			}
		}
		if(fnf) {
			process.stderr.write('"' + config.DIR_BASE + '" is not a directory.\n');
			done(false);
			return;
		}
		// Check package
		var pkg = parameters.package || '';
		if(!pkg.length) {
			process.stderr.write('Package handle not specified. Define a package variable in Gruntfile.parameters.js file or use a --package=... command line parameter.\n');
			done(false);
			return;
		}
		var pkgFolder;
		if(pkg.match(/^[\w\-]+$/)) {
			try {
				pkgFolder = path.join(webRoot, 'packages', pkg);
				fnf = !fs.lstatSync(pkgFolder).isDirectory();
			}
			catch(e) {
				if(e.code === 'ENOENT') {
					fnf = true;
				}
				else {
					process.stderr.write(e);
					done(false);
					return;
				}
			}
		}
		else {
			fnf = true;
		}
		if(fnf) {
			process.stderr.write('"' + pkg + '" is not a valid package handle.\n');
			done(false);
			return;
		}
		// Some common variables
		var c5fs = require('../libraries/fs');
		var phpFiles = [];
		var temp = require('temp');
		temp.track();
		var tempFolder = temp.mkdirSync('c5-');
		var execFile = require('child_process').execFile;
		var languagesFolder = path.join(pkgFolder, 'languages');
		var potFile = path.join(languagesFolder, 'messages.pot');
		var locales = null;
		// Let's define the main functions
		var listFiles = function(callback) {
			process.stdout.write('Listing .php files... ');
			var parser = new c5fs.directoryParser(pkgFolder);
			parser.onlyFilesWithExtension.push('.php');
			parser.onFile = function(cb, abs, rel) {
				phpFiles.push('packages/' + pkg + rel);
				cb();
			};
			parser.start(function(error) {
				if(!error && !phpFiles.length) {
					error = 'No .php files found in "' + pkgFolder + '".';
				}
				if(error) {
					process.stderr.write(error.message || error);
					done(false);
					return;
				}
				process.stdout.write('done.\n');
				callback();
			});
		};
		var createPotFile = function(callback) {
			var listFile = path.join(tempFolder, 'list');
			fs.writeFileSync(listFile, phpFiles.join('\n'));
			var potTempFile = path.join(tempFolder, 'pot');
			process.stdout.write('Creating temporary .pot file... ');
			execFile(
				'xgettext',
				[
					'--default-domain=messages', // Domain
					'--output=pot', // Output .pot file name
					'--output-dir=' + c5fs.escapeShellArg(tempFolder), // Output .pot folder name
					'--language=PHP', // Source files are in php
					'--from-code=UTF-8', // Source files are in utf-8
					'--add-comments=i18n', // Place comment blocks preceding keyword lines in output file if they start with '// i18n: '
					'--keyword', // Don't use default keywords
					'--keyword=t:1', // Look for the first argument of the "t" function for extracting translatable text in singular form
					'--keyword=t2:1,2', // Look for the first and second arguments of the "t2" function for extracting both the singular and plural forms
					'--keyword=tc:1c,2', // Look for the first argument of the "tc" function for extracting translation context, and the second argument is the translatable text in singular form
					'--no-escape', // Do not use C escapes in output
					'--add-location', // Generate '#: filename:line' lines
					'--no-wrap', // Do not break long message lines, longer than the output page width, into several lines
					'--files-from=' + c5fs.escapeShellArg(listFile), // Get list of input files from file
				],
				{
					cwd: webRoot
				},
				function(error) {
					if(error !== null) {
						process.stderr.write(error.message || error);
						done(false);
						return;
					}
					process.stdout.write('done.\n');
					process.stdout.write('Normalyzing .pot file... ');
					fs.readFile(potTempFile, {encoding: 'utf8'}, function(error, potData) {
						if(error !== null) {
							process.stderr.write(error.message || error);
							done(false);
							return;
						}
						// Normalize line endings
						potData = potData.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
						// Remove initial comments
						var potLines = potData.split('\n');
						var startIndex = -1;
						potLines.some(function(line, index) {
							if(line === 'msgid ""') {
								startIndex = index;
								return true;
							}
						});
						if(startIndex > 0) {
							potLines = potLines.splice(startIndex);
							potData = potLines.join('\n');
						}
						potData = potData
							// Remove useless headers
							.replace(/\n"PO-Revision-Date: YEAR-MO-DA HO:MI\+ZONE\\n"\n/m, '\n')
							.replace(/\n"Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n"\n/m, '\n')
							.replace(/\n"Language-Team: LANGUAGE <LL@li.org>\\n"\n/m, '\n')
							.replace(/\n"Language: \\n"\n/m, '\n')
							// Fill-in package handle
							.replace(/\n"Project-Id-Version: PACKAGE VERSION\\n"\n/m, '\n"Project-Id-Version: ' + pkg + '\\n"\n')
							// State that the charset is UTF-8
							.replace(/\n"Content-Type: text\/plain; charset=CHARSET\\n"\n/m, '\n"Content-Type: text/plain; charset=UTF-8\\n"\n')
						;
						// One location per line
						potLines = [];
						potData.split('\n').forEach(function(line) {
							var m;
							while((m = /^(#: .*?:\d+) (.*)$/.exec(line)) !== null) {
								potLines.push(m[1]);
								line = '#: ' + m[2];
							}
							potLines.push(line);
						});
						potData = potLines.join('\n');
						process.stdout.write('done.\n');
						process.stdout.write('Saving final .pot file... ');
						fs.writeFileSync(potFile, potData);
						process.stdout.write('done.\n');
						callback();
					});
				}
			);
		};
		var getLocaleList = function(callback) {
			process.stdout.write('Looking for locales to process... ');
			var fixLocaleCase = true, showLocalesHelp = false;
			if(parameters.locales instanceof Array) {
				locales = parameters.locales;
			}
			else if(typeof(parameters.locales) == 'string' && parameters.locales.length) {
				if(parameters.locales == '-') {
					locales = [];
				}
				else {
					locales = parameters.locales.split(',');
				}
			}
			if(locales === null) {
				locales = [];
				showLocalesHelp = true;
				fixLocaleCase = false;
				fs.readdirSync(languagesFolder).forEach(function(item) {
					var localeDir = path.join(languagesFolder, item, 'LC_MESSAGES');
					try {
						if(fs.lstatSync(localeDir).isDirectory()) {
							locales.push(item);
						}
					}
					catch(e) {
					}
				});
			}
			if(!locales.length) {
				process.stdout.write('none.\n');
				if(showLocalesHelp) {
					process.stdout.write('To create or update the .po files please specify a "locales" variable in Gruntfile.parameters.js file or use a --locales=... command line parameter.\n');
				}
				done(true);
				return;
			}
			if(fixLocaleCase) {
				locales.forEach(function(locale, index) {
					var m = /^(.*)_(.*)$/.exec(locale.replace(/-/, '_'));
					if(m) {
						locales[index] = m[1].toLowerCase() + '_' + m[2].toUpperCase();
					}
					else {
						locales[index] = locale.toLowerCase();
					}
				});
			}
			process.stdout.write(locales.length + ' found (' + locales.join(', ') + ').\n');
			callback();
		};
		var processNextLocale = function(localeIndex, callback) {
			if(localeIndex >= locales.length) {
				callback();
				return;
			}
			var locale = locales[localeIndex];
			process.stdout.write('Processing ' + locale + '\n');
			var localeDir = path.join(languagesFolder, locale, 'LC_MESSAGES'),
				poFile = path.join(localeDir, 'messages.po'),
				moFile = path.join(localeDir, 'messages.mo');
			var localeDirExist = false;
			try {
				if(fs.lstatSync(localeDir).isDirectory()) {
					localeDirExist = true;
				}
			}
			catch(e) {
			}
			if(!localeDirExist) {
				c5fs.mkdirRecursiveSync(localeDir);
			}
			var poExists = false;
			try {
				if(fs.lstatSync(poFile).isFile()) {
					poExists = true;
				}
			}
			catch(e) {
			}
			var moExists = false;
			try {
				if(fs.lstatSync(moFile).isFile()) {
					moExists = true;
				}
			}
			catch(e) {
			}
			function nextStep() {
				if(moExists && !poExists) {
					// We have only the .mo file: let's decompile it
					process.stdout.write('  # decompiling .mo file... ');
					execFile(
						'msgunfmt',
						[
							'--no-escape', // Do not use C escapes in output
							'--force-po', // Write PO file even if empty
							'--no-wrap', // Do not break long message lines, longer than the output page width, into several lines
							'--output-file=' + c5fs.escapeShellArg(poFile), // Write output to specified file
							c5fs.escapeShellArg(moFile) // Input .mo file
						],
						{},
						function(error) {
							if(error) {
								process.stderr.write(error.message || error);
								done(false);
								return;
							}
							process.stdout.write('done.\n');
							poExists = true;
							nextStep();
						}
					);
					return;
				}
				if(!poExists) {
					// We don't have neither the .po not the .mo file: let's create an empty .po file
					process.stdout.write('  # creating empty .po file... ');
					execFile(
						'msginit',
						[
							'--input=' + c5fs.escapeShellArg(potFile), // Input POT file
							'--locale=' + c5fs.escapeShellArg(locale), // Set target locale
							'--no-translator', // Declares that the PO file will not have a human translator and is instead automatically generated
							'--no-wrap', // Do not break long message lines, longer than the output page width, into several lines
							'--output-file=' + c5fs.escapeShellArg(poFile) // Write output to specified PO file
						],
						{},
						function(error) {
							if(error) {
								process.stderr.write(error.message || error);
								done(false);
								return;
							}
							process.stdout.write('done.\n');
							poExists = true;
							nextStep();
						}
					);
					return;
				}
				// Let's update the existing .po file with the .pot
				process.stdout.write('  # creating temporary .po file... ');
				var poTempFile = path.join(tempFolder, locale + '.po');
				execFile(
					'msgmerge',
					[
						'--no-fuzzy-matching', // Do not use fuzzy matching when an exact match is not found
						'--previous', // Keep the previous msgids of translated messages, marked with '#|', when adding the fuzzy marker to such messages
						'--lang=' + c5fs.escapeShellArg(locale), // Specify the 'Language' field to be used in the header entry
						'--force-po', // Always write an output file even if it contains no message
						'--add-location', // Generate '#: filename:line' lines
						'--no-wrap', // Do not break long message lines
						'--output-file=' + c5fs.escapeShellArg(poTempFile), // Write output to specified file
						c5fs.escapeShellArg(poFile), // Translations referring to old sources
						c5fs.escapeShellArg(potFile) // References to the new sources

					],
					{},
					function(error) {
						if(error) {
							process.stderr.write(error.message || error);
							done(false);
							return;
						}
						process.stdout.write('done.\n');
						process.stdout.write('  # normalizing data... ');
						fs.readFile(poTempFile, {encoding: 'utf8'}, function(error, poData) {
							poData = poData.replace(/\r\n/g, '\n').replace(/\r/g, '\n');
							// One location per line
							var poLines = [];
							poData.split('\n').forEach(function(line) {
								var m;
								while((m = /^(#: .*?:\d+) (.*)$/.exec(line)) !== null) {
									poLines.push(m[1]);
									line = '#: ' + m[2];
								}
								poLines.push(line);
							});
							poData = poLines.join('\n');
							process.stdout.write('done.\n');
							process.stdout.write('  # Saving final .po file... ');
							fs.writeFileSync(poFile, poData);
							process.stdout.write('done.\n');
							process.stdout.write('done.\n');
							processNextLocale(localeIndex + 1, callback);
						});
					}
				);
				return;
			}
			nextStep();
		};
		listFiles(function() {
			try {
				fs.lstatSync(languagesFolder);
			}
			catch(error) {
				if(error.code !== 'ENOENT') {
					process.stderr.write(error.message || error);
					done(false);
					return;
				}
				process.stdout.write('Creating languages folder... ');
				try {
					fs.mkdirSync(languagesFolder);
				}
				catch(error) {
					process.stderr.write(error.message || error);
					done(false);
					return;
				}
				process.stdout.write('done.\n');
			}
			createPotFile(function() {
				getLocaleList(function() {
					processNextLocale(0, function() {
						done(true);
					});
				});
			});
		});
	}
	catch(e) {
		process.stderr.write(e.message || e);
		done(false);
	}
};
