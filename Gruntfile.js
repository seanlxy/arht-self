module.exports = function(grunt){

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		concat:{
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
				separator: ';'
			},
			dist: {
				src:[
					'assets/js/libs/unmin/jquery-2.2.2.js',
					'assets/js/libs/unmin/bootstrap.js',
					'assets/js/libs/unmin/slick.js',
					'assets/js/libs/unmin/photoswipe.js',
					'assets/js/libs/unmin/photoswipe-ui-default.js',
					'assets/js/libs/unmin/shopify.js',
				],
				dest: 'assets/js/libs/min/vendor.js'
			}
		},
		uglify:{
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build: {
				files: [{
		            src: 'assets/js/libs/min/vendor.js',
		            dest: 'assets/js/libs/min/vendor.js'
		        }, {
		            src: 'assets/js/unmin/main.js',
		            dest: 'assets/js/min/main.js'
		        }]
			}
		},
		sass: {
			dist: {
				files: {
					'assets/css/_main_xl.css' : 'assets/sass/main.scss'
				}
			}
		},
		cssmin: {
			options: {
				shorthandCompacting: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'assets/css/main.css': 'assets/css/_main_xl.css'
				}
			}
		},
		watch: {
			scripts:{
				files: ['assets/js/libs/unmin/*.js', 'assets/js/unmin/main.js', 'Gruntfile.js'],
				tasks: ['concat', 'uglify'],
	      	}, 
	      	css: {
				files: 'assets/sass/*.scss',
				tasks: ['sass', 'cssmin']
			}
	    }

	});

	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-watch');
	

	// Default task(s).
	grunt.registerTask('default', ['watch']);

};