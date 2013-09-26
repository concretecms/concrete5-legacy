module.exports = function(grunt) {
	var config = {}, extend = require('util')._extend;

	config.rootWeb = '';
	config.rootPath = '../web';

	var uglifyTargets = {
		bootstrap: {
			options: {
				sourceMap: '<%= rootPath %>/concrete/js/bootstrap.map',
				sourceMappingURL: '<%= rootWeb %>/concrete/js/bootstrap.map',
				sourceMapRoot: '<%= rootWeb %>/concrete/js/bootstrap'
			},
			files: {
				'<%= rootPath %>/concrete/js/bootstrap.js': [
					'<%= rootPath %>/concrete/js/bootstrap/bootstrap.tooltip.js',
					'<%= rootPath %>/concrete/js/bootstrap/bootstrap.popover.js',
					'<%= rootPath %>/concrete/js/bootstrap/bootstrap.dropdown.js',
					'<%= rootPath %>/concrete/js/bootstrap/bootstrap.transitions.js',
					'<%= rootPath %>/concrete/js/bootstrap/bootstrap.alert.js'
				]
			},
		},
		jquery_cookie: {
			options: {
				sourceMap: '<%= rootPath %>/concrete/js/jquery.cookie.map',
				sourceMappingURL: '<%= rootWeb %>/concrete/js/jquery.cookie.map',
				sourceMapRoot: '<%= rootWeb %>/concrete/js/ccm_app'
			},
			files: {
				'<%= rootPath %>/concrete/js/jquery.cookie.js': [
					'<%= rootPath %>/concrete/js/ccm_app/jquery.cookie.js'
				]
			}
		},
		ccm_dashboard: {
			options: {
				sourceMap: '<%= rootPath %>/concrete/js/ccm.dashboard.map',
				sourceMappingURL: '<%= rootWeb %>/concrete/js/ccm.dashboard.map',
				sourceMapRoot: '<%= rootWeb %>/concrete/js/ccm_app'
			},
			files: {
				'<%= rootPath %>/concrete/js/ccm.dashboard.js': [
					'<%= rootPath %>/concrete/js/ccm_app/dashboard.js'
				] 
			}
		},
		ccm_app: {
			options: {
				sourceMap: '<%= rootPath %>/concrete/js/ccm.app.map',
				sourceMappingURL: '<%= rootWeb %>/concrete/js/ccm.app.map',
				sourceMapRoot: '<%= rootWeb %>/concrete/js/ccm_app'
			},
			files: {
				'<%= rootPath %>/concrete/js/ccm.app.js': [
					'<%= rootPath %>/concrete/js/ccm_app/jquery.colorpicker.js',
					'<%= rootPath %>/concrete/js/ccm_app/jquery.hoverIntent.js',
					'<%= rootPath %>/concrete/js/ccm_app/jquery.liveupdate.js',
					'<%= rootPath %>/concrete/js/ccm_app/jquery.metadata.js',
					'<%= rootPath %>/concrete/js/ccm_app/chosen.jquery.js',
					'<%= rootPath %>/concrete/js/ccm_app/filemanager.js',
					'<%= rootPath %>/concrete/js/ccm_app/jquery.cookie.js',
					'<%= rootPath %>/concrete/js/ccm_app/layouts.js',
					'<%= rootPath %>/concrete/js/ccm_app/legacy_dialog.js',
					'<%= rootPath %>/concrete/js/ccm_app/newsflow.js',
					'<%= rootPath %>/concrete/js/ccm_app/page_reindexing.js',
					'<%= rootPath %>/concrete/js/ccm_app/quicksilver.js',
					'<%= rootPath %>/concrete/js/ccm_app/remote_marketplace.js',
					'<%= rootPath %>/concrete/js/ccm_app/search.js',
					'<%= rootPath %>/concrete/js/ccm_app/sitemap.js',
					'<%= rootPath %>/concrete/js/ccm_app/status_bar.js',
					'<%= rootPath %>/concrete/js/ccm_app/tabs.js',
					'<%= rootPath %>/concrete/js/ccm_app/tinymce_integration.js',
					'<%= rootPath %>/concrete/js/ccm_app/ui.js',
					'<%= rootPath %>/concrete/js/ccm_app/toolbar.js',
					'<%= rootPath %>/concrete/js/ccm_app/themes.js',
					'<%= rootPath %>/concrete/js/ccm_app/composer.js'
				]
			}
		}
	};
	config.uglify = {
		options: {
			mangle: false,
			compress: true,
			beautify: false,
			report: 'min',
			preserveComments: false,
			banner: '',
			footer: '',
			sourceMapPrefix: 4
		}
	};
	var uglifyTargets_debug = [], uglifyTargets_production = []
	for(var uglifyTarget in uglifyTargets) {
		var uglifyTargetDebug = extend({}, uglifyTargets[uglifyTarget]);
		uglifyTargets_debug.push('uglify:' + uglifyTarget + '_debug');
		config.uglify[uglifyTarget + '_debug'] = uglifyTargetDebug;
		var uglifyTargetProduction = extend({}, uglifyTargets[uglifyTarget]);
		delete uglifyTargetProduction.options;
		uglifyTargets_production.push('uglify:' + uglifyTarget + '_production');
		config.uglify[uglifyTarget + '_production'] = uglifyTargetProduction;
	}
	
	var lessFiles = {
		'<%= rootPath %>/concrete/css/jquery.ui.css': '<%= rootPath %>/concrete/css/ccm_app/build/jquery.ui.less',
		'<%= rootPath %>/concrete/css/jquery.rating.css': '<%= rootPath %>/concrete/css/ccm_app/build/jquery.rating.less',
		'<%= rootPath %>/concrete/css/ccm.default.theme.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.default.theme.less',
		'<%= rootPath %>/concrete/css/ccm.dashboard.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.dashboard.less',
		'<%= rootPath %>/concrete/css/ccm.dashboard.1200.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.dashboard.1200.less',
		'<%= rootPath %>/concrete/css/ccm.colorpicker.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.colorpicker.less',
		'<%= rootPath %>/concrete/css/ccm.app.mobile.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.app.mobile.less',
		'<%= rootPath %>/concrete/css/ccm.app.css': '<%= rootPath %>/concrete/css/ccm_app/build/ccm.app.less'
	};
	config.less = {
		options: {
			compress: false,
			yuicompress: false,
			ieCompat: true,
			optimization: null,
			strictImports: false,
			syncImport: false,
			dumpLineNumbers: false,
			relativeUrls: false,
			report: 'min'
		},
		debug: {
			options: {
				compress: true,
				yuicompress: false
			},
			files: lessFiles
		},
		production: {
			options: {
				compress: true,
				yuicompress: true
			},
			files: lessFiles
		},
	};
	

	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.initConfig(config);
	grunt.registerTask('uglify:debug', uglifyTargets_debug);
	grunt.registerTask('uglify:production', uglifyTargets_production);
	grunt.registerTask('debug', ['less:debug', 'uglify:debug']);
	grunt.registerTask('production', ['less:production', 'uglify:production']);
	grunt.registerTask('default', 'production');
};