module.exports = function(grunt) {

    require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        composerBin: 'vendor/bin',

        shell: {
            phpcs: {
                options: {
                    stdout: true
                },
                command: '<%= composerBin %>/phpcs -p --colors'
            },

            phpcbf: {
                options: {
                    stdout: true
                },
                command: '<%= composerBin %>/phpcbf'
            },

            phpunit: {
                options: {
                    stdout: true
                },
                command: '<%= composerBin %>/phpunit'
            },
        },

        gitinfo: {
            commands: {
                'local.tag.current.name': [ 'name-rev', '--tags', '--name-only', 'HEAD' ],
                'local.tag.current.nameLong': [ 'describe', '--tags', '--long' ]
            }
        },

        clean: {
            main: [ 'dist' ], //Clean up build folder
            i18n: [ 'languages/*.mo', 'languages/*.pot' ]
        },

        copy: {
            // Copy the plugin to a versioned release directory
            main: {
                src: [
                    '**',
                    '!*.xml', '!*.log', //any config/log files
                    '!node_modules/**', '!Gruntfile.js', '!package.json', //npm/Grunt
                    '!assets/**', //wp-org assets
                    '!dist/**', //build directory
                    '!.git/**', //version control
                    '!tests/**', '!scripts/**', '!phpunit.xml', '!phpunit.xml.dist', //unit testing
                    '!vendor/**', '!composer.lock', '!composer.phar', '!composer.json', //composer
                    '!.*', '!**/*~', //hidden files
                    '!CONTRIBUTING.md',
                    '!README.md',
                    '!phpcs.xml', '!phpcs.xml.dist', // CodeSniffer Configuration
                ],
                dest: 'dist/',
                options: {
                    processContentExclude: [ '**/*.{png,gif,jpg,ico,mo}' ],
                    processContent: function(content, srcpath) {
                        if (srcpath == 'readme.txt' || srcpath == 'woocommerce-local-pickup-time.php') {
                            if (grunt.config.get('gitinfo').local.tag.current.name !== 'undefined') {
                                content = content.replace('{{version}}', grunt.config.get('gitinfo').local.tag.current.name);
                            } else {
                                content = content.replace('{{version}}', grunt.config.get('gitinfo').local.tag.current.nameLong);
                            }
                        }
                        return content;
                    },
                },
            }
        },

        addtextdomain: {
            options: {
                textdomain: 'woocommerce-local-pickup-time',    // Project text domain.
            },
            update_all_domains: {
				        options: {
					        updateDomains: true
				        },
				        src: [ '*.php', '**/*.php', '!node_modules/**', '!tests/**', '!scripts/**' ]
			      },
        },

        wp_readme_to_markdown: {
            dest: {
                files: {
                    'README.md': 'readme.txt'
                }
            },
        },

        makepot: {
            target: {
                options: {
                    domainPath: '/languages',         // Where to save the POT file.
                    exclude: [
                        'node_modules/.*',				//npm
                        'assets/.*', 							//wp-org assets
                        'dist/.*', 								//build directory
                        '.git/.*', 								//version control
                        'tests/.*', 'scripts/.*',	//unit testing
                        'vendor/.*', 							//composer
                    ],                                // List of files or directories to ignore.
                    mainFile: 'woocommerce-local-pickup-time.php',                     // Main project file.
                    potFilename: 'woocommerce-local-pickup-time.pot',                  // Name of the POT file.
                    potHeaders: {
                        poedit: true,                 // Includes common Poedit headers.
                        'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
                    },                                // Headers to add to the generated POT file.
                    type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
                    updateTimestamp: true,            // Whether the POT-Creation-Date should be updated without other changes.
                    updatePoFiles: true               // Whether to update PO files in the same directory as the POT file.
                }
            }
        },

        po2mo: {
            plugin: {
                src: 'languages/*.po',
                expand: true
            }
        },

        checkrepo: {
            deploy: {
                tagged: true, // Check that the last commit (HEAD) is tagged
                clean: true // Check that working directory is clean
            }
        },

        checktextdomain: {
            options: {
                text_domain: 'woocommerce-local-pickup-time',
                keywords: [
										'__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_x:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ],
            },
            files: {
                src: [
                    '**/*.php',
                    '!node_modules/**',
                    '!dist/**',
                    '!tests/**',
                    '!vendor/**',
                    '!*~',
                ],
                expand: true,
            },
        },

        wp_deploy: {
            deploy: {
                options: {
                    plugin_slug: 'woocommerce-local-pickup-time-select',
                    build_dir: 'dist/',
                    assets_dir: 'assets/',
                    max_buffer: 1024 * 1024,
                    skip_confirmation: false,
                },
            }
        },

    });

    grunt.registerTask( 'phpcs', [ 'shell:phpcs' ] );
    grunt.registerTask( 'phpcbf', [ 'shell:phpcbf' ] );
    grunt.registerTask( 'phpunit', [ 'shell:phpunit' ] );
		grunt.registerTask( 'i18n', [ 'addtextdomain', 'makepot', 'po2mo' ] );
    grunt.registerTask( 'readme', [ 'wp_readme_to_markdown' ] );
    grunt.registerTask( 'test', [ 'checktextdomain', 'phpcs', 'phpunit' ] );
    grunt.registerTask( 'build', [ 'gitinfo', 'test', 'clean', 'i18n', 'readme', 'copy' ] );
    //grunt.registerTask( 'deploy', [ 'checkbranch:master', 'checkrepo', 'build', 'wp_deploy' ] );
    grunt.registerTask( 'deploy', [ 'checkrepo', 'build' ] );

};

