// gulpfile.js

var gulp = require('gulp');
var browserify = require('browserify');
var babelify = require('babelify');
var source = require('vinyl-source-stream');
var concat = require('gulp-concat');
var notify = require('gulp-notify');
var uglify = require('gulp-uglify');
var buffer = require('vinyl-buffer');

//var watcher = gulp.watch('app/components/*.js');
//
//watcher.on('change', function(event) {
//    console.log('File ' + event.path + ' was ' + event.type + ', running tasks...');
//
//
//});
//
//gulp.task('build', function () {
//    browserify({
//        entries: './app/components/main.js',
//        extensions: ['./app/components/*.js'],
//        debug: true
//    })
//        .transform(babelify)
//        .bundle()
//        .pipe(source('bundle.js'))
//        .pipe(gulp.dest('public/js/build'))
//        .pipe(notify({ message: "Job's Done"}));
//});
//
//gulp.task('compress', function() {
//    return gulp.src('./public/js/build/*.js')
//        .pipe(uglify())
//        .pipe(gulp.dest('./public/js/dist'));
//});


gulp.watch('app/components/*.js', ['browserify']);

gulp.task('browserify', function() {
    return browserify('./app/components/main.js')
        .transform(babelify)
        .bundle()
        .pipe(source('bundle.js')) // gives streaming vinyl file object
        .pipe(buffer()) // <----- convert from streaming to buffered vinyl file object
        .pipe(uglify()) // now gulp-uglify works
        .pipe(gulp.dest('./public/js/dist'))
        .pipe(notify({ message: "Job's Done!"}));
});

gulp.task('default', ['browserify'], function(){});