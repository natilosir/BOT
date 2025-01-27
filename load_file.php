<?php

if (isset($_GET['file'])) {
    header('Content-Type: text/plain');
    $fileUrl = $_GET['file'];

    if ($fileUrl !== 'df') {
        $filePath = '../../../' . $fileUrl;
    } else {
        $filePath = 'route.php';
    }

    $resolvedPath = realpath($filePath);
    $allowedBaseDir = realpath(__DIR__ . '/../../../');

    if ($resolvedPath && strpos($resolvedPath, $allowedBaseDir) === 0 && file_exists($filePath)) {
        if (filesize($filePath) > 0) {
            $fileHandle = fopen($filePath, 'r');
            while (!feof($fileHandle)) {
                echo fgets($fileHandle);
            }
            fclose($fileHandle);
        } else {
            http_response_code(403);
        }
    } else {
        http_response_code(403);
    }
} elseif (isset($_GET['folder'])) {
    date_default_timezone_set('Asia/Tehran'); // تنظیم منطقه زمانی

    $folderUrl = explode('/', trim($_GET['folder'], '/'));
    
    if (count($folderUrl) > 0) {
        if (strpos(end($folderUrl), '.') !== false) {
            array_pop($folderUrl); 
        }
    }

    $folderPath = '../../../' . implode('/', $folderUrl);

    if (is_dir($folderPath)) {
        $files = scandir($folderPath);
        $result[] = [
            'name' => '../',
            'slug' => implode('/', array_slice($folderUrl, 0, -1)), // اصلاح شده
            'type' => true
        ];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folderPath . '/' . $file;
                if (is_dir($filePath)) {
                    $result[] = [
                        'name' => $file,
                        'slug' => implode('/', $folderUrl) . '/' . $file,
                        'type' => true
                    ];
                }
            }
        }

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $folderPath . '/' . $file;
                if (!is_dir($filePath)) {
                    $result[] = [
                        'name' => $file,
                        'slug' => implode('/', $folderUrl) . '/' . $file,
                        'type' => false
                    ];
                }
            }
        }

        $result = array_map(function($item) {
            $item['slug'] = preg_replace('/\/+/', '/', $item['slug']);
            return $item;
        }, $result);

    
        echo json_encode($result, JSON_UNESCAPED_UNICODE);

    } else {
        http_response_code(403);
    }
} else {
    http_response_code(403);
}
?>
