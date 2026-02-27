// --- LOCAL STORAGE HELPERS ---
// A unique key so we don't accidentally overwrite data from other projects
const STORAGE_KEY = 'cms121_collapsed_folders';

// Get the saved list of closed folders from the browser's filing cabinet
function getSavedCollapsedFolders() {
    const saved = localStorage.getItem(STORAGE_KEY);
    // If we find the list, turn the JSON back into a Javascript array. If not, return an empty array.
    return saved ? JSON.parse(saved) : [];
}

// Save the list back into the browser's filing cabinet
function saveCollapsedFolders(foldersArray) {
    // LocalStorage only holds strings, so we convert the array into a JSON string
    localStorage.setItem(STORAGE_KEY, JSON.stringify(foldersArray));
}

// --- EVENT DELEGATION FOR CLICKS ---
document.addEventListener('click', function(event) {
    const toggleElement = event.target.closest('.folder-toggle');
    if (!toggleElement) return;

    const subTree = toggleElement.nextElementSibling;
    const caret = toggleElement.querySelector('.caret');
    
    // Determine the unique path of the folder that was just clicked
    const folderPath = toggleElement.getAttribute('data-path');
    
    if (subTree && subTree.tagName === 'UL') {
        // 1. Toggle the CSS classes
        const isNowCollapsed = subTree.classList.toggle('collapsed');
        caret.classList.toggle('caret-down');
        
        // 2. Update LocalStorage based on what just happened
        let savedFolders = getSavedCollapsedFolders();
        
        if (isNowCollapsed) {
            // If the user closed it, add this path to our saved list
            if (!savedFolders.includes(folderPath)) {
                savedFolders.push(folderPath);
            }
        } else {
            // If the user opened it, remove this path from our saved list
            savedFolders = savedFolders.filter(path => path !== folderPath);
        }
        
        saveCollapsedFolders(savedFolders);
    }
});

// --- THE AJAX POLLING LOGIC ---
async function fetchLiveUpdates() {
    try {
        const response = await fetch('api.php');
        const data = await response.json();

        // Diagnostics Updates
        document.getElementById('diag-php').textContent = 'v' + data.php_version;
        const dbElement = document.getElementById('diag-db');
        dbElement.textContent = data.db_text;
        dbElement.className = data.db_class;

        const treeContainer = document.getElementById('tree-container');
        
        // Only update the DOM if the files actually changed on the server
        if (treeContainer.innerHTML !== data.tree) {
            
            // 1. Overwrite the HTML (which defaults to everything being expanded)
            treeContainer.innerHTML = data.tree;

            // 2. Fetch our master list of closed folders from the browser
            const savedFolders = getSavedCollapsedFolders();

            // 3. Loop through the fresh HTML and shut any folder that is on our saved list
            document.querySelectorAll('.folder-toggle').forEach(toggle => {
                const folderPath = toggle.getAttribute('data-path');
                
                if (savedFolders.includes(folderPath)) {
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