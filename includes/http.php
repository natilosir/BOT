<?php

function http($uri, $array)
{
    // Ensure $array is an array
    if (! is_array($array) && ! is_object($array)) {
        // Log or debug the invalid input
        error_log('http() called with invalid $array parameter: '.print_r($array, true));
        $array = []; // Provide an empty array as a fallback
    }

    // Initialize a cURL session
    $ch = curl_init();

    // Set the URL of the website you want to send data to
    curl_setopt($ch, CURLOPT_URL, api.$uri);

    // Tell cURL to use the POST method
    curl_setopt($ch, CURLOPT_POST, 1);

    // Attach the POST fields
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array));

    // Return the transfer as a string instead of outputting it directly
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // Execute the cURL session and fetch the response
    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        error_log('cURL error: '.curl_error($ch));
    }

    // Close the cURL session
    curl_close($ch);

    // Display the response
    return json_decode($response);
}
