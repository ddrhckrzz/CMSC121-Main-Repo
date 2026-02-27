// UX Helper: Toggle folder visibility
function toggleFolder(element) {
    var subTree = element.nextElementSibling;
    var caret = element.querySelector('.caret');
    if (subTree && subTree.tagName === 'UL') {
        subTree.classList.toggle('collapsed');
        caret.classList.toggle('caret-down');
    }
}

async function fetchLiveUpdates() {
    try {
        const response = await fetch('api.php');
        const data = await response.json();

        // Update Diagnostics
        document.getElementById('diag-php').textContent = 'v' + data.php_version;
        const dbElement = document.getElementById('diag-db');
        dbElement.textContent = data.db_text;
        dbElement.className = data.db_class;

        const treeContainer = document.getElementById('tree-container');

        // Only manipulate the DOM if the server actually detects a change
        if (treeContainer.innerHTML !== data.tree) {

            // --- 1. MEMORIZE STATE ---
            // Create a list of the paths of every folder that is currently collapsed
            const collapsedFolders = new Set();
            document.querySelectorAll('.folder-toggle').forEach(toggle => {
                const subTree = toggle.nextElementSibling;
                if (subTree && subTree.classList.contains('collapsed')) {
                    collapsedFolders.add(toggle.getAttribute('data-path'));
                }
            });

            // --- 2. REPLACE HTML ---
            treeContainer.innerHTML = data.tree;

            // --- 3. RESTORE STATE ---
            // Loop through the brand new elements and re-collapse the memorized ones
            document.querySelectorAll('.folder-toggle').forEach(toggle => {
                if (collapsedFolders.has(toggle.getAttribute('data-path'))) {
                    toggle.nextElementSibling.classList.add('collapsed');
                    toggle.querySelector('.caret').classList.remove('caret-down');
                }
            });
        }

    } catch (error) {
        console.error("AJAX Error: Could not reach api.php", error);
        document.getElementById('diag-db').textContent = "Server Unreachable";
        document.getElementById('diag-db').className = "status-bad";
    }
}

fetchLiveUpdates();
setInterval(fetchLiveUpdates, 2000);