<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use ImamHossain\GithubUpdater\Services\UpdaterService;

Route::get('/github-pull', function (Request $request, UpdaterService $service) {
    $token = env('GITHUB_UPDATER_TOKEN');
    if ($token && $request->query('token') !== $token) {
        abort(403, 'Invalid token');
    }

    $steps = $service->run();

    $rows = '';
    foreach ($steps as $i => $step) {
        $status = $step['success'] ? '✔ SUCCESS' : '✘ FAILED';
        $color = $step['success'] ? '#00ff9c' : '#ff4d4d';
        $output = htmlspecialchars($step['output']);
        $rows .= <<<HTML
        <div class="step">
            <div class="step-header">
                <span class="step-num">[{$i}]</span>
                <span class="step-label">{$step['label']}</span>
                <span class="step-status" style="color:{$color}">{$status}</span>
            </div>
            <pre class="step-output">{$output}</pre>
        </div>
        HTML;
    }

    $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>GitHub Deploy Console</title>
        <style>
            body {
                background: #0d0d0d;
                color: #00ff9c;
                font-family: 'Courier New', monospace;
                padding: 30px;
                margin: 0;
            }
            h1 {
                color: #00ff9c;
                text-shadow: 0 0 8px #00ff9c88;
                border-bottom: 1px solid #00ff9c44;
                padding-bottom: 10px;
                font-size: 22px;
            }
            .subtitle { color: #666; margin-bottom: 25px; font-size: 13px; }
            .step {
                background: #111;
                border: 1px solid #1f1f1f;
                border-left: 3px solid #00ff9c;
                margin-bottom: 15px;
                border-radius: 4px;
                overflow: hidden;
            }
            .step-header {
                display: flex;
                gap: 12px;
                padding: 10px 15px;
                background: #161616;
                align-items: center;
            }
            .step-num { color: #555; }
            .step-label { flex: 1; color: #e0e0e0; }
            .step-status { font-weight: bold; font-size: 12px; }
            .step-output {
                white-space: pre-wrap;
                word-break: break-word;
                padding: 12px 15px;
                margin: 0;
                color: #8a8a8a;
                font-size: 12.5px;
                max-height: 250px;
                overflow-y: auto;
            }
            .footer {
                margin-top: 25px;
                color: #00ff9c;
                font-size: 14px;
                text-shadow: 0 0 6px #00ff9c66;
            }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-thumb { background: #00ff9c44; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>&gt; GITHUB DEPLOY CONSOLE_</h1>
        <div class="subtitle">Executed at {$request->server('REQUEST_TIME_FLOAT')} — imamhossain/github-updater</div>
        {$rows}
        <div class="footer">&gt; All processes finished. Connection closed._</div>
    </body>
    </html>
    HTML;

    return response($html);
});