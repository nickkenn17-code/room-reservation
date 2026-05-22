importScripts('https://storage.googleapis.com/workbox-cdn/releases/7.0.0/workbox-sw.js');

// 1. The placeholder for the build tool
workbox.precaching.precacheAndRoute(self.__WB_MANIFEST);

// 2. Offline Fallback Logic
const OFFLINE_URL = '/offline.html';

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('offline-fallback').then((cache) => cache.add(OFFLINE_URL))
    );
});

// If a route fails (no network, no cache), serve offline.html
workbox.routing.setCatchHandler(({ event }) => {
    if (event.request.destination === 'document') {
        return caches.match(OFFLINE_URL);
    }
    return Response.error();
});




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
    },

    // Task 3: Inject Workbox precache manifest
    workbox: {
      inject: {
        options: {
          swSrc: 'sw.js',
          swDest: 'sw.js',
          globDirectory: './',
          globPatterns: [
            '**/*.{html,css,js,png,jpg}'
          ],
          templatedURLs: {
            '/': ['index.php']
          }
        }
      }
    }
  });

  // Load the plugins
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-workbox');

  // Default tasks
  grunt.registerTask('default', ['cssmin', 'workbox:inject', 'watch']);
  grunt.registerTask('build', ['cssmin', 'workbox:inject']);

  // This tells the Service Worker to listen for "navigation" requests
// If any page load fails, it serves the offline.html file
workbox.routing.setCatchHandler(({ event }) => {
    if (event.request.destination === 'document') {
        return caches.match('/offline.html');
    }
    return Response.error();
});
};