/* ═══════════════════════════════════════════════
    EXPENSE TRACKER — app.js
═══════════════════════════════════════════════ */

// ── SVG icon helper (matches sprite in index.html) ────
function svgIcon(id, sizeClass = 'icon-sm') {
    return `<svg class="icon ${sizeClass}" aria-hidden="true"><use href="#${id}"/></svg>`;
}

// ── DOM References ────────────────────────────
const expenseForm       = document.getElementById('expense-form');
const expenseList       = document.getElementById('expense-list');
const totalBalanceEl    = document.getElementById('total-balance');
const editIdInput       = document.getElementById('edit-id');
const btnSubmit         = document.getElementById('btn-submit');
const btnSubmitText     = document.getElementById('btn-submit-text');
const btnCancelEdit     = document.getElementById('btn-cancel-edit');
const searchInput       = document.getElementById('search-input');
const filterCategory    = document.getElementById('filter-category');
const expenseCountBadge = document.getElementById('expense-count-badge');

// Stat cards
const statMonthlyVal  = document.getElementById('stat-monthly-val');
const statTopCatVal   = document.getElementById('stat-top-cat-val');
const statCountVal    = document.getElementById('stat-count-val');
const budgetUsedEl    = document.getElementById('budget-used');
const budgetLimitEl   = document.getElementById('budget-limit');
const budgetBarFill   = document.getElementById('budget-bar-fill');
const budgetBarTrack  = document.querySelector('.budget-bar-track');

// Budget input row
const btnSetBudget       = document.getElementById('btn-set-budget');
const budgetInputRow     = document.getElementById('budget-input-row');
const budgetAmountInput  = document.getElementById('budget-amount-input');
const btnSaveBudget      = document.getElementById('btn-save-budget');

// Trend toggle
const trendButtons = document.querySelectorAll('.trend-btn');

// Delete modal
const deleteModal   = document.getElementById('delete-modal');
const modalCancel   = document.getElementById('modal-cancel');
const modalConfirm  = document.getElementById('modal-confirm');

// ── State ─────────────────────────────────────
let expenses       = [];
let monthlyBudget  = 0;
let trendDays      = 7;          // 7 or 30
let pendingDeleteId        = null;
let pendingDeleteElement   = null;
let animatedBalance        = 0;  // tracks current displayed value for animation
let balanceAnimFrame       = null;

// ── Category Config ───────────────────────────

// ═══════════════════════════════════════════════
// THEME MANAGEMENT
// ═══════════════════════════════════════════════
const themeToggleBtn = document.getElementById('theme-toggle');

function applyTheme(isLight) {
    document.body.classList.toggle('light-mode', isLight);
    const themeIconEl = document.getElementById('theme-icon');
    if (themeIconEl) {
        themeIconEl.querySelector('use').setAttribute('href', isLight ? '#ic-moon' : '#ic-sun');
    }
}

// Apply saved theme on load
applyTheme(localStorage.getItem('theme') === 'light');

themeToggleBtn.addEventListener('click', () => {
    const isLight = !document.body.classList.contains('light-mode');
    localStorage.setItem('theme', isLight ? 'light' : 'dark');
    applyTheme(isLight);
    drawCharts();
});

// ═══════════════════════════════════════════════
// NAVBAR & MOBILE MENU
// ═══════════════════════════════════════════════
const mobileToggle = document.getElementById('mobile-toggle');
const navLinks     = document.getElementById('nav-links');
const navLinkItems = document.querySelectorAll('.nav-link');

if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        navLinks.classList.toggle('show');
        const iconUse = document.getElementById('mobile-toggle-icon').querySelector('use');
        const isShowing = navLinks.classList.contains('show');
        iconUse.setAttribute('href', isShowing ? '#ic-x' : '#ic-list');
    });
}

// Close mobile menu when a link is clicked + update active state
navLinkItems.forEach(link => {
    link.addEventListener('click', () => {
        navLinks.classList.remove('show');
        const toggleIcon = document.getElementById('mobile-toggle-icon');
        if (toggleIcon) toggleIcon.querySelector('use').setAttribute('href', '#ic-list');
        
        navLinkItems.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
    });
});


// ═══════════════════════════════════════════════
// DEFAULT DATE
// ═══════════════════════════════════════════════
document.getElementById('date').valueAsDate = new Date();

// ═══════════════════════════════════════════════
// LOCALSTORAGE HELPERS
// ═══════════════════════════════════════════════
function loadExpenses() {
    try {
        const stored = localStorage.getItem('expenses');
        expenses = stored ? JSON.parse(stored) : [];
    } catch {
        expenses = [];
    }
}

function saveExpenses() {
    localStorage.setItem('expenses', JSON.stringify(expenses));
}

function loadBudget() {
    monthlyBudget = parseFloat(localStorage.getItem('monthlyBudget') || '0');
    if (monthlyBudget > 0) {
        budgetAmountInput.value = monthlyBudget;
    }
}

function saveBudget(val) {
    monthlyBudget = val;
    localStorage.setItem('monthlyBudget', val);
}

// ═══════════════════════════════════════════════
// ANIMATED BALANCE COUNTER
// ═══════════════════════════════════════════════
function animateBalance(targetValue) {
    if (balanceAnimFrame) cancelAnimationFrame(balanceAnimFrame);

    const start      = animatedBalance;
    const diff       = targetValue - start;
    const duration   = 600; // ms
    const startTime  = performance.now();

    function step(now) {
        const elapsed  = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out cubic
        const eased    = 1 - Math.pow(1 - progress, 3);
        const current  = start + diff * eased;

        totalBalanceEl.textContent = `Rs ${current.toFixed(2)}`;

        if (progress < 1) {
            balanceAnimFrame = requestAnimationFrame(step);
        } else {
            animatedBalance  = targetValue;
            totalBalanceEl.textContent = `Rs ${targetValue.toFixed(2)}`;
        }
    }

    balanceAnimFrame = requestAnimationFrame(step);
}

// ═══════════════════════════════════════════════
// SORT EXPENSES (newest date first, then by ID)
// ═══════════════════════════════════════════════
function sortExpenses() {
    expenses.sort((a, b) => {
        const dateDiff = new Date(b.date) - new Date(a.date);
        if (dateDiff !== 0) return dateDiff;
        return parseInt(b.id) - parseInt(a.id);
    });
}

// ═══════════════════════════════════════════════
// FORM SUBMISSION — Add / Edit
// ═══════════════════════════════════════════════
expenseForm.addEventListener('submit', (e) => {
    e.preventDefault();
    if (btnSubmit.disabled) return;

    const title    = document.getElementById('title').value.trim();
    const amount   = parseFloat(document.getElementById('amount').value);
    const category = document.getElementById('category').value;
    const date     = document.getElementById('date').value;
    const notes    = document.getElementById('notes').value.trim();
    const editId   = editIdInput.value;

    if (!title || isNaN(amount) || amount <= 0 || !category || !date) {
        showMessage('Please fill in all required fields.', 'error');
        return;
    }

    // Disable button to prevent double-submit
    const originalHTML = btnSubmit.innerHTML;
    btnSubmit.innerHTML = `${svgIcon('ic-spinner')} Saving…`;
    btnSubmit.disabled  = true;

    setTimeout(() => {
        if (editId) {
            // ── Edit mode ──
            const idx = expenses.findIndex(ex => String(ex.id) === String(editId));
            if (idx !== -1) {
                expenses[idx] = { ...expenses[idx], title, amount, category, date, notes };
            }
            showMessage('Expense updated!', 'success');
            cancelEdit();
        } else {
            // ── Add mode ──
            const newExpense = {
                id: Date.now().toString(),
                title,
                amount,
                category,
                date,
                notes
            };
            expenses.unshift(newExpense);
            showMessage('Expense added!', 'success');
            expenseForm.reset();
            document.getElementById('date').valueAsDate = new Date();
        }

        sortExpenses();
        saveExpenses();
        updateUI();

        btnSubmit.innerHTML = originalHTML;
        btnSubmit.disabled  = false;
    }, 180);
});

// ═══════════════════════════════════════════════
// EDIT EXPENSE
// ═══════════════════════════════════════════════
function editExpense(id) {
    const expense = expenses.find(ex => String(ex.id) === String(id));
    if (!expense) return;

    // Pre-fill form
    editIdInput.value = expense.id;
    document.getElementById('title').value    = expense.title;
    document.getElementById('amount').value   = expense.amount;
    document.getElementById('category').value = expense.category;
    document.getElementById('date').value     = expense.date;
    document.getElementById('notes').value    = expense.notes || '';


    // Update form UI to "edit mode"
    const formTitleIcon = document.getElementById('form-title-icon');
    if (formTitleIcon) formTitleIcon.querySelector('use').setAttribute('href', '#ic-pen');
    document.getElementById('form-title-text').textContent = 'Edit Expense';
    btnSubmitText.textContent = 'Save Changes';
    // Swap submit icon to check
    const submitIconEl = document.getElementById('btn-submit-icon');
    if (submitIconEl) submitIconEl.querySelector('use').setAttribute('href', '#ic-check');
    btnCancelEdit.classList.remove('hidden');

    // Scroll form into view
    expenseForm.closest('section').scrollIntoView({ behavior: 'smooth', block: 'start' });
    document.getElementById('title').focus();
}

function cancelEdit() {
    editIdInput.value = '';
    expenseForm.reset();
    document.getElementById('date').valueAsDate = new Date();
    const formTitleIcon = document.getElementById('form-title-icon');
    if (formTitleIcon) formTitleIcon.querySelector('use').setAttribute('href', '#ic-plus');
    document.getElementById('form-title-text').textContent = 'Add New Expense';
    btnSubmitText.textContent = 'Add Expense';
    const submitIconEl = document.getElementById('btn-submit-icon');
    if (submitIconEl) submitIconEl.querySelector('use').setAttribute('href', '#ic-plus');
    btnCancelEdit.classList.add('hidden');
}

btnCancelEdit.addEventListener('click', cancelEdit);

// ═══════════════════════════════════════════════
// DELETE EXPENSE — Custom Modal
// ═══════════════════════════════════════════════
function deleteExpense(id, buttonElement) {
    pendingDeleteId      = id;
    pendingDeleteElement = buttonElement;
    deleteModal.hidden   = false;
    modalConfirm.focus();
}

modalCancel.addEventListener('click', () => {
    deleteModal.hidden   = true;
    pendingDeleteId      = null;
    pendingDeleteElement = null;
});

// Close modal on overlay click
deleteModal.addEventListener('click', (e) => {
    if (e.target === deleteModal) {
        deleteModal.hidden = true;
        pendingDeleteId = null;
        pendingDeleteElement = null;
    }
});

// Close modal on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !deleteModal.hidden) {
        deleteModal.hidden = true;
        pendingDeleteId = null;
        pendingDeleteElement = null;
    }
});

modalConfirm.addEventListener('click', () => {
    deleteModal.hidden = true;

    if (!pendingDeleteId) return;

    const id            = pendingDeleteId;
    const buttonElement = pendingDeleteElement;
    pendingDeleteId      = null;
    pendingDeleteElement = null;

    // Animate item out
    if (buttonElement) {
        const item = buttonElement.closest('.expense-item');
        if (item) {
            item.classList.add('deleting');
            setTimeout(() => {
                expenses = expenses.filter(ex => String(ex.id) !== String(id));
                saveExpenses();
                updateUI();
                showMessage('Expense deleted', 'success');
            }, 320);
            return;
        }
    }

    expenses = expenses.filter(ex => String(ex.id) !== String(id));
    saveExpenses();
    updateUI();
    showMessage('Expense deleted', 'success');
});

// ═══════════════════════════════════════════════
// SEARCH & FILTER
// ═══════════════════════════════════════════════
searchInput.addEventListener('input', renderExpenseList);
filterCategory.addEventListener('change', renderExpenseList);

function getFilteredExpenses() {
    const query    = searchInput.value.trim().toLowerCase();
    const catFilter = filterCategory.value;

    return expenses.filter(ex => {
        const matchesCat   = !catFilter || ex.category === catFilter;
        const matchesQuery = !query ||
            ex.title.toLowerCase().includes(query) ||
            (ex.notes && ex.notes.toLowerCase().includes(query)) ||
            ex.category.toLowerCase().includes(query);
        return matchesCat && matchesQuery;
    });
}

// ═══════════════════════════════════════════════
// BUDGET
// ═══════════════════════════════════════════════
btnSetBudget.addEventListener('click', () => {
    budgetInputRow.classList.toggle('hidden');
    if (!budgetInputRow.classList.contains('hidden')) {
        budgetAmountInput.focus();
    }
});

btnSaveBudget.addEventListener('click', () => {
    const val = parseFloat(budgetAmountInput.value);
    if (isNaN(val) || val <= 0) {
        showMessage('Enter a valid budget amount', 'error');
        return;
    }
    saveBudget(val);
    budgetInputRow.classList.add('hidden');
    updateStatCards();
    showMessage(`Budget set to Rs ${val.toFixed(2)}`, 'info');
});

budgetAmountInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') btnSaveBudget.click();
});

function updateBudgetBar(monthlySpent) {
    budgetUsedEl.textContent = `Rs ${monthlySpent.toFixed(2)}`;

    if (monthlyBudget > 0) {
        const pct = Math.min((monthlySpent / monthlyBudget) * 100, 100);
        budgetLimitEl.textContent = `/ Rs ${monthlyBudget.toFixed(0)}`;
        budgetBarFill.style.width = `${pct}%`;
        budgetBarTrack.setAttribute('aria-valuenow', Math.round(pct));

        budgetBarFill.classList.remove('warn', 'over');
        if (pct >= 100)       budgetBarFill.classList.add('over');
        else if (pct >= 75)   budgetBarFill.classList.add('warn');
    } else {
        budgetLimitEl.textContent = '/ not set';
        budgetBarFill.style.width = '0%';
        budgetBarTrack.setAttribute('aria-valuenow', 0);
    }
}

// ═══════════════════════════════════════════════
// STAT CARDS
// ═══════════════════════════════════════════════
function updateStatCards() {
    const now        = new Date();
    const thisMonth  = now.getMonth();
    const thisYear   = now.getFullYear();

    let monthlySpent = 0;
    const categoryTotals = {};

    expenses.forEach(ex => {
        const d = new Date(ex.date + 'T00:00:00'); // avoid UTC offset
        if (d.getMonth() === thisMonth && d.getFullYear() === thisYear) {
            monthlySpent += parseFloat(ex.amount);
        }
        categoryTotals[ex.category] = (categoryTotals[ex.category] || 0) + parseFloat(ex.amount);
    });

    statMonthlyVal.textContent = `Rs ${monthlySpent.toFixed(2)}`;
    statCountVal.textContent   = expenses.length;

    // Top category
    const topCat = Object.entries(categoryTotals).sort((a, b) => b[1] - a[1])[0];
    statTopCatVal.textContent = topCat ? topCat[0] : '—';

    updateBudgetBar(monthlySpent);
}

// ═══════════════════════════════════════════════
// RENDER EXPENSE LIST
// ═══════════════════════════════════════════════
function renderExpenseList() {
    const filtered = getFilteredExpenses();

    expenseCountBadge.textContent = filtered.length;
    expenseList.innerHTML = '';

    if (filtered.length === 0) {
        const hasAny = expenses.length > 0;
        expenseList.innerHTML = `
            <div class="empty-state">
                ${svgIcon(hasAny ? 'ic-no-results' : 'ic-receipt', 'icon-xl')}
                <h4>${hasAny ? 'No matches found' : 'No expenses yet'}</h4>
                <p>${hasAny ? 'Try adjusting your search or filter.' : 'Add your first expense to get started.'}</p>
            </div>`;
        return;
    }

    // Category → SVG sprite id map
    const catIconMap = {
        Food:          'ic-cat-food',
        Transport:     'ic-cat-transport',
        Utilities:     'ic-cat-utilities',
        Entertainment: 'ic-cat-entertainment',
        Shopping:      'ic-cat-shopping',
        Health:        'ic-cat-health',
        Education:     'ic-cat-education',
        Other:         'ic-tag'
    };

    filtered.forEach(expense => {
        const item = document.createElement('div');
        item.classList.add('expense-item');
        item.dataset.cat = expense.category;

        const amountNum     = parseFloat(expense.amount);
        const formattedDate = formatDate(expense.date);
        const catIcon       = svgIcon(catIconMap[expense.category] || 'ic-tag', 'icon-xs');
        const notesHTML     = expense.notes
            ? `<span class="expense-notes" title="${escapeHTML(expense.notes)}">
                   ${svgIcon('ic-note', 'icon-xs')} ${escapeHTML(expense.notes)}
               </span>`
            : '';

        item.innerHTML = `
            <div class="expense-info">
                <span class="expense-title">${escapeHTML(expense.title)}</span>
                <div class="expense-meta">
                    <span class="category-pill cat-pill--${expense.category}">
                        ${catIcon} ${expense.category}
                    </span>
                    <span>${svgIcon('ic-calendar', 'icon-xs')} ${formattedDate}</span>
                </div>
                ${notesHTML}
            </div>
            <div class="expense-amount">
                Rs ${amountNum.toFixed(2)}
                <div class="item-actions">
                    <button class="btn-edit"
                            onclick="editExpense('${expense.id}')"
                            aria-label="Edit expense"
                            title="Edit">
                        ${svgIcon('ic-pen', 'icon-xs')}
                    </button>
                    <button class="btn-delete"
                            onclick="deleteExpense('${expense.id}', this)"
                            aria-label="Delete expense"
                            title="Delete">
                        ${svgIcon('ic-trash', 'icon-xs')}
                    </button>
                </div>
            </div>`;

        expenseList.appendChild(item);
    });
}

// ═══════════════════════════════════════════════
// UPDATE UI (master)
// ═══════════════════════════════════════════════
function updateUI() {
    const total = expenses.reduce((acc, ex) => acc + parseFloat(ex.amount), 0);

    animateBalance(total);
    updateStatCards();
    renderExpenseList();
    drawCharts();
}

// ═══════════════════════════════════════════════
// DATE HELPER
// ═══════════════════════════════════════════════
function formatDate(dateStr) {
    const parts   = dateStr.split('-');
    const dateObj = new Date(+parts[0], +parts[1] - 1, +parts[2]);
    return dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

// ═══════════════════════════════════════════════
// EXPORT CSV
// ═══════════════════════════════════════════════
function exportCSV() {
    if (expenses.length === 0) {
        showMessage('No expenses to export', 'error');
        return;
    }

    let csv = 'ID,Title,Amount (Rs),Category,Date,Notes\n';
    expenses.forEach(row => {
        csv += `${row.id},"${escapeCSV(row.title)}",${row.amount},"${row.category}",${row.date},"${escapeCSV(row.notes || '')}"\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href  = url;
    link.download = `expenses_${new Date().toISOString().slice(0,10)}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    showMessage('CSV exported!', 'success');
}

// ═══════════════════════════════════════════════
// SANITISATION HELPERS
// ═══════════════════════════════════════════════
function escapeHTML(str) {
    if (!str) return '';
    return str.toString().replace(/[&<>'"]/g, tag => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    }[tag] || tag));
}

function escapeCSV(str) {
    if (!str) return '';
    return str.toString().replace(/"/g, '""');
}

// ═══════════════════════════════════════════════
// TOAST NOTIFICATIONS
// ═══════════════════════════════════════════════
function showMessage(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const iconIds = { success: 'ic-check', error: 'ic-trash', info: 'ic-note' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `${svgIcon(iconIds[type] || 'ic-check', 'icon-sm')}<span>${escapeHTML(msg)}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-fadeout');
        setTimeout(() => { if (container.contains(toast)) container.removeChild(toast); }, 320);
    }, 3200);
}

// ═══════════════════════════════════════════════
// CHART.JS IMPLEMENTATIONS
// ═══════════════════════════════════════════════
let categoryChartInstance = null;
let trendChartInstance    = null;

// Trend toggle buttons
trendButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        trendButtons.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-pressed', 'false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-pressed', 'true');
        trendDays = parseInt(btn.dataset.days, 10);
        drawCharts();
    });
});

function getChartColors() {
    const isLight = document.body.classList.contains('light-mode');
    return {
        text: isLight ? '#475569' : '#94a3b8',
        grid: isLight ? 'rgba(0,0,0,0.05)' : 'rgba(255,255,255,0.05)',
    };
}

function drawCharts() {
    if (!window.Chart) return;

    const colors = getChartColors();

    // ── Category Doughnut ──────────────────────
    const categoryTotals = {};
    expenses.forEach(ex => {
        categoryTotals[ex.category] = (categoryTotals[ex.category] || 0) + parseFloat(ex.amount);
    });

    const catLabels = Object.keys(categoryTotals);
    const catData   = Object.values(categoryTotals);
    const catColors = ['#6366f1','#ec4899','#22d3ee','#10b981','#f59e0b','#8b5cf6','#f97316','#3b82f6'];

    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    if (categoryChartInstance) categoryChartInstance.destroy();

    categoryChartInstance = new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: catLabels.length ? catLabels : ['No Data'],
            datasets: [{
                data:            catData.length ? catData : [1],
                backgroundColor: catData.length ? catColors.slice(0, catLabels.length) : [colors.grid],
                borderWidth:     2,
                borderColor:     'transparent',
                hoverOffset:     8
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            cutout:              '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color:     colors.text,
                        padding:   16,
                        font:      { size: 11, family: 'Poppins' },
                        usePointStyle: true,
                        pointStyleWidth: 8
                    }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => catData.length
                            ? ` Rs ${ctx.parsed.toFixed(2)}`
                            : ' No expenses yet'
                    }
                }
            }
        }
    });

    // ── Trend Bar ─────────────────────────────
    const recentTrend = {};
    for (let i = trendDays - 1; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        const key = `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
        recentTrend[key] = 0;
    }

    expenses.forEach(ex => {
        if (recentTrend[ex.date] !== undefined) {
            recentTrend[ex.date] += parseFloat(ex.amount);
        }
    });

    const trendLabels = Object.keys(recentTrend).map(d => {
        const p = d.split('-');
        return `${p[1]}/${p[2]}`;
    });
    const trendData    = Object.values(recentTrend);
    const isTrendEmpty = trendData.every(v => v === 0);

    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    if (trendChartInstance) trendChartInstance.destroy();

    trendChartInstance = new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Spent (Rs)',
                data:            isTrendEmpty ? trendData.map(() => 0) : trendData,
                backgroundColor: isTrendEmpty ? colors.grid : 'rgba(99,102,241,0.75)',
                borderRadius:    6,
                borderSkipped:   false
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: colors.text, font: { size: 10 }, maxRotation: 45 },
                    grid:  { display: false }
                },
                y: {
                    ticks: {
                        color:    colors.text,
                        font:     { size: 10 },
                        callback: v => `Rs ${v}`
                    },
                    grid: { color: colors.grid },
                    beginAtZero: true
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => isTrendEmpty
                            ? ' No expenses in this period'
                            : ` Rs ${ctx.parsed.y.toFixed(2)}`
                    }
                }
            }
        }
    });
}

// ═══════════════════════════════════════════════
// INIT
// ═══════════════════════════════════════════════
function init() {
    loadExpenses();
    loadBudget();
    updateUI();
}

init();
