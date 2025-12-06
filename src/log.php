<?php

namespace natilosir\bot;

use ReflectionClass;
use Throwable;

date_default_timezone_set('Asia/Tehran');
header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding('UTF-8');

class AdvancedLogger {
    public static $instance;
    public        $logFilePath;

    public $logLevels = [
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

    public function initialize(): void {
        if ( file_exists($this->logFilePath) ) {
            @unlink($this->logFilePath);
        }

        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        set_error_handler([ $this, 'errorHandler' ]);
        set_exception_handler([ $this, 'exceptionHandler' ]);
        register_shutdown_function([ $this, 'shutdownHandler' ]);

        $this->createLogFile();
    }
    private function createLogFile(): void
    {
        $html = <<<HTML
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Debug Log</title>

    <style>
    @font-face {
        font-family: "FiraCode";
        src: url("https://dl.natilos.ir/ffff/FiraCode-Medium.woff2") format("woff2");
        font-weight: normal;
        font-style: normal;
    }

    @font-face {
        font-family: "IRANSans";
        src: url("https://natilos.ir/zimage/font/is.woff") format("woff2");
        font-weight: normal;
        font-style: normal;
    }

    body {font-family: IRANSans; background:#1e1e1e; color:#d4d4d4; margin:0; padding:20px;}
    .log-entries {direction: ltr; text-align: left;}
    .log-entry {background:#2c203d; margin:10px 0; padding:15px; border-radius:8px; border-left:4px solid #b48ef7;}

    .log-meta {margin-bottom:10px; font-size:0.9em;}
    .log-badge {background:#7c3aed;font-family: FiraCode;padding:2px 8px; border-radius:4px; font-weight:bold;}
    .log-time {margin:0 15px; color:#b3a6ca;font-family: FiraCode}
    .log-file {color:#c084fc;font-family: FiraCode}

    .log-entry-fatal,
    .log-entry-error { background:#3b0d0d !important; border-left:4px solid #ff4b4b !important; }

    .log-entry-exception { background:#4a0000 !important; border-left:4px solid #ff1a1a !important; }

    .log-entry-warning {
        background:#3b320d !important;
        border-left:4px solid #facc15 !important;
        color:#fef9c3 !important;
    }

    .log-entry-info {
        background:#0d3b1a !important;
        border-left:4px solid #22c55e !important;
    }

    .log-entry-default {
        background:#2c203d !important;
        border-left:4px solid #b48ef7 !important;
    }

    .log-badge-error,
    .log-badge-fatal { background:#ff4b4b !important; }

    .log-badge-exception { background:#ff1a1a !important; color:#fff !important; }

    .log-badge-warning { background:#facc15 !important; color:#000 !important; }

    .log-badge-info { background:#22c55e !important; }

    .array-item { display:flex; align-items:flex-start; gap:10px; }
    .key-label { color:#ff79c6; font-weight:bold; flex-shrink:0; }

    .arrow::before {
        content: "⇒";
        color: #8be9fd;
        font-weight: bold;
    }

    .data-container { font-family:FiraCode; flex:1; min-width:0; }

    .array-wrapper { display:inline-flex; background:#201528; border-radius:5px; gap:8px; line-height:1.4; }

    .object-wrapper { display:inline-block; background:#201528; border-radius:6px; }

    .array-header, .object-header {
    display: inline-flex;
    align-items: flex-start;
    gap: 6px;
    cursor: pointer;
    flex-direction: row;
    justify-content: flex-start;
}
    .type-bracket { color:#c678dd; font-weight:bold; font-size:1.1em; }
    .array-title { color:#f1fa8c; font-size:0.92em; opacity:0.95; }

    .toggle {
        cursor: pointer;
        color: #c084fc;
        font-weight: bold;
        font-size: 22px;
        user-select: none;
        transition: transform 0.18s ease;
        margin-top: -3px;
    }

    .array-wrapper.open > .toggle,
    .object-wrapper.open > .toggle { transform: rotate(90deg); }

    .toggle-content { display:none; margin-top:8px; margin-right:20px; }

    .array-wrapper.open > .toggle-content,
    .object-wrapper.open > .toggle-content { display:block; }

    .string-value { color:#8ef58a; font-family: IRANSans; }
    .number-value { color:#d19a66; }
    .boolean-value { color:#ff4b4b; font-weight:bold; }
    .null-value { color:#ff5555; }
    .object-class { color:#ffb86c; font-weight:bold; }

    </style>
</head>

<body>
    <div id="logEntries" class="log-entries"></div>

<script>
document.addEventListener("DOMContentLoaded",  function(e) {
    const nodes = document.querySelectorAll('.string-value');
    const onlyBasicLatin = /^[\u0000-\u007F]*$/;

    nodes.forEach(node => {
        const text = node.textContent || "";

        if (onlyBasicLatin.test(text)) {
            node.style.fontFamily = "FiraCode";
        }
    });
});

document.addEventListener("click", function(e) {
    let header = e.target.closest(".array-header, .object-header");
    if (!header) return;

    let wrapper = header.closest(".array-wrapper, .object-wrapper");
    if (!wrapper) return;

    wrapper.classList.toggle("open");
});

function updateLog(html) {
    document.getElementById("logEntries").insertAdjacentHTML("beforeend", html);
}
</script>

</body>
</html>
HTML;

        file_put_contents($this->logFilePath, $html, LOCK_EX);
    }

    public static function getInstance(): self {
        if ( !self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function errorHandler( int $errno, string $errstr, string $errfile, int $errline ): bool {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $this->log([ 'message' => $errstr ], $this->logLevels[$errno] ?? 'ERROR', [], $errfile, $errline, $trace);
        return true;
    }

    /* ======================================================================
       Unserialize + JSON auto-detector
    ====================================================================== */

    public function log( $data, string $level, array $context = [], ?string $file = null, ?int $line = null, ?array $trace = null ): void {
        $date      = date('Y-m-d H:i:s');
        $content   = $this->formatData($data);
        $traceHTML = $trace ? $this->formatTrace($trace) : '';

        $l = strtolower($level);

        $classes = [
            'fatal'     => [ 'entry' => 'log-entry-fatal', 'badge' => 'log-badge-error' ],
            'error'     => [ 'entry' => 'log-entry-fatal', 'badge' => 'log-badge-error' ],
            'exception' => [ 'entry' => 'log-entry-exception', 'badge' => 'log-badge-exception' ],
            'warning'   => [ 'entry' => 'log-entry-warning', 'badge' => 'log-badge-warning' ],
            'info'      => [ 'entry' => 'log-entry-info', 'badge' => 'log-badge-info' ],
        ];

        $entryClass = $classes[$l]['entry'] ?? 'log-entry-default';
        $badgeClass = $classes[$l]['badge'] ?? 'log-badge';

        $html = "<div class='log-entry {$entryClass}'>" . "<div class='log-meta'>" . "<span class='log-badge {$badgeClass}'>{$level}</span>" . "<span class='log-time'>{$date}</span>" . "<span class='log-file'>{$file}:{$line}</span>" . "</div>" . "<div class='data-container'>{$content}</div>" . "{$traceHTML}" . "</div>";

        $safe = json_encode($html, JSON_UNESCAPED_UNICODE);

        $entry = "<script>updateLog($safe);</script>\n";

        file_put_contents($this->logFilePath, $entry, FILE_APPEND | LOCK_EX);
    }

    /* ======================================================================
       Formatter اصلی
    ====================================================================== */

    public function formatData( $data, int $depth = 0 ): string {
        $data = $this->autoDecode($data);

        if ( $depth > 10 ) {
            return "<span class='string-value'>[Maximum depth reached]</span>";
        }

        if ( is_array($data) ) {
            return $this->formatArray($data, $depth + 1);
        }
        if ( is_object($data) ) {
            return $this->formatObject($data, $depth + 1);
        }
        if ( is_string($data) ) {
            $safe = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
            return "<span class='string-value'>\"$safe\"</span>";
        }
        if ( is_numeric($data) ) {
            return "<span class='number-value'>$data</span>";
        }
        if ( is_bool($data) ) {
            return "<span class='boolean-value'>" . ( $data ? 'true' : 'false' ) . "</span>";
        }
        if ( $data === null ) {
            return "<span class='null-value'>null</span>";
        }

        return "<span class='string-value'>" . htmlspecialchars(print_r($data, true), ENT_QUOTES, 'UTF-8') . "</span>";
    }

    private function autoDecode( $data ) {
        if ( !is_string($data) ) {
            return $data;
        }

        $t = trim($data);

        // تشخیص serialize
        if ( preg_match('/^(a|O|s|i|b|d):/', $t) ) {
            $u = @unserialize($t, [ 'allowed_classes' => true ]);
            if ( $u !== false || $t === 'b:0;' ) {
                return $u;
            }
        }

        // تشخیص JSON
        $json = json_decode($t, true);
        if ( json_last_error() === JSON_ERROR_NONE ) {
            return $json;
        }

        return $data;
    }

    private function formatArray( array $array, int $depth ): string {
        $id        = uniqid("arr_");
        $isRoot    = $depth === 1;
        $openClass = $isRoot ? "open" : "";

        $out = "<div class='data-container array-wrapper {$openClass}'>";

        $out .= "<div class='array-header'>
                <span class='type-bracket'>[</span>
                <span class='array-title'>array(" . count($array) . ")</span>
                <span class='type-bracket'>]</span>
                <span class='toggle'>▶</span>
             </div>";

        $out .= "<div id='{$id}' class='toggle-content'>";

        foreach ( $array as $k => $v ) {
            $fk = htmlspecialchars((string) $k, ENT_QUOTES, 'UTF-8');
            $fv = $this->formatData($v, $depth);

            $out .= "<div class='array-item' style='margin-left:20px;'>
                    <span class='key-label'>$fk</span> => $fv
                 </div>";
        }

        $out .= "</div></div>";
        return $out;
    }

    /* ======================================================================
       Trace formatter
    ====================================================================== */

    private function formatObject( object $obj, int $depth ): string {
        $id        = uniqid("obj_");
        $class     = get_class($obj);
        $safeClass = htmlspecialchars(addslashes($class), ENT_QUOTES, 'UTF-8');
        $isRoot    = $depth === 1;
        $openClass = $isRoot ? "open" : "";

        $out = "<div class='data-container object-wrapper {$openClass}'>
            <div class='object-header'>
                <span class='type-brace'>{</span>
                <span class='object-class'>Object($safeClass)</span>
                <span class='type-brace'>}</span>
                <span class='toggle'>▶</span>
            </div>
            <div id='{$id}' class='toggle-content'>";

        try {
            $ref   = new ReflectionClass($obj);
            $props = $ref->getProperties();

            if ( empty($props) ) {
                $props = get_object_vars($obj);
                foreach ( $props as $name => $val ) {
                    $fv  = $this->formatData($val, $depth);
                    $out .= "<div class='object-prop' style='margin-left:20px;'>
                        <span class='key-label'>dynamic \$$name</span> = $fv
                     </div>";
                }
            }
            else {
                foreach ( $props as $p ) {
                    $p->setAccessible(true);
                    $name = $p->getName();
                    $val  = $p->getValue($obj);
                    $vis  = $p->isPublic() ? 'public' : ( $p->isProtected() ? 'protected' : 'private' );
                    $fv   = $this->formatData($val, $depth);

                    $out .= "<div class='object-prop' style='margin-left:20px;'>
                        <span class='key-label'>$vis \$$name</span> = $fv
                     </div>";
                }
            }
        } catch ( Throwable $e ) {
            $out .= "<div class='object-prop'>[Reflection error]</div>";
        }

        $out .= "</div></div>";
        return $out;
    }

    /* ======================================================================
       لاگ اصلی
    ====================================================================== */

    private function formatTrace( array $trace ): string {
        $html = "<div class='context-section'><h4>Backtrace</h4><div class='data-container'>";
        foreach ( $trace as $i => $t ) {
            $file = $t['file'] ?? '[internal]';
            $line = $t['line'] ?? '-';
            $func = $t['function'] ?? '?';

            $html .= "<div style='margin-bottom:6px; color:#a5b4fc'>#$i $file:$line — {$func}()</div>";

            if ( !empty($t['args']) ) {
                $html .= "<div style='margin-right:20px'>" . $this->formatData($t['args']) . "</div>";
            }
        }
        return $html . "</div></div>";
    }

    /* ======================================================================
       Error / Exception / Shutdown handlers
    ====================================================================== */

    public function exceptionHandler( Throwable $e ): void {
        $this->log([
            'message'   => $e->getMessage(),
            'exception' => $e,
        ], 'EXCEPTION', [], $e->getFile(), $e->getLine(), $e->getTrace());
    }

    public function shutdownHandler(): void {
        $e = error_get_last();
        if ( $e && in_array($e['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ]) ) {
            $this->log($e, 'FATAL', [], $e['file'] ?? '', $e['line'] ?? 0);
        }

        // بستن تگ‌های HTML در انتهای فایل
        file_put_contents($this->logFilePath, "</div></body></html>", FILE_APPEND | LOCK_EX);
    }

    private function entryClass( string $level ): string {
        $l = strtolower($level);

        return match ( $l ) {
            'fatal', 'error' => 'log-entry-fatal',
            'exception'      => 'log-entry-exception',
            'warning'        => 'log-entry-warning',
            'info'           => 'log-entry-info',
            default          => 'log-entry-default',
        };
    }

}

/* ======================================================================
   کلاس ساده‌تر برای استفاده روزمره
====================================================================== */

class Log {
    public static function info( $data, array $context = [] ): void {
        self::write('INFO', $data, $context);
    }

    public static function write( string $level, $data, array $context = [] ): void {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)[2] ?? [];

        AdvancedLogger::getInstance()->log($data, $level, $context, $trace['file'] ?? null, $trace['line'] ?? null);
    }

    public static function debug( $data, array $context = [] ): void {
        self::write('DEBUG', $data, $context);
    }

    public static function error( $data, array $context = [] ): void {
        self::write('ERROR', $data, $context);
    }

    public static function warning( $data, array $context = [] ): void {
        self::write('WARNING', $data, $context);
    }

    public static function notice( $data, array $context = [] ): void {
        self::write('NOTICE', $data, $context);
    }
}

/* توابع کمکی کوتاه */
function lg( $data, string $level = 'DEBUG', array $context = [] ): void {
    Log::write($level, $data, $context);
}

function log( $data, string $level = 'DEBUG', array $context = [] ): void {
    Log::write($level, $data, $context);
}

function dd( ...$vars ): never {
    foreach ( $vars as $v ) {
        Log::debug($v);
    }
    die;
}

function dump( ...$vars ): void {
    foreach ( $vars as $v ) {
        Log::debug($v);
    }
}

/* فعال‌سازی خودکار */
AdvancedLogger::getInstance();