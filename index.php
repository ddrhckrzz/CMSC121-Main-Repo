<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Directory</title>
    <style>
        body { 
            font-family: "JetBrains Mono", "Courier New", monospace; 
            background: #1e1e2e; 
            color: #cdd6f4; 
            padding: 2rem; 
            /* Removed the 150px margin-bottom that caused the blank space */
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
            background: #181825; 
            padding: 2rem; 
            border-radius: 8px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.3); 
        }
        h1 { 
            margin-top: 0; 
            color: #cba6f7; 
            border-bottom: 1px solid #45475a; 
            padding-bottom: 10px; 
        }
        
        /* TUI Tree CSS structure */
        ul.tree, ul.tree ul { list-style-type: none; margin: 0; padding: 0; }
        ul.tree ul { padding-left: 1.5em; }
        ul.tree li { position: relative; padding-left: 1.5em; margin: 0; line-height: 2em; }
        
        /* Branching lines */
        ul.tree ul li::before, ul.tree ul li::after { content: ""; position: absolute; left: 0; }
        ul.tree ul li::before { top: 0; bottom: 0; border-left: 1px solid #585b70; }
        ul.tree ul li::after { top: 1em; width: 1.2em; border-top: 1px solid #585b70; }
        ul.tree ul li:last-child::before { bottom: auto; height: 1em; }

        .icon { margin-right: 8px; font-size: 1.1em; }
        .folder { font-weight: bold; color: #89b4fa; }
        
        a { text-decoration: none; color: #a6e3a1; transition: color 0.1s; }
        a:hover { text-decoration: underline; color: #94e2d5; }
        
        .empty-msg { color: #a6adc8; font-style: italic; }

        /* Interactive Folder Styles */
        .folder-toggle { cursor: pointer; user-select: none; display: inline-flex; align-items: center; }
        .folder-toggle:hover .folder { color: #b4befe; }
        .caret { display: inline-block; width: 15px; text-align: center; color: #585b70; font-size: 0.8em; margin-right: 5px; transition: transform 0.2s; }
        .caret-down { transform: rotate(90deg); color: #89b4fa; }
        .collapsed { display: none !important; }

        /* Floating Diagnostics Panel */
        .diagnostics-panel {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(24, 24, 37, 0.95);
            border: 1px solid #45475a;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 10px 15px rgba(0,0,0,0.5);
            font-size: 0.85em;
            backdrop-filter: blur(5px);
            z-index: 1000;
        }
        .diagnostics-panel h3 { margin: 0 0 10px 0; color: #f38ba8; font-size: 1.1em; border-bottom: 1px solid #45475a; padding-bottom: 5px; }
        .diag-row { margin-bottom: 5px; display: flex; justify-content: space-between; gap: 20px; }
        .diag-label { color: #a6adc8; font-weight: bold; }
        .status-good { color: #a6e3a1; font-weight: bold; }
        .status-bad { color: #f38ba8; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Project Index</h1>
    
    <?php
    function buildDirectoryTree($dir, $baseDir = '') {
        $html = '';
        $items = @scandir($dir);
        
        if (!$items) return '';

        $folders = [];
        $files = [];

        foreach ($items as $item) {
            // Skip hidden configuration files and directories
            if (strpos($item, '.') === 0) continue; 
            
            $path = $dir . '/' . $item;
            $relativePath = $baseDir === '' ? $item : $baseDir . '/' . $item;

            if (is_dir($path)) {
                $folders[$item] = $relativePath;
            } else {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if (in_array($ext, ['php', 'html'])) {
                    if ($relativePath === 'index.php') continue;
                    $files[$item] = $relativePath;
                }
            }
        }

        if (empty($folders) && empty($files)) return ''; 

        $html .= "<ul>\n";
        
        ksort($folders);
        foreach ($folders as $folderName => $relPath) {
            $subTree = buildDirectoryTree($dir . '/' . $folderName, $relPath);
            
            if ($subTree !== '') { 
                $html .= "<li>";
                $html .= "<div class='folder-toggle' onclick='toggleFolder(this)'>";
                $html .= "<span class='caret caret-down'>▶</span><span class='icon'>📁</span><span class='folder'>" . htmlspecialchars($folderName) . "</span>";
                $html .= "</div>\n";
                $html .= $subTree;
                $html .= "</li>\n";
            }
        }

        ksort($files);
        foreach ($files as $fileName => $relPath) {
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $icon = ($ext === 'php') ? '🐘' : '🌐';
            $html .= "<li><span class='icon'>$icon</span><a href='" . htmlspecialchars($relPath) . "'>" . htmlspecialchars($fileName) . "</a></li>\n";
        }

        $html .= "</ul>\n";
        return $html;
    }

    $treeHtml = buildDirectoryTree(__DIR__);
    
    if ($treeHtml === '') {
        echo "<p class='empty-msg'>No PHP or HTML files found in subdirectories.</p>";
    } else {
        echo preg_replace('/<ul>/', '<ul class="tree">', $treeHtml, 1);
    }
    
    // --- Diagnostics Logic ---
    $php_version = phpversion();
    
    // Test MariaDB connection
    $db_status_class = "status-bad";
    $db_status_text = "Disconnected";
    
    mysqli_report(MYSQLI_REPORT_OFF); // Disable fatal errors for the check
    $mysqli = @new mysqli("127.0.0.1", "root", "", "lamp_dev", 3306);
    
    if (!$mysqli->connect_error) {
        $db_status_class = "status-good";
        $db_status_text = "Connected (lamp_dev)";
        $mysqli->close();
    }
    ?>
</div>

<div class="diagnostics-panel">
    <h3>System Diagnostics</h3>
    <div class="diag-row">
        <span class="diag-label">Web Server:</span>
        <span class="status-good">Caddy (Running)</span>
    </div>
    <div class="diag-row">
        <span class="diag-label">PHP-FPM:</span>
        <span class="status-good">v<?php echo htmlspecialchars($php_version); ?></span>
    </div>
    <div class="diag-row">
        <span class="diag-label">MariaDB:</span>
        <span class="<?php echo $db_status_class; ?>"><?php echo $db_status_text; ?></span>
    </div>
</div>

<script>
    function toggleFolder(element) {
        // Find the adjacent unordered list (the folder contents)
        var subTree = element.nextElementSibling;
        var caret = element.querySelector('.caret');
        
        if (subTree && subTree.tagName === 'UL') {
            subTree.classList.toggle('collapsed');
            caret.classList.toggle('caret-down');
        }
    }
</script>

</body>
</html>