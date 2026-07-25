const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | The stock entries (resources/js/app.js, resources/sass/app.scss) do not
 | exist in this repo. The storefront ships as a self-contained template
 | under public/assets/templates/basic.
 |
 | NOTE: the committed public/.../css/main.css is HAND-MAINTAINED and is
 | currently AHEAD of the SASS source in .../basic/sass (the compiled CSS
 | contains ~30 classes the SASS no longer emits: preloader, cookie banner,
 | dashboard sidebar, .badge--discount, etc.). Compiling the SASS straight
 | onto main.css would REGRESS the storefront, so this build writes to a
 | scratch folder (css/build/) instead. Re-point the destination to
 | `${templateBase}/css` only after the SASS has been resynced with main.css.
 |
 */

const templateBase = 'public/assets/templates/basic';

mix.sass(`${templateBase}/sass/main.scss`, `${templateBase}/css/build/main.css`)
    .options({
        processCssUrls: false, // keep template url() asset paths as-authored
    });
