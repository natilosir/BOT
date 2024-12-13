<?php

$the_user = $user->where('tel_id', $fromID)->first();
$id       = $the_user->id;

$map = [
    'من 🙎‍♂ پسرم'  => 'M',
    'من 🙍‍♀ دخترم' => 'F',
];

$ok = $user->update($id, ['sex' => $map[$text]]);

if ($ok) {
    include_once 'includes/unknown_connect.php';
}
