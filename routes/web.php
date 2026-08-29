<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use ImamHossain\GithubUpdater\Services\UpdaterService;

// Auth required — login na thakle deploy console access hobe na
Route::middleware('auth')->get('/github-pull', function (Request $request, UpdaterService $service) {
    return response()->stream(function () use ($service) {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>GitHub Deploy Console</title>
            <style>
                body { background: #0d0d0d; color: #00ff9c; font-family: 'Courier New', monospace; padding: 30px; margin: 0; }
                .hacker-name {
                    text-align: center; font-size: 42px; font-weight: bold; letter-spacing: 8px; color: #00ff9c;
                    text-shadow: 0 0 5px #00ff9c, 0 0 15px #00ff9c, 0 0 30px #00ff9c88, 0 0 50px #00ff9c44;
                    margin-bottom: 20px; animation: flicker 3s infinite alternate;
                }
                @keyframes flicker { 0%,19%,21%,23%,25%,54%,56%,100% { opacity:1; } 20%,22%,24%,55% { opacity:0.7; } }
                h1 { color: #00ff9c; text-shadow: 0 0 8px #00ff9c88; border-bottom: 1px solid #00ff9c44; padding-bottom: 10px; font-size: 22px; }
                .subtitle { color: #666; margin-bottom: 25px; font-size: 13px; }
                .step { background: #111; border: 1px solid #1f1f1f; border-left: 3px solid #555; margin-bottom: 12px; border-radius: 4px; overflow: hidden; opacity: 0; animation: fadeIn 0.4s forwards; }
                @keyframes fadeIn { to { opacity: 1; } }
                .step-header { display: flex; gap: 12px; padding: 10px 15px; background: #161616; align-items: center; }
                .step-num { color: #555; }
                .step-label { flex: 1; color: #e0e0e0; }
                .step-status { font-weight: bold; font-size: 12px; }
                .step-output {
                    white-space: pre-wrap; word-break: break-word; padding: 12px 15px; margin: 0;
                    color: #8fffc8; font-size: 12.5px; max-height: 300px; overflow-y: auto; background: #0a0a0a;
                }
                .footer { margin-top: 25px; color: #00ff9c; font-size: 14px; text-shadow: 0 0 6px #00ff9c66; }
                ::-webkit-scrollbar { width: 6px; }
                ::-webkit-scrollbar-thumb { background: #00ff9c44; border-radius: 3px; }
            </style>
        </head>
        <body>
            <div class="hacker-name">IMAM HOSSAIN</div>
            <h1>&gt; GITHUB DEPLOY CONSOLE_</h1>
            <div class="subtitle">imamhossain/github-updater — running live...</div>
        HTML;
        flush();

        $service->run(function ($step, $index) {
            $status = $step['success'] ? '✔ SUCCESS' : '✘ FAILED';
            $color = $step['success'] ? '#00ff9c' : '#ff4d4d';
            $output = htmlspecialchars($step['output']);
            $label = htmlspecialchars($step['label']);

            echo <<<HTML
            <div class="step" style="border-left-color:{$color}">
                <div class="step-header">
                    <span class="step-num">[{$index}]</span>
                    <span class="step-label">{$label}</span>
                    <span class="step-status" style="color:{$color}">{$status}</span>
                </div>
                <pre class="step-output">{$output}</pre>
            </div>
            HTML;
            flush();
        });

        echo '<div class="footer">&gt; All processes finished. Connection closed._</div></body></html>';
        flush();
    }, 200, [
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Accel-Buffering' => 'no',
        'Cache-Control' => 'no-cache',
    ]);
});