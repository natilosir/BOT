<?php
if (isset($_GET['file'])) {
    // دریافت آدرس فایل از پارامتر 'file'
    $fileUrl = $_GET['file'];

    // ساخت مسیر نهایی با توجه به نام فایل
    if ($fileUrl !== 'df') {
        $filePath = "../../../" . $fileUrl;
    } else {
        $filePath = 'route.php';
    }

    // بررسی امنیت مسیر فایل (برای جلوگیری از دسترسی به فایل‌های غیرمجاز)
    $resolvedPath = realpath($filePath); 
    $allowedBaseDir = realpath(__DIR__ . '/../../../'); // مسیر پایه مجاز

    // اگر فایل موجود باشد و داخل مسیر مجاز باشد
    if ($resolvedPath && strpos($resolvedPath, $allowedBaseDir) === 0 && file_exists($filePath)) {
        // بررسی اینکه فایل دارای محتوا باشد
        if (filesize($filePath) > 0) {
            // باز کردن فایل و نمایش محتوای آن
            $fileHandle = fopen($filePath, "r");
            while (!feof($fileHandle)) {
                echo fgets($fileHandle);
            }
            fclose($fileHandle);
        }else{http_response_code(403);}
        }else{http_response_code(403);}
        }else{http_response_code(403);}