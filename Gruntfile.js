module.exports = function(grunt) {

    // 1. Вся настройка находится здесь
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: 'public/css/source/',
                    src: ['*.css'],
                    dest: 'public/css/',
                    ext: '.min.css'
                }]
            }
        },
        uglify: {
            target: {
                files: [{
                    expand: true,
                    cwd: 'public/js/source/',
                    src: ['*.js'],
                    dest: 'public/js/min/',
                    ext: '.min.js'
                }]
            }
        },

        watch: {
            scripts: {
                files: ['public/css/source/*.css','public/js/source/*.js'],
                tasks: ['cssmin','uglify'],
                options: {
                    spawn: false
                }
            }
        }

    });

    // 3. Тут мы указываем Grunt, что хотим использовать этот плагин
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // 4. Указываем, какие задачи выполняются, когда мы вводим «grunt» в терминале
    grunt.registerTask('default', ['cssmin','uglify','watch']);

};