const cssFileName = 'app-de57b5b5.css';
const jsFileName = 'app-09484a32.js';

module.exports = {
    content: [
        'resources/views/main/**/*.blade.php',
        'resources/views/components/main/**/*.blade.php',
        `public/build/assets/${jsFileName}`,
    ],
    css: [
        `public/build/assets/${cssFileName}`,
    ],
    output: `public/build/assets/${cssFileName}`,
    safelist: ['h2', 'fs-3', 'fs-4'],
}