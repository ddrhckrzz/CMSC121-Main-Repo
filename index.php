<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Directory</title>
    <link href="index.css" rel="stylesheet" type="text/css">
    <script src="index.js" defer></script>
</head>

<body>

    <div class="container">
        <h1>Project Index <span class="live-indicator">● LIVE</span></h1>
        <div id="tree-container">Loading files...</div>
    </div>

    <div class="diagnostics-panel">
        <h3>System Diagnostics</h3>
        <div class="diag-row">
            <span class="diag-label">Web Server:</span>
            <span class="status-good">Caddy</span>
        </div>
        <div class="diag-row">
            <span class="diag-label">PHP-FPM:</span>
            <span id="diag-php" class="status-good">Checking...</span>
        </div>
        <div class="diag-row">
            <span class="diag-label">MariaDB:</span>
            <span id="diag-db" class="status-bad">Checking...</span>
        </div>
    </div>

</body>

</html>