<?php
// General Settings:
$c_path = "/home/budzique/public_html/i/";
$c_url = "http://i.budzique.com/"; // Example: http://i.jbud.org/ (Remember the slash at the end!)
$c_title = "TinyImg"; // Title of your image host... (Example: Bob's Images)
$c_tagline = "Worth a thousand words"; // Tagline of your image host... (Example: Worth a thousand words)
$c_maxFileSize = 10; // Max Filesize in MB
$c_maxUploads = 10; // Max uploads per session
$c_debug_mode = false; // Turn on to get additional messages about server configuration.

// DO NOT EDIT Below:
$c_maxFileSize = $c_maxFileSize * 1024 * 1024; // Convert MB to Bytes
// Make formatted URL string for images:
$c_url_formatted = $c_url."i/";
$c_url_formatted = str_replace("/", "%2F", $c_url_formatted);
$c_url_formatted = str_replace(":", "%3A", $c_url_formatted);
?>