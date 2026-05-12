<?php
require_once 'api/session_check.php';
checkLogin();

$pageTitle = "Dashboard";
$currentPage = 'dashboard';
include 'includes/header.php';
include 'includes/navbar.php';
?>

    <!-- Custom Delete Confirmation Modal -->
    <div id="delete-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title" hidden>
        <div class="modal-card">
            <div class="modal-icon">
                <svg class="icon icon-lg"><use href="#ic-trash"/></svg>
            </div>
            <h3 id="modal-title">Delete Expense?</h3>
            <p>This action cannot be undone. Are you sure?</p>
            <div class="modal-actions">
                <button id="modal-cancel" class="btn-modal-cancel">Cancel</button>
                <button id="modal-confirm" class="btn-modal-confirm">
                    <svg class="icon icon-sm"><use href="#ic-trash"/></svg> Delete
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Dashboard Header -->
        <header class="glass-panel header-container" role="banner">
            <div class="header-titles">
                <h1>Overview</h1>
                <div class="header-actions">
                    <button id="btn-export-csv" onclick="exportCSV()" class="btn-action">
                        <svg class="icon icon-sm" aria-hidden="true"><use href="#ic-download"/></svg>
                        Export CSV
                    </button>
                </div>
            </div>
            <div class="balance-container">
                <p>Total Spent</p>
                <h2 id="total-balance" aria-live="polite">Rs 0.00</h2>
            </div>
        </header>

        <!-- Stat Cards -->
        <section class="stat-cards" aria-label="Summary statistics">
            <div class="stat-card" id="stat-monthly">
                <div class="stat-icon stat-icon--purple">
                    <svg class="icon icon-md"><use href="#ic-calendar"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">This Month</span>
                    <span class="stat-value" id="stat-monthly-val">Rs 0.00</span>
                </div>
            </div>
            <div class="stat-card" id="stat-top-cat">
                <div class="stat-icon stat-icon--pink">
                    <svg class="icon icon-md"><use href="#ic-fire"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Top Category</span>
                    <span class="stat-value" id="stat-top-cat-val">—</span>
                </div>
            </div>
            <div class="stat-card" id="stat-count">
                <div class="stat-icon stat-icon--cyan">
                    <svg class="icon icon-md"><use href="#ic-list"/></svg>
                </div>
                <div class="stat-body">
                    <span class="stat-label">Total Entries</span>
                    <span class="stat-value" id="stat-count-val">0</span>
                </div>
            </div>
            <div class="stat-card stat-card--budget" id="stat-budget-card">
                <div class="stat-icon stat-icon--green">
                    <svg class="icon icon-md"><use href="#ic-target"/></svg>
                </div>
                <div class="stat-body stat-body--full">
                    <div class="budget-header">
                        <span class="stat-label">Monthly Budget</span>
                        <button id="btn-set-budget" class="btn-set-budget" aria-label="Set monthly budget" title="Set budget">
                            <svg class="icon icon-xs"><use href="#ic-pen"/></svg>
                        </button>
                    </div>
                    <div class="budget-values">
                        <span id="budget-used">Rs 0.00</span>
                        <span id="budget-limit">/ Rs 0</span>
                    </div>
                    <div class="budget-bar-track" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="Budget used">
                        <div class="budget-bar-fill" id="budget-bar-fill"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Budget Input (hidden) -->
        <div id="budget-input-row" class="budget-input-row hidden">
            <div class="budget-input-wrapper">
                <svg class="icon icon-sm" aria-hidden="true"><use href="#ic-rupee"/></svg>
                <input type="number" id="budget-amount-input" placeholder="Enter monthly budget (Rs)" min="1" step="1" aria-label="Monthly budget amount">
            </div>
            <button id="btn-save-budget" class="btn-primary" style="width:auto; padding: 0.7rem 1.4rem; margin-top:0;">
                <svg class="icon icon-sm" aria-hidden="true"><use href="#ic-check"/></svg> Save
            </button>
        </div>

        <!-- Main Content -->
        <main class="grid-layout" role="main">
            <!-- Add / Edit Expense Form -->
            <section class="add-expense glass-panel" aria-labelledby="form-title">
                <h3 id="form-title">
                    <span class="form-title-icon">
                        <svg class="icon icon-sm" aria-hidden="true" id="form-title-icon"><use href="#ic-plus"/></svg>
                    </span>
                    <span id="form-title-text">Add New Expense</span>
                </h3>
                <form id="expense-form" class="expense-form-container" novalidate>
                    <input type="hidden" id="edit-id">

                    <div class="input-group">
                        <label for="title">Title</label>
                        <div class="input-wrapper" data-field="title">
                            <svg class="icon icon-sm input-icon" aria-hidden="true"><use href="#ic-tag"/></svg>
                            <input type="text" id="title" placeholder="e.g. Groceries" required maxlength="80" autocomplete="off">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="amount">Amount (Rs)</label>
                        <div class="input-wrapper" data-field="amount">
                            <svg class="icon icon-sm input-icon" aria-hidden="true"><use href="#ic-rupee"/></svg>
                            <input type="number" id="amount" placeholder="0.00" step="0.01" min="0.01" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="category">Category</label>
                        <div class="input-wrapper" data-field="category">
                            <svg class="icon icon-sm input-icon" aria-hidden="true"><use href="#ic-grid"/></svg>
                            <select id="category" required>
                                <option value="" disabled selected>Select category</option>
                                <option value="Food">&#127869; Food &amp; Dining</option>
                                <option value="Transport">&#128663; Transportation</option>
                                <option value="Utilities">&#9889; Utilities</option>
                                <option value="Entertainment">&#127916; Entertainment</option>
                                <option value="Shopping">&#128717; Shopping</option>
                                <option value="Health">&#127973; Health</option>
                                <option value="Education">&#128218; Education</option>
                                <option value="Other">&#127991; Other</option>
                            </select>
                            <svg class="icon icon-xs select-arrow" aria-hidden="true"><use href="#ic-chevron-down"/></svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="date">Date</label>
                        <div class="input-wrapper" data-field="date">
                            <svg class="icon icon-sm input-icon" aria-hidden="true"><use href="#ic-calendar"/></svg>
                            <input type="date" id="date" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="notes">Notes <span class="label-optional">(optional)</span></label>
                        <div class="input-wrapper input-wrapper--textarea" data-field="notes">
                            <svg class="icon icon-sm input-icon textarea-icon" aria-hidden="true"><use href="#ic-note"/></svg>
                            <textarea id="notes" placeholder="Any additional details..." rows="3" maxlength="200"></textarea>
                        </div>
                    </div>

                    <div class="form-btn-group">
                        <button type="submit" id="btn-submit" class="btn-primary">
                            <svg class="icon icon-sm" aria-hidden="true" id="btn-submit-icon"><use href="#ic-plus"/></svg>
                            <span id="btn-submit-text">Add Expense</span>
                        </button>
                        <button type="button" id="btn-cancel-edit" class="btn-secondary hidden">
                            <svg class="icon icon-sm" aria-hidden="true"><use href="#ic-x"/></svg>
                            Cancel
                        </button>
                    </div>
                </form>
            </section>

            <!-- Right Column -->
            <div class="right-column">
                <!-- Analytics -->
                <section class="dashboard-analytics glass-panel" aria-labelledby="analytics-title">
                    <div class="section-header">
                        <h3 id="analytics-title">Analytics Overview</h3>
                        <div class="trend-toggle" role="group" aria-label="Trend period">
                            <button id="trend-7" class="trend-btn active" data-days="7" aria-pressed="true">7D</button>
                            <button id="trend-30" class="trend-btn" data-days="30" aria-pressed="false">30D</button>
                        </div>
                    </div>
                    <div class="charts-container">
                        <div class="chart-wrapper">
                            <canvas id="categoryChart" aria-label="Spending by category" role="img"></canvas>
                        </div>
                        <div class="chart-wrapper">
                            <canvas id="trendChart" aria-label="Spending trend" role="img"></canvas>
                        </div>
                    </div>
                </section>

                <!-- Expense History -->
                <section class="expense-history glass-panel" aria-labelledby="history-title">
                    <div class="section-header">
                        <h3 id="history-title">Recent Expenses</h3>
                        <span class="expense-count-badge" id="expense-count-badge">0</span>
                    </div>

                    <div class="filter-bar">
                        <div class="search-wrapper">
                            <svg class="icon icon-sm search-icon" aria-hidden="true"><use href="#ic-search"/></svg>
                            <input type="search" id="search-input" placeholder="Search expenses..." aria-label="Search expenses">
                        </div>
                        <select id="filter-category" aria-label="Filter by category">
                            <option value="">All Categories</option>
                            <option value="Food">Food &amp; Dining</option>
                            <option value="Transport">Transportation</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Shopping">Shopping</option>
                            <option value="Health">Health</option>
                            <option value="Education">Education</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="history-list" id="expense-list" aria-live="polite" aria-label="Expense list">
                        <div class="loading">
                            <svg class="icon icon-md spin" aria-hidden="true"><use href="#ic-spinner"/></svg>
                            Loading...
                        </div>
                    </div>
                </section>
            </div>
        </main>

        <!-- About Us Section -->
        <section id="about" class="glass-panel about-section" aria-labelledby="about-title">
            <div class="section-header">
                <h3 id="about-title">About Expense Tracker</h3>
            </div>
            <div class="about-grid">
                <div class="about-info">
                    <p>Expense Tracker is a high-performance personal finance tool designed for clarity and speed. Our mission is to provide a beautiful, private, and intuitive experience for managing your daily expenses.</p>
                    <ul class="about-features">
                        <li><svg class="icon icon-xs"><use href="#ic-check"/></svg> Real-time expense analytics</li>
                        <li><svg class="icon icon-xs"><use href="#ic-check"/></svg> Secure local-first storage</li>
                        <li><svg class="icon icon-xs"><use href="#ic-check"/></svg> Exportable CSV data</li>
                        <li><svg class="icon icon-xs"><use href="#ic-check"/></svg> Responsive glassmorphic design</li>
                    </ul>
                </div>
                <div class="about-badge">
                    <div class="stat-icon stat-icon--purple">
                        <svg class="icon icon-lg"><use href="#ic-wallet"/></svg>
                    </div>
                </div>
            </div>
        </section>
    </div>

<?php 
$extraScripts = ['app.js'];
include 'includes/footer.php'; 
?>
