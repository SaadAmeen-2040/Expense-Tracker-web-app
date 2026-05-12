<?php
require_once 'api/session_check.php';
checkLogin(true); // Require Admin

$pageTitle = "Admin Dashboard";
$currentPage = 'admin';
include 'includes/header.php';
include 'includes/navbar.php';
?>

    <div class="container">
        <header class="glass-panel header-container">
            <div class="header-titles">
                <h1>Admin Dashboard</h1>
                <p class="text-muted">System Overview & User Management</p>
            </div>
        </header>

        <section class="stat-cards">
            <div class="stat-card">
                <div class="stat-icon stat-icon--purple">
                    <svg class="icon icon-md"><use href="#ic-wallet"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Total Platform Spent</span>
                    <span class="stat-value" id="admin-total-spent">Rs 0.00</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--cyan">
                    <svg class="icon icon-md"><use href="#ic-list"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Active Users</span>
                    <span class="stat-value" id="admin-total-users">0</span>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon stat-icon--pink">
                    <svg class="icon icon-md"><use href="#ic-fire"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Total Transactions</span>
                    <span class="stat-value" id="admin-total-expenses">0</span>
                </div>
            </div>
        </section>

        <main class="grid-layout" style="grid-template-columns: 1fr;">
            <section class="glass-panel">
                <div class="section-header">
                    <h3>Registered Users</h3>
                </div>
                <div class="history-list" id="admin-user-list">
                    <div class="loading">Loading users...</div>
                </div>
            </section>
        </main>
    </div>

<?php 
$extraScripts = ['admin.js'];
include 'includes/footer.php'; 
?>
