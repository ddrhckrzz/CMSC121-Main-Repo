<?php
header('Content-Type: application/json');

/**
 * Reads the .indexignore file and returns an array of patterns
 */
function getExclusionPatterns($ignoreFilePath) {
    if (!file_exists($ignoreFilePath)) return [];
    
    // Read the file into an array, ignoring empty lines and newlines
    $lines = file($ignoreFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $patterns = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments (lines starting with #)
        if ($line !== '' && strpos($line, '#') !== 0) {
            $patterns[] = $line;
        }
    }
    return $patterns;
}

/**
 * Checks if a file or folder matches any of the ignore patterns
 */
function isExcluded($itemName, $relativePath, $patterns) {
    // We always skip the current and parent directory pointers
    if ($itemName === '.' || $itemName === '..') return true;
    
    foreach ($patterns as $pattern) {
        // fnmatch checks against shell wildcard patterns (like *.php)
        // We check both the exact filename and the relative path (e.g., "test/secret.php")
        if (fnmatch($pattern, $itemName) || fnmatch($pattern, $relativePath)) {
            return true;
        }
    }
    return false;
}

function buildDirectoryTree($dir, $baseDir = '', $ignorePatterns = []) {
    $html = '';
    $items = @scandir($dir);
    if (!$items) return '';

    $folders = [];
    $files = [];

    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        $relativePath = $baseDir === '' ? $item : $baseDir . '/' . $item;

        // --- NEW: Check our ignore list ---
        if (isExcluded($item, $relativePath, $ignorePatterns)) {
            continue; 
        }

        if (is_dir($path)) {
            $folders[$item] = $relativePath;
        } else {
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            // You can still restrict to specific extensions here if you want, 
            // or remove this if statement to list ALL non-ignored files.
            if (in_array($ext, ['php', 'html'])) {
                $files[$item] = $relativePath;
            }
        }
    }

    if (empty($folders) && empty($files)) return ''; 

    $html .= "<ul>\n";
    ksort($folders);
    foreach ($folders as $folderName => $relPath) {
        $subTree = buildDirectoryTree($dir . '/' . $folderName, $relPath, $ignorePatterns);
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

// 1. Load the rules from the text file
$ignorePatterns = getExclusionPatterns(__DIR__ . '/.indexignore');

// 2. Pass the rules into our recursive scanner
$treeHtml = buildDirectoryTree(__DIR__, '', $ignorePatterns);

if ($treeHtml !== '') {
    $treeHtml = preg_replace('/<ul>/', '<ul class="tree">', $treeHtml, 1);
} else {
    $treeHtml = "<p class='empty-msg'>No files found to display.</p>";
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

$response = [
    'tree' => $treeHtml,
    'php_version' => phpversion(),
    'db_class' => $db_class,
    'db_text' => $db_text
];

echo json_encode($response);
exit;