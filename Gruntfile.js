module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      scripts: {
        files: ['**/*.php'],
        tasks: ['phpunit'],
        options: {
          spawn: false,
        },
      },
    },
    phpunit: {
        classes: {
            dir: 'tests/'
        },
        options: {
            bin: 'vendor/bin/phpunit',
            colors: true
        }
    }
  });

  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('default', ['watch']);
};
