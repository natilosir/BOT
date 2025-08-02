<?php

namespace natilosir\bot;

date_default_timezone_set('Asia/Tehran');
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

class AdvancedLogger {
    public static $instance;
    public        $logFilePath;
    public        $logLevels = [
        E_ERROR             => 'ERROR',
        E_WARNING           => 'WARNING',
        E_PARSE             => 'PARSE',
        E_NOTICE            => 'NOTICE',
        E_CORE_ERROR        => 'CORE_ERROR',
        E_CORE_WARNING      => 'CORE_WARNING',
        E_COMPILE_ERROR     => 'COMPILE_ERROR',
        E_COMPILE_WARNING   => 'COMPILE_WARNING',
        E_USER_ERROR        => 'USER_ERROR',
        E_USER_WARNING      => 'USER_WARNING',
        E_USER_NOTICE       => 'USER_NOTICE',
        E_STRICT            => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED        => 'DEPRECATED',
        E_USER_DEPRECATED   => 'USER_DEPRECATED',
    ];

    public function __construct() {
        $this->logFilePath = __DIR__ . '/../../../../log.html';
        $this->initialize();
    }

    public static function getInstance() {
        if ( !self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function initialize() {
        if ( file_exists($this->logFilePath) ) {
            @unlink($this->logFilePath);
        }

        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $this->logFilePath);

        set_error_handler([ $this, 'errorHandler' ]);
        set_exception_handler([ $this, 'exceptionHandler' ]);
        register_shutdown_function([ $this, 'shutdownHandler' ]);

        $this->createLogFile();
    }

    public function createLogFile() {
        $htmlHeader = <<<HTML
            <!DOCTYPE html>
            <html lang="fa" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <title>LOG</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
                }
            
                @font-face {
                    font-family: 'FiraCode';
                    src: url('https://dl.natilos.ir/ffff/FiraCode-Medium.woff2') format('woff2');
                    font-weight: normal;
                    font-style: normal;
                }
            
                @font-face {
                    font-family: 'IRANSans';
                    src: url('https://natilos.ir/zimage/font/is.woff') format('woff2');
                    font-weight: normal;
                    font-style: normal;
                }
            
                body {
                    font-family: 'FiraCode', Tahoma, sans-serif;
                    color: #f0f0f0;
                    padding: 20px;
                    line-height: 1.6;
                    background: linear-gradient(145deg, #0f051f, #1a0b2e, #2a0b4a);
                    background-size: 400% 400%;
                    animation: gradientFlow 18s ease infinite;
                    min-height: 100vh;
                }
            
                @keyframes gradientFlow {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
            
                h1 {
                    color: #d9b8ff;
                    text-align: center;
                    margin: 25px 0 30px;
                    font-size: 2.4rem;
                    text-shadow: 0 2px 8px rgba(217, 184, 255, 0.15);
                    position: relative;
                    padding-bottom: 10px;
                }
            
                h1::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 120px;
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #9d4edd, transparent);
                    border-radius: 3px;
                }
            
                .log-container {
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 15px;
                }
            
                .log-entry {
                    background: linear-gradient(145deg, rgba(15, 5, 31, 0.85), rgba(26, 11, 46, 0.9));
                    border-left: 4px solid #7b2cbf;
                    margin: 18px 0;
                    padding: 18px;
                    border-radius: 8px;
                    direction: ltr;
                    font-family: FiraCode;
                    font-size: 14px;
                    overflow-x: auto;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
                    backdrop-filter: blur(4px);
                    border-top: 1px solid rgba(157, 78, 221, 0.1);
                    transition: transform 0.3s, box-shadow 0.3s;
                }
            
                .log-entry:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 16px rgba(123, 44, 191, 0.3);
                    border-left: 4px solid #9d4edd;
                    background: linear-gradient(145deg, rgba(20, 8, 41, 0.9), rgba(33, 13, 61, 0.95));
                }
            
                .log-entry.error {
                    border-left-color: #ff1a1a;
                    background: linear-gradient(145deg, rgba(40, 5, 15, 0.9), rgba(60, 10, 20, 0.95));
                }
            
                .log-entry.error:hover {
                    border-left-color: #ff3333;
                    box-shadow: 0 8px 16px rgba(255, 51, 51, 0.25);
                    animation: errorPulse 2.5s infinite;
                }
            
                @keyframes errorPulse {
                    0% { box-shadow: 0 6px 12px rgba(255, 26, 26, 0.2); }
                    50% { box-shadow: 0 6px 18px rgba(255, 26, 26, 0.4); }
                    100% { box-shadow: 0 6px 12px rgba(255, 26, 26, 0.2); }
                }
            
                .log-entry.warning {
                    border-left-color: #ffb700;
                    background: linear-gradient(145deg, rgba(40, 30, 5, 0.9), rgba(60, 45, 10, 0.95));
                }
            
                .log-entry.warning:hover {
                    border-left-color: #ffcc00;
                    box-shadow: 0 8px 16px rgba(255, 183, 0, 0.25);
                }
            
                .log-entry.notice {
                    border-left-color: #3d8eff;
                    background: linear-gradient(145deg, rgba(5, 15, 40, 0.9), rgba(10, 25, 60, 0.95));
                }
            
                .log-entry.notice:hover {
                    border-left-color: #4da6ff;
                    box-shadow: 0 8px 16px rgba(61, 142, 255, 0.25);
                }
            
                .log-entry.debug {
                    border-left-color: #6eff9e;
                    background: linear-gradient(145deg, rgba(5, 40, 15, 0.9), rgba(10, 60, 25, 0.95));
                }
            
                .log-entry.debug:hover {
                    border-left-color: #88ffaa;
                    box-shadow: 0 8px 16px rgba(110, 255, 158, 0.2);
                }
            
                .log-entry.info {
                    border-left-color: #9d4edd;
                    background: linear-gradient(145deg, rgba(30, 5, 40, 0.9), rgba(45, 10, 60, 0.95));
                }
            
                .log-entry.info:hover {
                    border-left-color: #c77dff;
                    box-shadow: 0 8px 16px rgba(157, 78, 221, 0.3);
                }
            
                .log-meta {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 12px;
                    font-size: 0.9rem;
                }
            
                .log-time {
                    color: #d9b8ff;
                    position: relative;
                    padding-left: 18px;
                }
            
                .log-time::before {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 14%;
                    width: 12px;
                    height: 12px;
                    background: linear-gradient(135deg, #9d4edd, #7b2cbf);
                    border-radius: 3px;
                    opacity: 0.7;
                }
            
                .log-level {
                    font-weight: bold;
                    padding: 3px 10px;
                    border-radius: 15px;
                }
            
                .level-error { 
                    color: #ff3333;
                    background: rgba(255, 51, 51, 0.1);
                    border: 1px solid rgba(255, 51, 51, 0.3);
                }
            
                .level-warning { 
                    color: #ffb700;
                    background: rgba(255, 183, 0, 0.1);
                    border: 1px solid rgba(255, 183, 0, 0.3);
                }
            
                .level-notice { 
                    color: #3d8eff;
                    background: rgba(61, 142, 255, 0.1);
                    border: 1px solid rgba(61, 142, 255, 0.3);
                }
            
                .level-debug { 
                    color: #6eff9e;
                    background: rgba(110, 255, 158, 0.1);
                    border: 1px solid rgba(110, 255, 158, 0.3);
                }
            
                .level-info { 
                    color: #9d4edd;
                    background: rgba(157, 78, 221, 0.1);
                    border: 1px solid rgba(157, 78, 221, 0.3);
                }
            
                .log-content {
                    white-space: pre-wrap;
                    margin: 8px 0;
                }
            
                .log-context {
                    margin-top: 12px;
                    padding-top: 12px;
                    border-top: 1px dashed rgba(157, 78, 221, 0.3);
                }
            
                .log-file {
                    color: #c77dff;
                    font-weight: 500;
                }
            
                .object-class {
                    color: #c77dff;
                    font-weight: bold;
                }
            
                .property-name {
                    color: #3d8eff;
                }
            
                .string-value {
                    color: #6eff9e;
                    font-family: IRANSans !important;
                }
            
                .number-value {
                    color: #ffb700;
                }
            
                .boolean-value {
                    color: #ff3333;
                    font-weight: bold;
                }
            
                .null-value {
                    color: #888;
                    font-style: italic;
                }
            
                /* Responsive design */
                @media (max-width: 768px) {
                    body {
                        padding: 15px;
                    }
            
                    h1 {
                        font-size: 1.8rem;
                    }
            
                    .log-entry {
                        padding: 15px;
                    }
                }
            </style>
            </head>
            <body>
            HTML;

        file_put_contents($this->logFilePath, $htmlHeader, LOCK_EX);
    }

    public function log( $data, $level = 'DEBUG', $context = [], $file = null, $line = null ) {
        $logLevel = strtoupper($level);
        $logClass = strtolower($level);

        $content          = $this->formatData($data);
        $formattedContext = !empty($context) ? $this->formatData($context) : null;

        $timestamp = date('Y-m-d H:i:s');
        $timePart  = "<span class='log-time'>$timestamp</span>";
        $levelPart = "<span class='log-level level-$logClass'>$logLevel</span>";

        // افزودن اطلاعات فایل و خط
        $fileInfo = '';
        if ( $file && $line ) {
            $file     = implode('/', array_slice(explode('/', $file), 5));
            $fileInfo = "<span class='log-file'>File: $file:$line</span>";
        }

        $logEntry = "<div class='log-entry $logClass'>";
        $logEntry .= "<div class='log-meta'>$fileInfo $levelPart $timePart</div>";
        $logEntry .= "<div class='log-content'>$content</div>";

        if ( $formattedContext ) {
            $logEntry .= "<div class='log-context'><strong>Context:</strong> $formattedContext</div>";
        }

        $logEntry .= "</div>";

        file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    public function formatData( $data, $depth = 0 ) {
        // محدود کردن عمق برای جلوگیری از لوپ‌های بی‌نهایت
        if ( $depth > 20 ) {
            return "<span class='string-value'>[Max depth reached]</span>";
        }

        if ( is_object($data) ) {
            return $this->formatObject($data, $depth + 1);
        }

        if ( is_array($data) ) {
            return $this->formatArray($data, $depth + 1);
        }

        if ( is_string($data) ) {
            return "<span class='string-value'>" . htmlspecialchars($data, ENT_QUOTES, 'UTF-8') . "</span>";
        }

        if ( is_int($data) || is_float($data) ) {
            return "<span class='number-value'>$data</span>";
        }

        if ( is_bool($data) ) {
            $value = $data ? 'true' : 'false';
            return "<span class='boolean-value'>$value</span>";
        }

        if ( is_null($data) ) {
            return "<span class='null-value'>null</span>";
        }

        if ( is_resource($data) ) {
            return "<span class='string-value'>Resource: " . get_resource_type($data) . "</span>";
        }

        return "<span class='string-value'>" . htmlspecialchars(print_r($data, true), ENT_QUOTES, 'UTF-8') . "</span>";
    }

    public function formatObject( $object, $depth ) {
        if ( $depth > 20 ) {
            return "<span class='string-value'>[Max depth reached]</span>";
        }

        $className = get_class($object);
        $result    = "<div><span class='object-class'>Object($className)</span> {";

        try {
            if ( $className === 'stdClass' ) {
                $array          = json_decode(json_encode($object), true);
                $formattedValue = $this->formatArray($array, $depth + 1);
                $result         .= "<br>$formattedValue<br>";
            }
            else {
                $reflection = new \ReflectionClass($object);
                $properties = $reflection->getProperties();

                $items = [];
                foreach ( $properties as $property ) {
                    try {
                        $property->setAccessible(true);
                        $name  = $property->getName();
                        $value = $property->getValue($object);

                        $formattedValue = $this->formatData($value, $depth + 1);
                        $items[]        = "<span class='property-name'>$name</span> => $formattedValue";
                    } catch ( Exception $e ) {
                        $items[] = "<span class='property-name'>$name</span> => <span class='string-value'>[Inaccessible]</span>";
                    }
                }

                if ( empty($items) ) {
                    $result .= " [No accessible properties]";
                }
                else {
                    $result .= "<br>" . implode(",<br>", $items) . "<br>";
                }
            }
            $result .= "}";
        } catch ( Exception $e ) {
            $result .= " ... } <span class='string-value'>[Could not inspect object properties: {$e->getMessage()}]</span>";
        }

        return $result . "</div>";
    }

    public function formatArray( $array, $depth ) {
        if ( $depth > 20 ) {
            return "<span class='string-value'>[Max depth reached]</span>";
        }

        if ( empty($array) ) {
            return "[]";
        }

        $result = "[";
        $items  = [];

        foreach ( $array as $key => $value ) {
            $formattedKey   = is_string($key) ? "'<span class='property-name'>$key</span>'" : $key;
            $formattedValue = $this->formatData($value, $depth + 1);
            $items[]        = "$formattedKey => $formattedValue";
        }

        $result .= "<br>" . implode(",<br>", $items) . "<br>]";
        return $result;
    }

    public function errorHandler( $errno = null, $errstr = null, $errfile = null, $errline = null, $errcontext = null ) {
        $level = $this->logLevels[$errno] ?? 'UNKNOWN';

        $errorData = [
            'message' => $errstr,
            'file'    => $errfile,
            'line'    => $errline,
            'context' => $errcontext,
        ];

        $this->log($errorData, $level, [], $errfile, $errline);
        return true;
    }

    public function exceptionHandler( $exception ) {
        $errorData = [
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $exception->getTrace(),
        ];

        $this->log($errorData, 'ERROR', [], $exception->getFile(), $exception->getLine());
    }

    public function shutdownHandler() {
        $error = error_get_last();

        if ( $error && in_array($error['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ]) ) {
            $this->log($error, 'FATAL', [], $error['file'], $error['line']);
        }

        file_put_contents($this->logFilePath, "</body></html>", FILE_APPEND | LOCK_EX);
    }
}

class log {
    public static function info( $data, $context = [] ) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $file  = $trace[0]['file'] ?? 'unknown';
        $line  = $trace[0]['line'] ?? 'unknown';
        AdvancedLogger::getInstance()->log($data, 'INFO', $context, $file, $line);
    }

    public static function debug( $data, $context = [] ) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $file  = $trace[0]['file'] ?? 'unknown';
        $line  = $trace[0]['line'] ?? 'unknown';
        AdvancedLogger::getInstance()->log($data, 'DEBUG', $context, $file, $line);
    }

    public static function error( $data, $context = [] ) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $file  = $trace[0]['file'] ?? 'unknown';
        $line  = $trace[0]['line'] ?? 'unknown';
        AdvancedLogger::getInstance()->log($data, 'ERROR', $context, $file, $line);
    }

    public static function warning( $data, $context = [] ) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $file  = $trace[0]['file'] ?? 'unknown';
        $line  = $trace[0]['line'] ?? 'unknown';
        AdvancedLogger::getInstance()->log($data, 'WARNING', $context, $file, $line);
    }

    public static function notice( $data, $context = [] ) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1);
        $file  = $trace[0]['file'] ?? 'unknown';
        $line  = $trace[0]['line'] ?? 'unknown';
        AdvancedLogger::getInstance()->log($data, 'NOTICE', $context, $file, $line);
    }
}

function lg( $data, $level = 'DEBUG', $context = [] ) {
    $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
    AdvancedLogger::getInstance()->log($data, $level, $context, $trace['file'] ?? 'unknown', $trace['line'] ?? 'unknown');
}

function dd( $data ) {
    $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 1)[0];
    AdvancedLogger::getInstance()->log($data, 'DEBUG', [], $trace['file'] ?? 'unknown', $trace['line'] ?? 'unknown');
    die();
}

AdvancedLogger::getInstance();

//log::info('این یک پیغام اطلاعاتی است');
//log::debug('مقدار متغیر:', ['var' => $value]);
//log::error('خطا در اجرای کد');
//lg('این یک لاگ است', 'INFO');
//lg(['data' => $data], 'DEBUG', ['context' => 'additional info']);

?>