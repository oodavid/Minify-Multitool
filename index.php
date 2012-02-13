<?php
	/**
	 * Minify Multitool
	 *
	 *	Combines, minifies, and caches JS and CSS
	 *
	 *	Example URLS
	 *		/minify?v=12&f=css/base.css,bootstrap.less,index.css
	 *		/minify?v=123&f=js/base.js,index.js
	 *
	 *  3rd Party code
	 *		LESS:   leafo.net/lessphp
	 *		CSSMIN: lateralcode.com/css-minifier/
	 *		JSMIN:  github.com/rgrove/jsmin-php
	 */

	require 'lessc.inc.php'; $less = new lessc();
	require 'cssmin.php';
	require 'jsmin.php';

	/*
	 * Figure out the request variables (.htaccess screws them so we can't use $_GET - no worries)
	 */
		// Pull it out
		$tmp = $_SERVER['REQUEST_URI'];
		// Remove everything before the ?
		$tmp = substr($tmp, (strpos($tmp, '?') + 1));
		// Parse into parameters
		parse_str($tmp, $params);
		// Do we have both a version and a list of files?
		if(!array_key_exists('v', $params) || !array_key_exists('f', $params)){
			// Be upset
			header ("Content-type: text/plain");
			echo <<<HEREDOC
/**
 * Minify Multitool - @oodavid 2012
 *
 *	Nothing to do, are you missing a parameter?
 *
 *	Example URLS
 *		/minify?v=12&f=css/base.css,bootstrap.less,index.css
 *		/minify?v=123&f=js/base.js,index.js
 */
HEREDOC;
			exit();
		}
		// Split up the files into an array
		$files = explode(',', $params['f']);

	/*
	 * Load the files and process them
	 */
		// The content is written here
		$content = "/* === Minify Multitool - @oodavid 2012 === */\n\n";

		// We assume it's plaintext unless something gets processed
		$mime = "text/plain";

		// Loop the files
		foreach($files AS $file){
			// An identifier
			$content .= "/* {$file} */\n\n";
			// Load up the file
			$string = @file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/' . $file);
			if($string){
				// Only process files that haven't been min'ed already
				if(strpos($file, '.min') === FALSE){
					// Process based on extension
					switch(pathinfo($file, PATHINFO_EXTENSION)){
						case 'less':
							$string = $less->parse($string);
						case 'css':
							$mime = "text/css";
							$string = minifycss($string);
							break;
						case 'js':
							$mime = "text/javascript";
							$string = JSMin::minify($string);
							break;
					}
				}
				// Append to the content
				$content .= trim($string) . "\n\n";
			}
		}
	
	/*
	 * Write the file and output to browser
	 */
		// Write it
		if(basename($_SERVER['REDIRECT_URL'])){
			$filepath = dirname(__FILE__) . '/cache/' . basename($_SERVER['REDIRECT_URL']);
			file_put_contents($filepath, $content);
		}

		// Output
		header ("Content-type: {$mime}");
		echo $content;