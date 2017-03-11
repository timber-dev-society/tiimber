module.exports = function(grunt) {

  var fileWatch = [
    'src/*.php',
    'src/**/*.php',
    'tests/*.php',
    'tests/**/*.php'
  ]

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      php: {
        files: fileWatch,
        tasks: ['phpunit'],
        options: {
          spawn: false,
        },
      },
      phpDocker: {
        files: fileWatch,
        tasks: ['exec'],
        options: {
          spawn: false,
        },
      }
    },
    phpunit: {
        classes: {
            dir: 'tests/'
        },
        options: {
            bin: 'vendor/bin/phpunit',
            colors: true
        }
    },
    exec: {
        docker: 'docker-compose up'
    }
  });

  grunt.loadNpmTasks('grunt-phpunit');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-exec');

  grunt.registerTask('default', ['watch:php']);
  grunt.registerTask('watch-docker', ['watch:phpDocker']);
};
