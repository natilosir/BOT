    <?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Display all POST data
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}
