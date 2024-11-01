<?php

require 'ORM.php';

echo 'Select all data with chaining methods<br>';
$users = DB::Table('users')
    ->where('name', 'Jane Doe')
    ->orderBy('id', 'DESC')
    ->limit(10)
    ->get();

foreach ($users as $user) {
    echo $user['id'].' - '.$user['name'].' - '.$user['email'].'<br>';
}

echo 'Count users with where condition<br>';
$userCount = DB::Table('users')
    ->count();

echo 'Total number of users named John: '.$userCount.'<br>';

echo 'Search with multiple conditions<br>';
$searchResults = DB::Table('users')
    ->search(['name' => 'Jane', 'email' => 'example.com'])
    ->orderBy('id', 'deSC')
    ->limit(15)
    ->get();

foreach ($searchResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}

echo 'Use the orderBy clause to sort users by email in ascending order with a limit<br>';
$orderedResults = DB::Table('users')
    ->where('name', 'John')
    ->andwhere('name', 'jane')
    ->orderBy('id', 'deSC')
    ->limit(7)
    ->get();

foreach ($orderedResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}

echo 'Run a custom SQL query<br>';
$customQueryResults = DB::Table('users')
    ->query("SELECT * FROM users WHERE email LIKE '%example.com%' LIMIT 5");
foreach ($customQueryResults as $result) {
    echo $result['id'].' - '.$result['name'].' - '.$result['email'].'<br>';
}

echo 'Insert new data with array<br>';
$newUser = ['name' => 'Jane Doe', 'email' => 'jane.doe@example.com'];
DB::Table('users')
    ->insert($newUser);

echo 'Update data with array<br>';
$updateData = ['name' => 'John Smith', 'email' => 'john.smith@example.com'];
DB::Table('users')
    ->update(1, $updateData);

echo 'Update data with single value<br>';
$updateDataSingle = 'Jane Smith';
DB::Table('users')
    ->update(2, ['name' => $updateDataSingle]);

echo 'Delete data<br>';
DB::Table('users')
    ->delete(1);
