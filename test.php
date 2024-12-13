<?php
include 'ORM/ORM.php';

$user = DB::table('users');

$results = $user->where('unk_connect', '>=', 1)->get();

echo (! empty($results)) ? '<table border="1"><tr><th>'.implode('</th><th>', array_keys((array) $results[0])).'</th></tr>'.implode('', array_map(function ($row) {
    return '<tr><td>'.implode('</td><td>', array_map('htmlspecialchars', (array) $row)).'</td></tr>';
}, $results)).'</table>' : print_r($results, true).'هیچ داده‌ای یافت نشد.'; ?>


