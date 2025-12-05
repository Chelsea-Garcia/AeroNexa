<?php
// frontend/api_helper.php

function fetchData($url) {
    // Initialize cURL session
    $ch = curl_init();
    
    // Set options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
    
    // Execute request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        return []; // Return empty array on error
    }
    
    // Close connection
    curl_close($ch);
    
    // Decode JSON response to Array
    return json_decode($response, true) ?? [];
}
?>