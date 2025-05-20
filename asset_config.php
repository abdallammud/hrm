<?php
function get_logo_name_from_url() {
	// Get the current server name (e.g., "subdomain.example.com")
	$server_name = $_SERVER['SERVER_NAME'];

	// Extract the subdomain 
	$parts = explode(".", $server_name);
	$subdomain = array_shift($parts); // Remove and return the first element 

	// Sanitize subdomain (optional)
	$sanitized_subdomain = preg_replace('/[^a-z0-9\-]/i', '', $subdomain); 

	// Construct the logo file name
	$logo_file = $sanitized_subdomain . "_logo.png"; 

	// Check if the logo file exists
	if (file_exists("./assets/images/" . $logo_file)) {
		return $logo_file;
	} else {
		return "logo.png"; 
	}
}


?>