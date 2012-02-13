# Minify MultiTool

 * Uses PHP and .htaccess
 * Combines, minifies, and caches JS and CSS
   * Parses .less files
   * Skips files with .min in their name
 * Use as a submodule without any mess
   * git submodule add git@github.com:oodavid/Minify-Multitool minify
   * There's a .gitignore file in the cache folder

## Example URLs

 * /minify?v=12&f=css/base.css,bootstrap.less,index.css
 * /minify?v=123&f=js/base.js,index.js

## 3rd party code

 * LESS: leafo.net/lessphp
 * CSSMIN: lateralcode.com/css-minifier/
 * JSMIN: github.com/rgrove/jsmin-php