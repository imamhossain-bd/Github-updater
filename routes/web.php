<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use ImamHossain\GithubUpdater\Services\UpdaterService;

Route::get('/github-pull', function (Request $request, UpdaterService $service) {
    $steps = $service->run();
    $totalSteps = count($steps);
    $successCount = count(array_filter($steps, fn($s) => $s['success']));
    $failCount = $totalSteps - $successCount;
    $allSuccess = $failCount === 0;

    $rows = '';
    foreach ($steps as $i => $step) {
        $isSuccess = $step['success'];
        $badgeClass = $isSuccess ? 'badge-success' : 'badge-fail';
        $icon = $isSuccess ? '✓' : '✕';
        $borderClass = $isSuccess ? 'border-success' : 'border-fail';
        $output = htmlspecialchars($step['output']);
        $rows .= <<<HTML
        <div class="card {$borderClass}">
            <div class="card-header">
                <div class="card-title">
                    <span class="step-icon {$badgeClass}">{$icon}</span>
                    <span class="step-name">{$step['label']}</span>
                </div>
                <span class="badge {$badgeClass}">{$icon} {$step['label']}</span>
            </div>
            <pre class="card-output">{$output}</pre>
        </div>
        HTML;
    }

    $summaryClass = $allSuccess ? 'summary-success' : 'summary-fail';
    $summaryText = $allSuccess ? 'All steps completed successfully' : "{$failCount} step(s) failed";

    $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Deploy Dashboard</title>
        <style>
            * { box-sizing: border-box; }
            body {
                background: #f4f6f8;
                color: #1a1d23;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                margin: 0;
                padding: 40px 24px;
            }
            .container { max-width: 900px; margin: 0 auto; }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 24px;
                flex-wrap: wrap;
                gap: 12px;
            }
            .header-left h1 {
                font-size: 22px;
                font-weight: 700;
                margin: 0 0 4px 0;
                color: #1a1d23;
            }
            .header-left p {
                margin: 0;
                font-size: 13px;
                color: #6b7280;
            }
            .header-right {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: 15px;
            }
            .owner-name {
                font-size: 14px;
                font-weight: 600;
                color: #374151;
            }
            .summary-bar {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 14px 18px;
                border-radius: 10px;
                margin-bottom: 24px;
                font-size: 14px;
                font-weight: 600;
            }
            .summary-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
            .summary-fail { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
            .summary-stats {
                margin-left: auto;
                display: flex;
                gap: 16px;
                font-size: 13px;
                font-weight: 500;
            }
            .cards { display: flex; flex-direction: column; gap: 12px; }
            .card {
                background: #ffffff;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                border-left: 4px solid #e5e7eb;
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            }
            .border-success { border-left-color: #10b981; }
            .border-fail { border-left-color: #ef4444; }
            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 14px 18px;
                background: #fafbfc;
                border-bottom: 1px solid #f0f1f3;
            }
            .card-title {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .step-icon {
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 700;
                color: white;
            }
            .step-icon.badge-success { background: #10b981; }
            .step-icon.badge-fail { background: #ef4444; }
            .step-name {
                font-size: 14px;
                font-weight: 600;
                color: #1f2937;
                font-family: 'SF Mono', Consolas, monospace;
            }
            .badge {
                display: none;
            }
            .card-output {
                margin: 0;
                padding: 14px 18px;
                font-family: 'SF Mono', Consolas, 'Courier New', monospace;
                font-size: 12px;
                line-height: 1.6;
                color: #6b7280;
                white-space: pre-wrap;
                word-break: break-word;
                max-height: 220px;
                overflow-y: auto;
                background: #ffffff;
            }
            .footer-note {
                text-align: center;
                margin-top: 24px;
                font-size: 12px;
                color: #9ca3af;
            }
            ::-webkit-scrollbar { width: 6px; }
            ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="header-left">
                    <h1>Deploy Dashboard</h1>
                    <p>imamhossain/github-updater · executed just now</p>
                </div>
                <div class="header-right">
                    <div class="avatar">IH</div>
                    <span class="owner-name">Imam Hossain</span>
                </div>
            </div>

            <div class="summary-bar {$summaryClass}">
                <span>{$summaryText}</span>
                <div class="summary-stats">
                    <span>✓ {$successCount} passed</span>
                    <span>✕ {$failCount} failed</span>
                    <span>{$totalSteps} total</span>
                </div>
            </div>

            <div class="cards">
                {$rows}
            </div>

            <div class="footer-note">Deployment pipeline finished · imamhossain/github-updater v1.2.0</div>
        </div>
    </body>
    </html>
    HTML;

    return response($html);
});