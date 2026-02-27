<style>
    /* * We use a highly specific ID to ensure this CSS 
     * never conflicts with the CSS of your actual projects.
     */
    #global-dev-home-btn {
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: #1e1e2e;
        color: #cdd6f4;
        padding: 10px 15px;
        border: 1px solid #45475a;
        border-radius: 8px;
        font-family: "JetBrains Mono", "Courier New", monospace;
        font-size: 14px;
        text-decoration: none;
        opacity: 0.2; /* Semi-transparent by default */
        transition: all 0.3s ease;
        z-index: 999999; /* Ensure it stays on top of everything */
        box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    }

    /* Reveal the button when the user hovers near it */
    #global-dev-home-btn:hover {
        opacity: 1;
        background: #313244;
        transform: translateY(-2px);
    }
</style>

<a href="/index.php" id="global-dev-home-btn">🏠 Home Index</a>