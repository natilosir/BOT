<?php

namespace natilosir\bot\log;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Throwable;

class AdvancedLogger {
    public function __construct() {
        $this->logFilePath = paths()->log;
        $this->startTime   = microtime(true);
        $this->startMemory = memory_get_usage();
        $this->entryCount  = 0;
        $this->cloner      = new VarCloner();
        $this->cloner->setMaxItems(200);
        $this->cloner->setMaxString(500);
        $this->initialize();
    }

    public static $instance;
    public        $logFilePath;
    public        $startTime;
    public        $startMemory;
    public        $entryCount = 0;
    public        $cloner;
    public        $logLevels  = [
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

    public function initialize(): void {
        if ( file_exists($this->logFilePath) ) @unlink($this->logFilePath);
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        set_exception_handler([ $this, 'exceptionHandler' ]);
        register_shutdown_function([ $this, 'shutdownHandler' ]);
        $this->createLogFile();
    }

    private function formatFilePath( ?string $file ): string {
        if ( !$file ) return '[unknown]';
        if ( defined('PATH') ) {
            $rootPath = constant('PATH');
            if ( strpos($file, $rootPath) === 0 ) return substr($file, strlen($rootPath));
        }
        return $file;
    }

    private function createLogFile(): void {
        $extractor  = new DumpHeaderExtractor();
        $dumpHeader = $extractor->getHeader();
        $html       = <<<HTML
            <!DOCTYPE html>
            <html lang="fa" dir="rtl">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Debug Log — Natilosir</title>
            {$dumpHeader}
            <style>
            @font-face{font-family:"FiraCode";src:url("https://dl.natilos.ir/ffff/FiraCode-Medium.woff2") format("woff2");font-display:swap}
            @font-face{font-family:"IRANSans";src:url("https://natilos.ir/zimage/font/is.woff") format("woff2");font-display:swap}
            *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
            :root{
              --bg:#08080f;--bg-2:#0d0d1a;
              --card:rgba(20,20,38,.65);--card-2:rgba(28,28,52,.75);
              --glass:rgba(255,255,255,.03);
              --border:rgba(255,255,255,.07);--border-2:rgba(255,255,255,.15);
              --text:#ececf5;--text-2:#9a9ab8;--text-3:#5c5c80;
              --violet:#3b0444;--violet-2:#7c3aed;--cyan:#ec79f6;--pink:#f472b6;
              --red:#ff4d6d;--amber:#ffb020;--green:#34d399;--blue:#60a5fa;
              --r-sm:10px;--r:14px;--r-lg:18px;--r-xl:24px;
              --shadow:0 8px 32px rgba(0,0,0,.4);
              --shadow-lg:0 20px 60px rgba(0,0,0,.5);
            }
            html{scroll-behavior:smooth}
            body{font-family:'IRANSans',system-ui,sans-serif;background:var(--bg);color:var(--text);
              min-height:100vh;line-height:1.5;overflow-x:hidden;-webkit-font-smoothing:antialiased}
            body::before{content:'';position:fixed;inset:0;
              background:
                radial-gradient(circle at 15% 15%,rgba(167,139,250,.12),transparent 45%),
                radial-gradient(circle at 85% 85%,rgba(34,211,238,.10),transparent 45%),
                radial-gradient(circle at 50% 50%,rgba(244,114,182,.05),transparent 60%);
              pointer-events:none;z-index:0;animation:bgFloat 20s ease-in-out infinite alternate}
            @keyframes bgFloat{0%{transform:scale(1) translate(0,0)}100%{transform:scale(1.1) translate(-2%,2%)}}
            ::selection{background:rgba(167,139,250,.35);color:#fff}
            ::-webkit-scrollbar{width:10px;height:10px}
            ::-webkit-scrollbar-track{background:transparent}
            ::-webkit-scrollbar-thumb{background:linear-gradient(180deg,rgba(167,139,250,.4),rgba(34,211,238,.4));
              border-radius:10px;border:2px solid var(--bg)}
            ::-webkit-scrollbar-thumb:hover{background:linear-gradient(180deg,rgba(167,139,250,.7),rgba(34,211,238,.7))}
            .app{position:relative;z-index:1;max-width:1460px;margin:0 auto;padding:24px 20px 60px}
            
            /* HEADER */
            .header{background:var(--card);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);
              border:1px solid var(--border);border-radius:var(--r-xl);padding:28px 32px;margin-bottom:20px;
              position:relative;overflow:hidden}
            .header::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;
              background:linear-gradient(90deg,transparent,rgba(167,139,250,.6),rgba(34,211,238,.6),transparent)}
            .header::after{content:'';position:absolute;top:-50%;right:-20%;width:400px;height:400px;
              background:radial-gradient(circle,rgba(167,139,250,.15),transparent 70%);pointer-events:none}
            .header-top{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;position:relative;z-index:1}
            .brand{display:flex;align-items:center;gap:16px}
            .brand-logo{width:52px;height:52px;background:linear-gradient(135deg,var(--violet),var(--cyan));
              border-radius:16px;display:grid;place-items:center;font-size:26px;
              box-shadow:0 8px 24px rgba(167,139,250,.35),inset 0 1px 0 rgba(255,255,255,.2);position:relative}
            .brand-logo::after{content:'';position:absolute;inset:0;border-radius:inherit;background:inherit;
              filter:blur(16px);opacity:.5;z-index:-1}
            .brand-text h1{font-size:1.5rem;font-weight:700;letter-spacing:-.02em;
              background:linear-gradient(135deg,#fff 0%,#c4b5fd 60%,#67e8f9 100%);
              -webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;line-height:1.2}
            .brand-text .sub{font-size:.78rem;color:var(--text-3);font-family:'FiraCode',monospace;margin-top:3px;letter-spacing:.02em}
            .header-stats{display:flex;gap:10px;flex-wrap:wrap}
            .stat{display:flex;align-items:center;gap:8px;background:var(--glass);border:1px solid var(--border);
              border-radius:12px;padding:9px 14px;font-size:.8rem;font-family:'FiraCode',monospace;color:var(--text-2);transition:all .25s}
            .stat:hover{border-color:var(--border-2);background:rgba(255,255,255,.05)}
            .stat .v{color:var(--cyan);font-weight:600}
            .stat .lbl{color:var(--text-3);font-size:.72rem}
            .stat .ico{font-size:1rem}
            .stat.live .v{color:var(--green)}
            .stat.live .dot{width:6px;height:6px;border-radius:50%;background:var(--green);
              box-shadow:0 0 8px var(--green);animation:pulse 1.8s ease-in-out infinite}
            @keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
            
            /* TOOLBAR */
            .toolbar{display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px;
              position:sticky;top:12px;z-index:50;background:rgba(8,8,15,.75);
              backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);padding:12px;
              border-radius:var(--r-lg);border:1px solid var(--border);transition:all .3s}
            .search{flex:1;min-width:240px;position:relative}
            .search input{width:100%;background:rgba(255,255,255,.03);border:1px solid var(--border);
              border-radius:12px;padding:12px 44px 12px 44px;color:var(--text);font-family:inherit;
              font-size:.9rem;outline:none;transition:all .25s}
            .search input::placeholder{color:var(--text-3)}
            .search input:focus{border-color:rgba(167,139,250,.5);background:rgba(167,139,250,.05);
              box-shadow:0 0 0 3px rgba(167,139,250,.12)}
            .search .s-ico{position:absolute;right:14px;top:50%;transform:translateY(-50%);
              color:var(--text-3);font-size:.95rem;pointer-events:none}
            .search .kbd{position:absolute;left:12px;top:50%;transform:translateY(-50%);
              font-family:'FiraCode',monospace;font-size:.7rem;color:var(--text-3);
              background:rgba(255,255,255,.04);border:1px solid var(--border);
              border-radius:6px;padding:2px 7px;pointer-events:none;transition:opacity .2s}
            .search input:focus ~ .kbd{opacity:0}
            .filters{display:flex;gap:6px;flex-wrap:wrap}
            .fbtn{display:flex;align-items:center;gap:7px;background:rgba(255,255,255,.02);
              border:1px solid var(--border);border-radius:11px;padding:9px 14px;color:var(--text-2);
              font-family:'FiraCode',monospace;font-size:.78rem;font-weight:600;cursor:pointer;
              transition:all .2s;white-space:nowrap}
            .fbtn:hover{background:rgba(255,255,255,.05);border-color:var(--border-2);color:var(--text)}
            .fbtn.active{background:linear-gradient(135deg,rgba(167,139,250,.2),rgba(34,211,238,.2));
              color:#fff;border-color:rgba(167,139,250,.5);box-shadow:0 4px 16px rgba(167,139,250,.25)}
            .fbtn .cnt{background:rgba(255,255,255,.08);border-radius:8px;padding:1px 7px;
              font-size:.72rem;min-width:22px;text-align:center}
            .fbtn.active .cnt{background:rgba(255,255,255,.2)}
            .actions{display:flex;gap:6px}
            .abtn{width:38px;height:38px;display:grid;place-items:center;background:rgba(255,255,255,.02);
              border:1px solid var(--border);border-radius:11px;color:var(--text-2);font-size:1rem;
              cursor:pointer;transition:all .2s}
            .abtn:hover{background:rgba(255,255,255,.06);color:var(--text);border-color:var(--border-2);transform:translateY(-1px)}
            .abtn:active{transform:translateY(0)}
            
            /* ENTRIES */
            .entries{direction:ltr;text-align:left}
            .log-entry{background:var(--card);border:1px solid var(--border);border-radius:var(--r-lg);
              margin-bottom:10px;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);
              animation:slideIn .45s cubic-bezier(.16,1,.3,1) forwards;opacity:0;transform:translateY(10px);
              position:relative;backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px)}
            @keyframes slideIn{to{opacity:1;transform:translateY(0)}}
            .log-entry:hover{border-color:var(--border-2);box-shadow:var(--shadow);transform:translateY(-1px)}
            .log-entry.hidden{display:none}
            .log-entry::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;
              background:var(--accent,var(--violet));box-shadow:0 0 16px var(--accent,var(--violet));opacity:.85;z-index:2}
            .log-entry[data-level="error"]{--accent:#ff4d6d}
            .log-entry[data-level="fatal"]{--accent:#ff4d6d}
            .log-entry[data-level="exception"]{--accent:#ff4d6d}
            .log-entry[data-level="warning"]{--accent:#ffb020}
            .log-entry[data-level="info"]{--accent:#34d399}
            .log-entry[data-level="notice"]{--accent:#60a5fa}
            .log-entry[data-level="debug"]{--accent:#94a3b8}
            .log-entry[data-level="default"]{--accent:var(--violet)}
            
            /* ENTRY HEAD */
            .entry-head{display:flex;align-items:center;justify-content:space-between;
              padding:14px 18px 14px 20px;cursor:pointer;user-select:none;gap:12px;position:relative}
            .entry-head:hover .chev{color:var(--accent,var(--violet))}
            .entry-left{display:flex;align-items:center;gap:12px;flex:1;min-width:0}
            .lvl{font-family:'FiraCode',monospace;font-size:.68rem;font-weight:700;
              padding:4px 9px;border-radius:7px;letter-spacing:.05em;white-space:nowrap;flex-shrink:0;text-transform:uppercase;
              background:rgba(167,139,250,.14);color:#c4b5fd;border:1px solid rgba(167,139,250,.3)}
            .log-entry[data-level="error"] .lvl,.log-entry[data-level="fatal"] .lvl,.log-entry[data-level="exception"] .lvl{
              background:rgba(255,77,109,.14);color:#ff8fa3;border:1px solid rgba(255,77,109,.3)}
            .log-entry[data-level="warning"] .lvl{background:rgba(255,176,32,.14);color:#ffd07a;border:1px solid rgba(255,176,32,.3)}
            .log-entry[data-level="info"] .lvl{background:rgba(52,211,153,.14);color:#6ee7b7;border:1px solid rgba(52,211,153,.3)}
            .log-entry[data-level="notice"] .lvl{background:rgba(96,165,250,.14);color:#93c5fd;border:1px solid rgba(96,165,250,.3)}
            .log-entry[data-level="debug"] .lvl{background:rgba(148,163,184,.14);color:#cbd5e1;border:1px solid rgba(148,163,184,.3)}
            .msg{font-family:'FiraCode',monospace;font-size:.86rem;color:var(--text);
              white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;min-width:0}
            .entry-right{display:flex;align-items:center;gap:14px;flex-shrink:0}
            .meta{display:flex;align-items:center;gap:10px;font-size:.74rem;color:var(--text-3);font-family:'FiraCode',monospace}
            .meta .mi{display:inline-flex;align-items:center;padding:3px 8px;border-radius:7px;transition:all .2s}
            .meta .mi.file-mi{cursor:pointer;color:var(--text-2);max-width:340px;
              white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
            .meta .mi.file-mi:hover{background:rgba(34,211,238,.1);color:var(--cyan)}
            .meta .mi.file-mi:active{transform:scale(.97)}
            .meta .mi.file-mi:hover::after{opacity:1}
            .chev{color:var(--text-3);font-size:.75rem;
              transition:transform .3s cubic-bezier(.4,0,.2,1),color .2s;display:inline-block}
            .log-entry.open .chev{transform:rotate(90deg)}
            
            /* ENTRY BODY */
            .entry-body{max-height:0;overflow:hidden;
              transition:max-height .45s cubic-bezier(.4,0,.2,1),padding .3s;padding:0 20px}
            .log-entry.open .entry-body{max-height:10000px;padding:14px 20px 20px}
            .data{background:rgba(0,0,0,.35);border:1px solid var(--border);border-radius:12px;
              padding:4px;margin-top:4px;overflow-x:auto}
            /* UI polish layer - intentionally does not touch Symfony dump rendering */
            .log-entry{box-shadow:0 10px 35px rgba(0,0,0,.18)}
            .log-entry:hover{box-shadow:0 18px 50px rgba(124,58,237,.18)}
            .entry-head{background:linear-gradient(90deg,rgba(167,139,250,.04),transparent)}
            .trace-sec{background:rgba(124,58,237,.035);border-radius:14px;padding:14px}
            .trace-item{transition:.2s ease}
            .trace-item:hover{transform:translateX(-3px)}
            
            pre.sf-dump{font-family:'FiraCode',monospace!important;background:transparent!important;
              direction:ltr!important;text-align:left;text-shadow:none;padding:12px;border-radius:8px;margin:0}
            pre.sf-dump::selection{background-color:#f5f5f5;color:#1a1a1a}
            pre.sf-dump::-moz-selection{background-color:#f5f5f5;color:#1a1a1a}
            pre.sf-dump .sf-dump-default{direction:ltr!important;text-align:left}
            pre.sf-dump .sf-dump-note{color:#22d3ee!important;font-size:12px!important;font-family:'FiraCode',monospace!important}
            .sf-dump *{line-height:1.6;font-size:13px;font-family:'FiraCode',monospace!important}
            a.sf-dump-ref.sf-dump-toggle{font-size:0!important;line-height:1}
            a.sf-dump-ref.sf-dump-toggle>span{font-size:15px!important;line-height:1;display:contents;
              vertical-align:middle;transition:color .2s}
            a.sf-dump-ref.sf-dump-toggle>span:hover{color:var(--violet)}
            
            /* CALLER injected inside sf-dump header line */
            pre.sf-dump .log-caller{
              color:#c4b5fd!important;font-weight:700!important;font-family:'FiraCode',monospace!important;
              cursor:pointer;user-select:none;
              padding:2px 8px!important;border-radius:6px!important;
              background:rgba(167,139,250,.10);
              border:1px solid rgba(167,139,250,.22);
              font-size:11.5px!important;line-height:1.4!important;
              transition:all .2s;display:inline-block;margin-right:4px;
            }
            pre.sf-dump .log-caller:hover{
              background:rgba(167,139,250,.22);color:#fff!important;
              border-color:rgba(167,139,250,.5);
              box-shadow:0 2px 10px rgba(167,139,250,.25);
            }
            pre.sf-dump .log-caller:active{transform:scale(.97)}
            
            /* TRACE */
            .trace-sec{margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
            .trace-title{font-family:'FiraCode',monospace;font-size:.75rem;color:var(--cyan);
              margin-bottom:10px;letter-spacing:.03em;text-transform:uppercase;display:flex;align-items:center;gap:6px}
            .trace-title::before{content:'';width:3px;height:12px;background:var(--cyan);border-radius:2px;
              box-shadow:0 0 8px var(--cyan)}
            .trace-item{padding:8px 12px;margin-bottom:6px;background:rgba(0,0,0,.2);
              border:1px solid var(--border);border-radius:10px;font-size:.8rem;
              font-family:'FiraCode',monospace;transition:all .2s;direction:ltr;text-align:left}
            .trace-item:hover{border-color:var(--border-2);background:rgba(0,0,0,.3)}
            .trace-item .num{color:var(--text-3);margin-right:8px;font-weight:600}
            .trace-item .fn{color:var(--cyan)}
            .trace-item .fp{color:var(--text-2);font-size:.76rem}
            .trace-item .file-line{color:var(--text-3);font-size:.74rem;margin-top:4px;
              display:inline-block;padding:2px 7px;border-radius:6px;cursor:pointer;
              transition:all .2s;position:relative}
            .trace-item .file-line.copy-path:hover{background:rgba(34,211,238,.15);color:var(--cyan);
              box-shadow:0 0 0 1px rgba(34,211,238,.3)}
            .trace-item .file-line.copy-path:active{transform:scale(.97)}
            .trace-item .file-line.copy-path::after{content:"⧉";margin-inline-start:6px;
              opacity:0;transition:opacity .2s;font-size:.85rem}
            .trace-item .file-line.copy-path:hover::after{opacity:1}
            
            .copy-btn{position:absolute;top:77px;right:26px;background:rgba(255,255,255,.05);
              border:1px solid var(--border);border-radius:8px;padding:5px 9px;color:var(--text-3);
              cursor:pointer;font-size:.8rem;opacity:0;transition:all .2s;z-index:5;backdrop-filter:blur(8px)}
            .log-entry:hover .copy-btn{opacity:1}
            .copy-btn:hover{background:var(--violet);color:#fff;border-color:var(--violet);transform:scale(1.05)}
            .copy-btn.copied{background:var(--green);color:#fff;border-color:var(--green)}
            
            /* EMPTY */
            .empty{text-align:center;padding:80px 20px;color:var(--text-3);display:none}
            .empty.show{display:block}
            .empty .ico{font-size:3rem;margin-bottom:14px;opacity:.5}
            .empty .t{font-size:1rem;color:var(--text-2);margin-bottom:6px}
            .empty .s{font-size:.82rem;font-family:'FiraCode',monospace}
            
            /* FOOTER */
            .footer{background:var(--card);backdrop-filter:blur(24px);-webkit-backdrop-filter:blur(24px);
              border:1px solid var(--border);border-radius:var(--r-xl);padding:20px 28px;margin-top:20px;
              display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap}
            .footer-stats{display:flex;gap:22px;flex-wrap:wrap}
            .fstat{display:flex;align-items:center;gap:8px;font-size:.8rem;color:var(--text-2);font-family:'FiraCode',monospace}
            .fstat .v{color:var(--cyan);font-weight:600}
            .brand-mark{font-size:.74rem;color:var(--text-3);font-family:'FiraCode',monospace;letter-spacing:.02em}
            .brand-mark b{color:var(--violet);font-weight:600}
            
            /* TOAST */
            .toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(100px);
              background:linear-gradient(135deg,rgba(167,139,250,.95),rgba(124,58,237,.95));color:#fff;
              padding:12px 22px;border-radius:12px;font-family:'FiraCode',monospace;font-size:.82rem;
              z-index:9999;opacity:0;transition:all .35s cubic-bezier(.16,1,.3,1);pointer-events:none;
              box-shadow:0 20px 40px rgba(0,0,0,.4),0 0 40px rgba(167,139,250,.3);
              display:flex;align-items:center;gap:8px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.15)}
            .toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
            
            @media (max-width:720px){
              .app{padding:16px 12px 40px}
              .header{padding:20px}
              .brand-text h1{font-size:1.2rem}
              .brand-logo{width:44px;height:44px;font-size:22px}
              .toolbar{position:static}
              .meta .mi.file-mi{max-width:160px;font-size:.68rem}
              .filters{width:100%;overflow-x:auto;padding-bottom:4px}
              .fbtn{font-size:.72rem;padding:8px 12px}
              .entry-head{padding:12px 14px 12px 16px}
              .msg{font-size:.8rem}
              pre.sf-dump .log-caller{font-size:10.5px!important;padding:1px 6px!important}
            }
            </style>
            </head>
            <body>
            
            <div class="app">
            
              <header class="header">
                <div class="header-top">
                  <div class="brand">
                    <div class="brand-logo">🐛</div>
                    <div class="brand-text">
                      <h1>Debug Log</h1>
                      <div class="sub">natilosir\bot • AdvancedLogger v4.3</div>
                    </div>
                  </div>
                  <div class="header-stats">
                    <div class="stat live"><span class="dot"></span><span class="lbl">Live</span></div>
                    <div class="stat"><span class="ico">📋</span><span class="lbl">Entries</span><span class="v" id="statCount">0</span></div>
                    <div class="stat"><span class="ico">⏱️</span><span class="lbl">Uptime</span><span class="v" id="statUptime">0s</span></div>
                  </div>
                </div>
              </header>
            
              <div class="toolbar">
                <div class="search">
                  <span class="s-ico">🔍</span>
                  <input type="text" id="searchInput" placeholder="Search logs…" autocomplete="off">
                  <span class="kbd">/</span>
                </div>
                <div class="filters">
                  <button class="fbtn active" data-level="all">📋 ALL <span class="cnt" id="c-all">0</span></button>
                  <button class="fbtn" data-level="error">🔴 ERR <span class="cnt" id="c-error">0</span></button>
                  <button class="fbtn" data-level="warning">🟡 WARN <span class="cnt" id="c-warning">0</span></button>
                  <button class="fbtn" data-level="info">🟢 INFO <span class="cnt" id="c-info">0</span></button>
                  <button class="fbtn" data-level="exception">💥 EXC <span class="cnt" id="c-exception">0</span></button>
                  <button class="fbtn" data-level="notice">🔵 NOTE <span class="cnt" id="c-notice">0</span></button>
                </div>
                <div class="actions">
                  <button class="abtn" id="btnExpand" title="Expand all (E)">📂</button>
                  <button class="abtn" id="btnCollapse" title="Collapse all (C)">📁</button>
                  <button class="abtn" id="btnClear" title="Clear search">🗑️</button>
                </div>
              </div>
            
              <div id="logEntries" class="entries"></div>
            
              <div class="empty" id="empty">
                <div class="ico">🌌</div>
                <div class="t">No log entries found</div>
                <div class="s">Try adjusting your filters or search query</div>
              </div>
            
              <footer class="footer">
                <div class="footer-stats">
                  <div class="fstat"><span>📊</span><span>Total</span><span class="v" id="footerTotal">0</span></div>
                  <div class="fstat"><span>⚠️</span><span>Errors</span><span class="v" id="footerErrors">0</span></div>
                  <div class="fstat"><span>🕐</span><span>Updated</span><span class="v" id="footerTime">—</span></div>
                </div>
                <div class="brand-mark">Powered by <b>natilosir\bot</b> • AdvancedLogger v4.3</div>
              </footer>
            
            </div>
            
            <div class="toast" id="toast"><span>✓</span><span id="toastMsg">Copied</span></div>
            
            <script>
            (function(){
              'use strict';
              var byId = function(id){ return document.getElementById(id); };
              var state = {
                filter:'all', search:'', startTime: Date.now(),
                counts:{ all:0,error:0,warning:0,info:0,exception:0,notice:0,debug:0,fatal:0,default:0 }
              };
            
              function updateUptime(){
                var d = Math.floor((Date.now() - state.startTime) / 1000);
                var h = Math.floor(d/3600), m = Math.floor((d%3600)/60), s = d%60;
                var txt = h>0 ? h+'h '+m+'m' : (m>0 ? m+'m '+s+'s' : s+'s');
                var el = byId('statUptime'); if(el) el.textContent = txt;
              }
              setInterval(updateUptime, 1000);
            
              function updateStats(){
                byId('statCount').textContent = state.counts.all;
                byId('footerTotal').textContent = state.counts.all;
                byId('footerErrors').textContent =
                  (state.counts.error||0) + (state.counts.fatal||0) + (state.counts.exception||0);
                ['all','error','warning','info','exception','notice'].forEach(function(k){
                  var el = byId('c-'+k); if(el) el.textContent = state.counts[k] || 0;
                });
                byId('footerTime').textContent = new Date().toLocaleTimeString(
                  'en-GB', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
              }
            
              function applyFilters(){
                var entries = document.querySelectorAll('.log-entry');
                var visible = 0;
                entries.forEach(function(e){
                  var lvl = e.dataset.level || 'default';
                  var text = e.textContent.toLowerCase();
                  var okFilter = state.filter === 'all' || lvl === state.filter;
                  var okSearch = !state.search || text.indexOf(state.search) !== -1;
                  if(okFilter && okSearch){ e.classList.remove('hidden'); visible++; }
                  else e.classList.add('hidden');
                });
                byId('empty').classList.toggle('show', visible === 0);
              }
            
              var toastTimer;
              function showToast(msg){
                byId('toastMsg').textContent = msg;
                byId('toast').classList.add('show');
                clearTimeout(toastTimer);
                toastTimer = setTimeout(function(){ byId('toast').classList.remove('show'); }, 1800);
              }
            
              document.querySelectorAll('.fbtn').forEach(function(b){
                b.addEventListener('click', function(){
                  document.querySelectorAll('.fbtn').forEach(function(x){ x.classList.remove('active'); });
                  b.classList.add('active');
                  state.filter = b.dataset.level;
                  applyFilters();
                });
              });
            
              var searchInput = byId('searchInput');
              var debounce;
              searchInput.addEventListener('input', function(){
                clearTimeout(debounce);
                debounce = setTimeout(function(){
                  state.search = searchInput.value.toLowerCase().trim();
                  applyFilters();
                }, 120);
              });
            
              byId('btnExpand').addEventListener('click', function(){
                document.querySelectorAll('.log-entry').forEach(function(e){ e.classList.add('open'); });
                document.querySelectorAll('a.sf-dump-toggle').forEach(function(t){
                  if(!t.closest('.sf-dump-expanded')) t.click();
                });
              });
              byId('btnCollapse').addEventListener('click', function(){
                document.querySelectorAll('.log-entry').forEach(function(e){ e.classList.remove('open'); });
              });
              byId('btnClear').addEventListener('click', function(){
                searchInput.value = ''; state.search = ''; applyFilters(); searchInput.focus();
              });
            
              byId('logEntries').addEventListener('click', function(e){
                // 1) copy full entry
                var copyBtn = e.target.closest('.copy-btn');
                if(copyBtn){
                  e.stopPropagation();
                  var entry = copyBtn.closest('.log-entry');
                  copyText(entry.innerText);
                  copyBtn.classList.add('copied');
                  copyBtn.textContent = '✓';
                  showToast('Copied to clipboard');
                  setTimeout(function(){
                    copyBtn.classList.remove('copied');
                    copyBtn.textContent = '📋';
                  }, 1600);
                  return;
                }
                // 2) copy path (entry-head file-mi + trace file-line)
                var copyPath = e.target.closest('.copy-path');
                if(copyPath){
                  e.stopPropagation();
                  copyText(copyPath.dataset.path || copyPath.textContent.trim());
                  showToast('Path copied');
                  return;
                }
                // 3) click on caller inside dump → toggle entry
                var caller = e.target.closest('.log-caller');
                if(caller){
                  e.stopPropagation();
                  var le = caller.closest('.log-entry');
                  if(le) le.classList.toggle('open');
                  return;
                }
                // 4) click on head → toggle entry
                var head = e.target.closest('.entry-head');
                if(head){ head.parentElement.classList.toggle('open'); }
              });
            
              function copyText(text){
                if(navigator.clipboard && window.isSecureContext){
                  navigator.clipboard.writeText(text).catch(function(){});
                } else {
                  var ta = document.createElement('textarea');
                  ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                  document.body.appendChild(ta); ta.select();
                  try { document.execCommand('copy'); } catch(err){}
                  document.body.removeChild(ta);
                }
              }
            
              document.addEventListener('keydown', function(e){
                if(e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA'){
                  if(e.key === 'Escape') e.target.blur();
                  return;
                }
                if(e.key === '/'){ e.preventDefault(); searchInput.focus(); }
                if(e.key === 'e' || e.key === 'E'){ byId('btnExpand').click(); }
                if(e.key === 'c' || e.key === 'C'){ byId('btnCollapse').click(); }
              });
            
              window.updateLog = function(html){
                var container = byId('logEntries');
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var entry = temp.firstElementChild;
                if(!entry || !entry.classList.contains('log-entry')) return;
            
                var lvl = entry.dataset.level || 'default';
                if(state.counts[lvl] === undefined) state.counts[lvl] = 0;
                state.counts[lvl]++;
                state.counts.all++;
            
                if(state.counts.all === 1) entry.classList.add('open');
            
                container.appendChild(entry);
                updateStats();
                applyFilters();
            
                entry.querySelectorAll('script').forEach(function(old){
                  var s = document.createElement('script');
                  Array.prototype.forEach.call(old.attributes, function(a){ s.setAttribute(a.name, a.value); });
                  s.appendChild(document.createTextNode(old.innerHTML));
                  old.parentNode.replaceChild(s, old);
                });
            
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
              };
            
              updateUptime();
              updateStats();
            })();
            </script>
            </body>
            </html>
            HTML;
        file_put_contents($this->logFilePath, $html, LOCK_EX);
    }

    public static function getInstance(): self {
        if ( !self::$instance ) self::$instance = new self();
        return self::$instance;
    }

    public function errorHandler( int $errno, string $errstr, string $errfile, int $errline ): bool {
        $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
        $this->log([ 'message' => $errstr ], $this->logLevels[$errno] ?? 'ERROR', [], $errfile, $errline, $trace);
        return true;
    }

    private function findCallerFromTrace( ?array $trace ): array {
        if ( !$trace ) return [ 'class' => null, 'function' => null ];
        foreach ( $trace as $frame ) {
            $class = $frame['class'] ?? null;
            if ( $class !== null ) {
                if ( strpos($class, 'AdvancedLogger') !== false ) continue;
                if ( $class === 'natilosir\\bot\\Log' ) continue;
            }
            return [
                'class'    => $class,
                'function' => $frame['function'] ?? null,
            ];
        }
        return [ 'class' => null, 'function' => null ];
    }

    private function buildCallerLabel( ?string $class, ?string $func, ?string $file ): string {
        if ( $class && $func ) return $class . '::' . $func . '()';
        if ( $class ) return $class;
        if ( $func ) return $func . '()';
        if ( $file ) {
            $base = basename($file);
            return $base !== '' ? $base : 'global';
        }
        return 'global';
    }

    public function log( $data, string $level, array $context = [], ?string $file = null, ?int $line = null, ?array $trace = null, ?string $callerClass = null, ?string $callerFunction = null ): void {
        $date = verta();
        $l    = strtolower($level);

        set_error_handler([ $this, 'errorHandler' ]);
        if ( $callerClass === null && $callerFunction === null && $trace ) {
            $found          = $this->findCallerFromTrace($trace);
            $callerClass    = $found['class'];
            $callerFunction = $found['function'];
        }

        $callerLabel = $this->buildCallerLabel($callerClass, $callerFunction, $file);
        $safeCaller  = htmlspecialchars($callerLabel, ENT_QUOTES, 'UTF-8');

        $shortFile = $this->formatFilePath($file);
        $safeFile  = htmlspecialchars($shortFile, ENT_QUOTES, 'UTF-8');
        $safeLine  = (int) $line;
        $dataPath  = htmlspecialchars($shortFile . ':' . $safeLine, ENT_QUOTES, 'UTF-8');

        // caller injected inside dump header line
        $callerHtml = '<span class="log-caller" title="Click to toggle entry">' . $safeCaller . '</span>';

        $content   = $this->formatDataWithSymfony($data, $callerHtml);
        $trace     = $trace ? $this->normalizeTrace($trace) : [];
        $traceHTML = $trace ? $this->formatTrace($trace) : '';

        $shortMessage = $this->extractShortMessage($data);
        $safeMessage  = htmlspecialchars($shortMessage, ENT_QUOTES, 'UTF-8');
        $safeLevel    = htmlspecialchars($level, ENT_QUOTES, 'UTF-8');

        $html = "<div class='log-entry' data-level='{$l}'>" . "<button class='copy-btn' title='Copy entry'>📋</button>" . "<div class='entry-head'>" . "<div class='entry-left'>" . "<span class='lvl'>{$safeLevel}</span>" . "<span class='msg'>{$safeMessage}</span>" . "</div>" . "<div class='entry-right'>" . "<div class='meta'>" . "<span class='mi file-mi copy-path' data-path='{$dataPath}' title='Click to copy path'>📄 {$safeFile}:{$safeLine}</span>" . "<span class='mi'>🕐 {$date}</span>" . "</div>" . "<span class='chev'>▶</span>" . "</div>" . "</div>" . "<div class='entry-body'>" . "<div class='data'>{$content}</div>" . "{$traceHTML}" . "</div>" . "</div>";

        $safe  = json_encode($html, JSON_UNESCAPED_UNICODE);
        $entry = "<script>updateLog({$safe});</script>\n";
        file_put_contents($this->logFilePath, $entry, FILE_APPEND | LOCK_EX);
    }

    private function extractShortMessage( $data ): string {
        if ( is_string($data) ) return mb_substr($data, 0, 120);
        if ( is_array($data) ) {
            if ( isset($data['message']) ) return mb_substr((string) $data['message'], 0, 120);
            return 'Array(' . count($data) . ')';
        }
        if ( is_object($data) ) return get_class($data);
        return (string) $data;
    }

    /**
     * Dumps $data and injects $callerHtml before the first <span class="sf-dump-note">
     * Result:
     *   App\Models\Site\User array:2 [▼
     *     "message" => "..."
     */
    private function formatDataWithSymfony( $data, string $callerHtml = '' ): string {
        $dumper = new HtmlDumper();
        $dumper->setDumpHeader('');
        $cloned = $this->cloner->cloneVar($data);
        ob_start();
        $dumper->dump($cloned);
        $output = ob_get_clean();
        $output = str_replace('<pre', '<pre class="sf-dump"', $output);

        if ( $callerHtml !== '' ) {
            $needle = '<span class="sf-dump-note">';
            $pos    = strpos($output, $needle);
            if ( $pos !== false ) {
                $output = substr($output, 0, $pos) . $callerHtml . ' ' . substr($output, $pos);
            }
            elseif ( preg_match('/<pre\b[^>]*>/', $output, $m, PREG_OFFSET_CAPTURE) ) {
                $insertAt = $m[0][1] + strlen($m[0][0]);
                $output   = substr($output, 0, $insertAt) . $callerHtml . ' ' . substr($output, $insertAt);
            }
        }

        return $output;
    }

    private function normalizeTrace( array $trace ): array {
        $out = [];
        foreach ( $trace as $frame ) {
            $class = $frame['class'] ?? '';
            if ( $class === 'natilosir\\bot\\Log' || strpos($class, 'AdvancedLogger') !== false ) {
                continue;
            }
            if ( isset($frame['file']) ) {
                $out[] = $frame;
            }
            if ( count($out) >= 9 ) {
                break;
            }
        }
        return $out;
    }

    private function formatTrace( array $trace ): string {
        $html = "<div class='trace-sec'><div class='trace-title'>⚡ Backtrace (" . count($trace) . " frames)</div>";
        foreach ( $trace as $i => $t ) {
            $file  = $this->formatFilePath($t['file'] ?? null);
            $line  = $t['line'] ?? '-';
            $func  = $t['function'] ?? '?';
            $class = $t['class'] ?? '';
            $type  = $t['type'] ?? '';

            $fullFunc = $class ? "{$class}{$type}{$func}" : $func;
            $fullPath = $file . ':' . $line;

            $safeFunc = htmlspecialchars($fullFunc, ENT_QUOTES, 'UTF-8');
            $safeFile = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
            $safeLine = htmlspecialchars((string) $line, ENT_QUOTES, 'UTF-8');
            $safePath = htmlspecialchars($fullPath, ENT_QUOTES, 'UTF-8');

            $html .= "<div class='trace-item'>" . "<span class='num'>#{$i}</span>" . "<span class='fn'>{$safeFunc}()</span><br>" . "<span class='file-line copy-path' " . "data-path='{$safePath}' " . "title='Click to copy path'>" . "📄 {$safeFile}:{$safeLine}" . "</span>" . "</div>";
            if ( !empty($t['args']) ) {
                $html .= "<div style='margin:4px 0 10px 20px'>" . $this->formatDataWithSymfony($t['args']) . "</div>";
            }
        }
        return $html . "</div>";
    }

    public function exceptionHandler( Throwable $e ): void {
        $this->log([
            'message'   => $e->getMessage(),
            'exception' => get_class($e),
        ], 'EXCEPTION', [], $e->getFile(), $e->getLine(), $e->getTrace(), get_class($e), '__construct');
        if ( PHP_SAPI !== 'cli' && !headers_sent() ) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        $message = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        $file    = htmlspecialchars($this->formatFilePath($e->getFile()), ENT_QUOTES, 'UTF-8');
        $line    = (int) $e->getLine();
        $class   = htmlspecialchars(get_class($e), ENT_QUOTES, 'UTF-8');
        echo "<div dir='ltr' style='font-family:monospace;background:#1e1e1e;color:#eee;padding:18px;border-radius:10px'>" . "<b style='color:#ff6b6b'>{$class}</b><br>" . "<div style='margin-top:8px'>{$message}</div>" . "<div style='margin-top:8px;color:#aaa'>{$file}:{$line}</div>" . "<div style='margin-top:12px'>Full trace: <code>log.html</code></div></div>";
    }

    public function shutdownHandler(): void {
        $e = error_get_last();
        if ( $e && in_array($e['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ]) ) {
            $this->log($e, 'FATAL', [], $e['file'] ?? '', $e['line'] ?? 0);
        }
        file_put_contents($this->logFilePath, "</div></body></html>", FILE_APPEND | LOCK_EX);
    }
}