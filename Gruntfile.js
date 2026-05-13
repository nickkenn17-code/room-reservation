module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Task 1: Minify CSS
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'assets/css',
          src: ['*.css', '!*.min.css'],
          dest: 'assets/css',
          ext: '.min.css'
        }]
      }
    },

    // Task 2: Watch for changes and auto-run
    watch: {
      css: {
        files: ['assets/css/style.css'],
        tasks: ['cssmin']
      }
    }
  });

  // Load the plugins
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default tasks
  grunt.registerTask('default', ['cssmin', 'watch']);
};