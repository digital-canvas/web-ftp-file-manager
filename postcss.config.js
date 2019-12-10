var tailwindcss = require('tailwindcss');

module.exports = {
    plugins: [
        // include whatever plugins you want
        // but make sure you install these via yarn or npm!
        require('postcss-easy-import'),
        tailwindcss('./tailwind.config.js'),

        // add browserslist config to package.json (see below)
        require('autoprefixer')
    ]
}
