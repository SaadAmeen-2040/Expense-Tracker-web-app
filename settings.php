<?php
require_once 'api/session_check.php';
checkLogin();

$pageTitle = "Settings";
$currentPage = 'settings';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">
    <header class="glass-panel header-container">
        <div class="header-titles">
            <h1>Account Settings</h1>
            <p class="text-muted">Manage your profile and security</p>
        </div>
    </header>

    <main class="grid-layout" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Email Settings -->
        <section class="glass-panel">
            <div class="section-header">
                <h3>Email Address</h3>
            </div>
            <form id="email-form" class="expense-form-container">
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <svg class="icon icon-sm input-icon"><use href="#ic-note"/></svg>
                        <input type="email" id="email" placeholder="you@example.com" required>
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="btn-save-email">
                    Save Email
                </button>
            </form>
        </section>

        <!-- Password Settings -->
        <section class="glass-panel">
            <div class="section-header">
                <h3>Change Password</h3>
            </div>
            <form id="password-form" class="expense-form-container">
                <div class="input-group">
                    <label for="old_password">Current Password</label>
                    <div class="input-wrapper">
                        <svg class="icon icon-sm input-icon"><use href="#ic-target"/></svg>
                        <input type="password" id="old_password" placeholder="********" required>
                    </div>
                </div>
                <div class="input-group">
                    <label for="new_password">New Password</label>
                    <div class="input-wrapper">
                        <svg class="icon icon-sm input-icon"><use href="#ic-pen"/></svg>
                        <input type="password" id="new_password" placeholder="********" required minlength="6">
                    </div>
                </div>
                <div class="input-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="input-wrapper">
                        <svg class="icon icon-sm input-icon"><use href="#ic-check"/></svg>
                        <input type="password" id="confirm_password" placeholder="********" required minlength="6">
                    </div>
                </div>
                <button type="submit" class="btn-primary" id="btn-save-password">
                    Update Password
                </button>
            </form>
        </section>
    </main>
</div>

<?php 
$extraScripts = ['settings.js'];
include 'includes/footer.php'; 
?>
