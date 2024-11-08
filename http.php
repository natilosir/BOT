<?php

function http($uri, $array)
{
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

    //   the cURL session and fetch the response
    $response = curl_exec($ch);

    // Close the cURL session
    curl_close($ch);

    // Display the response
    return json_decode($response);
}
