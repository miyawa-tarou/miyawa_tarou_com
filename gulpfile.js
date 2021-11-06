let gulp = require('gulp');
let sass = require('gulp-sass');

const compileSass = () =>
        gulp.src('./sass/*.scss')
            .pipe(sass({outputStyle: 'expanded'}))
            .pipe(gulp.dest('./css/'));

const watchSassFiles = () =>
    gulp.watch('./sass/*.scss', compileSass);

exports.default = gulp.parallel(compileSass, watchSassFiles);
