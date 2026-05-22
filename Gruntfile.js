module.exports = function(grunt) {
  const workboxBuild = require('workbox-build');

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

    // Task 2: Watch for changes
    watch: {
      css: {
        files: ['assets/css/style.css'],
        tasks: ['cssmin']
      }
    }
  });

  // Load the standard plugins
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Task 3: The Workbox Build Plugin Requirement
  grunt.registerTask('workbox', 'Generates the Service Worker with Precaching', function() {
    const done = this.async();
    workboxBuild.injectManifest({
      swSrc: 'sw-template.js', // The template file
      swDest: 'sw.js',         // The final output file used by the browser
      globDirectory: '.',
      globPatterns: [
        'assets/css/*.min.css',
        'assets/images/*.{png,jpg,jpeg}',
        'manifest.json'
      ],
      globIgnores: [
        'node_modules/**'
      ],
      templatedURLs: {
        '/': ['index.php'] // Precaches the root route
      }
    }).then(({count, size, warnings}) => {
      warnings.forEach(console.warn);
      console.log(`J.A.R.V.I.S.: Successfully injected ${count} files, totaling ${size} bytes into sw.js.`);
      done();
    }).catch((err) => {
      console.error(`J.A.R.V.I.S.: Error generating service worker.`, err);
      done(false);
    });
  });

  // Default task now runs cssmin, generates the service worker, then starts watching
  grunt.registerTask('default', ['cssmin', 'workbox', 'watch']);
};