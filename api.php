<?php
// Tell the browser to expect machine-readable JSON, not an HTML web page
header('Content-Type: application/json');

function buildDirectoryTree($dir, $baseDir = '')
{
    $html = '';
    $items = @scandir($dir);
    if (!$items) return '';

    $folders = [];
    $files = [];

    foreach ($items as $item) {
        if (strpos($item, '.') === 0) continue;
        $path = $dir . '/' . $item;
        $relativePath = $baseDir === '' ? $item : $baseDir . '/' . $item;

        if (is_dir($path)) {
            $folders[$item] = $relativePath;
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, ['php', 'html'])) {
                if ($relativePath === 'index.php' || $relativePath === 'api.php') continue;
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
            $html .= "<li><div class='folder-toggle' data-path='" . htmlspecialchars($relPath) . "'>";
            $html .= "<span class='caret caret-down'>▶</span><span class='icon'>📁</span><span class='folder-name'>" . htmlspecialchars($folderName) . "</span>";
            $html .= "</div>\n" . $subTree . "</li>\n";
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
if ($treeHtml !== '') {
    $treeHtml = preg_replace('/<ul>/', '<ul class="tree">', $treeHtml, 1);
} else {
    $treeHtml = "<p class='empty-msg'>No PHP or HTML files found.</p>";
}

// Check Database Status
mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = @new mysqli("127.0.0.1", "root", "", "lamp_dev", 3306);

$db_class = "status-bad";
$db_text = "Disconnected";
if (!$mysqli->connect_error) {
    $db_class = "status-good";
    $db_text = "Connected (lamp_dev)";
    $mysqli->close();
}

// Package everything into an associative array and serialize it to JSON
$response = [
    'tree' => $treeHtml,
    'php_version' => phpversion(),
    'db_class' => $db_class,
    'db_text' => $db_text
];

echo json_encode($response);
