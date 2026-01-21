<div style="padding: 5rem 1rem; max-width: 800px; margin: 0 auto; line-height: 1.6;">
    <h1 style="font-size: 3rem; color: var(--accent); margin-bottom: 1rem;">HTTP 404 - Not Found</h1>
    <div style="background: var(--bg-card); border-left: 4px solid var(--accent); padding: 2rem; border-radius: 8px;">
        <h2 style="margin-bottom: 1rem; font-size: 1.2rem;">ERROR_LOG: Resource Requested Not Available</h2>
        <p style="color: var(--text-muted); font-family: monospace; font-size: 0.9rem;">
            [Request_URI]:
            <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?><br>
            [Controller]: index.php<br>
            [Action]: LoadView<br>
            [Status]: Failure - File not found in ./views/
            <?= htmlspecialchars($page) ?>.php
        </p>
        <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 1.5rem 0;">
        <p>
            The requested resource could not be found on this server. This may be due to:
        <ul style="margin-left: 2rem; margin-top: 0.5rem;">
            <li>An invalid 'page' parameter in the query string.</li>
            <li>The file was moved or deleted from the internal directory.</li>
            <li>Manual URL tampering.</li>
        </ul>
        </p>
    </div>
    <div style="margin-top: 2rem; text-align: center;">
        <a href="?page=home" class="btn">Return to Dashboard</a>
    </div>
</div>