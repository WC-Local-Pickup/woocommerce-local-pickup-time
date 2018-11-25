module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
      
        addtextdomain: {
            options: {
                textdomain: 'woocommerce-local-pickup-time',    // Project text domain.
            },
            update_all_domains: {
				        options: {
					        updateDomains: true
				        },
				        src: [ '*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**' ]
			      },
        },
      
        wp_readme_to_markdown: {
            your_target: {
                files: {
                    'README.md': 'readme.txt'
                }
            },
        },

        makepot: {
            target: {
                options: {
                    domainPath: '/languages',         // Where to save the POT file.
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
    });

    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-po2mo' );
    grunt.loadNpmTasks( 'grunt-wp-readme-to-markdown' );

		grunt.registerTask( 'i18n', ['addtextdomain', 'makepot', 'po2mo'] );
    grunt.registerTask( 'readme', ['wp_readme_to_markdown'] );
    //grunt.registerTask( 'default', ['makepot', 'po2mo'] );
};

