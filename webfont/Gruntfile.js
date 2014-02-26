module.exports = function(grunt) {
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		watch: {
			tasks: [ 'webfont' ],
			files: [ 'svg/*' ]
		},

		webfont: {
			icons: {
				src: 'svg/*.svg',
				dest: 'build',
				options: {
					font: 'forum-icons',
					hashes: false,
					stylesheet: 'css',
					relativeFontPath: '../font/',
					template: 'template/custom.css'
				}
			}
		}
	});

	grunt.loadTasks('build');
	
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-webfont');

	grunt.registerTask('default', [ 'webfont' ]);
};
