module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      css: {
        files: [
          '**/*.sass',
          '**/*.scss'
        ],
        tasks: ['sass', 'concat', 'cssmin', 'copy']
      },
      js: {
        files: [
          'assets/js/main.js',
          'Gruntfile.js'
        ],
        tasks: ['jshint', 'concat', 'uglify', 'copy']
      }
    },
    concat: {
      options: {
        separator: '\n',
      },
      js: {
        src: [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/bootstrap/dist/js/bootstrap.min.js',
          'node_modules/@fortawesome/fontawesome-free/js/all.min.js',
          'node_modules/toastr/build/toastr.min.js',
          'node_modules/nette.ajax.js/nette.ajax.js',
          'node_modules/nette.ajax.js/extensions/confirm.ajax.js',
          'node_modules/chart.js/dist/chart.min.js',
          'vendor/nette/forms/src/assets/netteForms.min.js',
          'assets/js/main.js'
        ],
        dest: 'temp/js/app.js',
      },
      css: {
        src: [
          'node_modules/bootstrap/dist/css/bootstrap.min.css',
          'node_modules/toastr/build/toastr.min.css',
          'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
          'temp/css/style.css'
        ],
        dest: 'temp/css/app.css',
      }
    },
    sass: {
      main: {       
        files: [{
          expand: true,
          cwd: 'assets/scss/',
          src: ['**/*.scss'],
          dest: 'temp/css',
          ext: '.css'
        }]
      }
    },
    copy: {
      fontawesomeFree: {
        expand: true,
        cwd: 'node_modules/@fortawesome/fontawesome-free/webfonts',
        src: '**',
        dest: 'www/webfonts/',
      }
    },
    uglify: {
      app: {
        files: {
          'www/js/app.min.js': ['temp/js/app.js']
        }
      }
    },
    cssmin: {
      target: {
        files: {
          'www/css/app.min.css': ['temp/css/app.css']
        }
      }
    },
    jshint: {
      options: {
        jshintrc: '.jshintrc'
      },
      all: ['Gruntfile.js', 'assets/js/main.js']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-sass-scss');

  grunt.registerTask('default', ['sass', 'jshint', 'concat', 'copy', 'uglify', 'cssmin', 'watch']);
};
