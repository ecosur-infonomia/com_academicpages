module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        jshint: {
            options: {
                curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                undef: true,
                boss: true,
                eqnull: true,
                browser: true
            },
            globals: {
                jQuery: true
            }
        },

        compress: {
            main: {
                options: {
                    mode: 'zip',
                    archive: 'dist/com_academicpages.zip'
                },
                files: [
                    {src: ['admin/**'], dest: 'com_academicpages'},
                    {src: ['site/**'], dest: 'com_academicpages'},
                    {src: ['index.html'], dest:'com_academicpages'},
                    {src: ['install.xml'], dest:'com_academicpages'}
                ]
            }
        },

        clean: ["dist"]

    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-compress');
    grunt.loadNpmTasks('grunt-contrib-qunit');
    grunt.loadNpmTasks('grunt-contrib-clean');
    
    // Default task(s).
    grunt.registerTask('default', ['clean', 'compress']);

};
