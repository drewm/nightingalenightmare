/* global module:false */
module.exports = function(grunt) {
	var port = grunt.option('port') || 8000;
	var base = grunt.option('base') || '.';

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		meta: {
			banner:
				'/*!\n' +
				' * reveal.js <%= pkg.version %> (<%= grunt.template.today("yyyy-mm-dd, HH:MM") %>)\n' +
				' * http://lab.hakim.se/reveal-js\n' +
				' * MIT licensed\n' +
				' *\n' +
				' * Copyright (C) 2016 Hakim El Hattab, http://hakim.se\n' +
				' */'
		},

		qunit: {
			files: [ 'test/*.html' ]
		},

		uglify: {
			options: {
				banner: '<%= meta.banner %>\n'
			},
			build: {
				src: 'public/js/reveal.js',
				dest: 'public/js/reveal.min.js'
			}
		},

		sass: {
			core: {
				files: {
					'public/css/reveal.css': 'public/css/reveal.scss',
				}
			},
			themes: {
				files: [
					{
						expand: true,
						cwd: 'public/css/theme/source',
						src: ['*.scss'],
						dest: 'public/css/theme',
						ext: '.css'
					}
				]
			}
		},

		autoprefixer: {
			dist: {
				src: 'public/css/reveal.css'
			}
		},

		cssmin: {
			compress: {
				files: {
					'public/css/reveal.min.css': [ 'public/css/reveal.css' ]
				}
			}
		},

		jshint: {
			options: {
				curly: false,
				eqeqeq: true,
				immed: true,
				latedef: true,
				newcap: true,
				noarg: true,
				sub: true,
				undef: true,
				eqnull: true,
				browser: true,
				expr: true,
				globals: {
					head: false,
					module: false,
					console: false,
					unescape: false,
					define: false,
					exports: false
				}
			},
			files: [ 'Gruntfile.js', 'public/js/reveal.js' ]
		},

		connect: {
			server: {
				options: {
					port: port,
					base: base,
					livereload: true,
					open: true
				}
			}
		},

		zip: {
			'reveal-js-presentation.zip': [
				'public/index.html',
				'public/css/**',
				'public/js/**',
				'public/lib/**',
				'public/images/**',
				'public/plugin/**',
				'public/**.md'
			]
		},

		watch: {
			js: {
				files: [ 'Gruntfile.js', 'public/js/reveal.js' ],
				tasks: 'js'
			},
			theme: {
				files: [ 'public/css/theme/source/*.scss', 'public/css/theme/template/*.scss' ],
				tasks: 'css-themes'
			},
			css: {
				files: [ 'public/css/reveal.scss' ],
				tasks: 'css-core'
			},
			html: {
				files: [ '*.html']
			},
			markdown: {
				files: [ '*.md' ]
			},
			options: {
				livereload: true
			}
		}

	});

	// Dependencies
	grunt.loadNpmTasks( 'grunt-contrib-qunit' );
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-contrib-connect' );
	grunt.loadNpmTasks( 'grunt-autoprefixer' );
	grunt.loadNpmTasks( 'grunt-zip' );

	// Default task
	grunt.registerTask( 'default', [ 'css', 'js' ] );

	// JS task
	grunt.registerTask( 'js', [ 'jshint', 'uglify' ] );

	// Theme CSS
	grunt.registerTask( 'css-themes', [ 'sass:themes' ] );

	// Core framework CSS
	grunt.registerTask( 'css-core', [ 'sass:core', 'autoprefixer', 'cssmin' ] );

	// All CSS
	grunt.registerTask( 'css', [ 'sass', 'autoprefixer', 'cssmin' ] );

	// Package presentation to archive
	// grunt.registerTask( 'package', [ 'default', 'zip' ] );

	// Serve presentation locally
	// grunt.registerTask( 'serve', [ 'connect', 'watch' ] );

	// Run tests
	// grunt.registerTask( 'test', [ 'jshint', 'qunit' ] );

};
