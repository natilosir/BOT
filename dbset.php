<?php

include 'ORM/ORM.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'delete') {
        $id    = isset($_POST['id']) ? $_POST['id'] : null;
        $table = isset($_POST['table']) ? $_POST['table'] : null;

        if ($id && $table) {
            try {
                DB::table($table)->delete($id);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid input.']);
        }
    } elseif ($action === 'update') {
        $id     = isset($_POST['id']) ? $_POST['id'] : null;
        $table  = isset($_POST['table']) ? $_POST['table'] : null;
        $column = isset($_POST['column']) ? $_POST['column'] : null;
        $value  = $_POST['value'] == '0d5cze8' ? '' : $_POST['value'];

        if ($id && $table && $column) {
            try {
                $updateData = [
                    $column => $value,
                ];
                DB::table($table)->update($id, $updateData);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid input.']);
        }
    }
}
