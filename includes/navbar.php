<nav class="navbar" id="main-nav">
    <div class="nav-content">
        <a href="index.php" class="nav-logo">
            <div class="logo-icon">
                <svg class="icon icon-md"><use href="#ic-wallet"/></svg>
            </div>
            <span class="logo-text">Expense Tracker</span>
        </a>

        <div class="nav-links-container" id="nav-links">
            <a href="index.php" class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">Dashboard</a>
            <?php if ($currentPage == 'dashboard'): ?>
                <a href="#analytics-title" class="nav-link">Analytics</a>
                <a href="#history-title" class="nav-link">History</a>
            <?php endif; ?>
            <a href="settings.php" class="nav-link <?php echo ($currentPage == 'settings') ? 'active' : ''; ?>">Settings</a>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin.php" class="nav-link <?php echo ($currentPage == 'admin') ? 'active' : ''; ?>">Admin</a>
            <?php endif; ?>
            <a href="index.php#about" class="nav-link">About</a>
        </div>

        <div class="nav-actions">
            <div class="user-profile">
                <div class="user-pill">
                    <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?></div>
                    <span class="user-name"><?php echo $_SESSION['username'] ?? 'User'; ?></span>
                </div>
                <div class="user-dropdown">
                    <a href="settings.php" class="dropdown-item">
                        <svg class="icon icon-xs"><use href="#ic-settings"/></svg> Profile Settings
                    </a>
                    <a href="api/logout.php" class="dropdown-item logout">
                        <svg class="icon icon-xs"><use href="#ic-logout"/></svg> Logout
                    </a>
                </div>
            </div>

            <div class="action-buttons">
                <button id="theme-toggle" class="btn-icon-action" aria-label="Toggle Theme">
                    <svg class="icon icon-sm" id="theme-icon"><use href="#ic-sun"/></svg>
                </button>
                <button class="mobile-nav-toggle" id="mobile-toggle" aria-label="Toggle menu">
                    <svg class="icon icon-sm" id="mobile-toggle-icon"><use href="#ic-list"/></svg>
                </button>
            </div>
        </div>
    </div>
</nav>
