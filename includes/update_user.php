<?php

function updateUser($from_id)
{
    // Get chat details from Telegram API
    $getChat    = http('getChat', ['chat_id' => $from_id]);
    $result     = $getChat->result;
    $id         = $result->id;
    $first_name = isset($result->first_name) ? $result->first_name : 'Unknown';
    $last_name  = isset($result->last_name) ? $result->last_name : 'Unknown';
    $username   = isset($result->username) ? $result->username : 'No Username';

    // Retrieve existing user data from the database based on tel_id
    $user_data = DB::table('users')->where('tel_id', $from_id)->first();

    // New user data to be saved or updated
    $new_data = [
        'tel_id'     => $from_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'username'   => $username,
        'created_at' => time(),
    ];

    // Check if the user exists in the database
    if ($user_data) {
        // Update only if there are changes in the data
        if (
            $user_data->first_name !== $first_name ||
            $user_data->last_name !== $last_name ||
            $user_data->username !== $username
        ) {
            DB::table('users')
                ->update(['tel_id' => $from_id], $new_data);
        }
    } elseif ($id) {
        // Insert a new user into the database

        DB::table('users')->insert($new_data);
    }
}