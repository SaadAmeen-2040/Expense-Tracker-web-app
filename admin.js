const token = localStorage.getItem('token');
const role = localStorage.getItem('role');

if (!token || role !== 'admin') {
    window.location.href = 'login.php';
}

// ── SVG icon helper ────
function svgIcon(id, sizeClass = 'icon-sm') {
    return `<svg class="icon ${sizeClass}" aria-hidden="true"><use href="#${id}"/></svg>`;
}

async function fetchWithAuth(url, options = {}) {
    options.headers = {
        ...options.headers,
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    };
    const res = await fetch(url, options);
    if (res.status === 401 || res.status === 403) {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        window.location.href = 'login.php';
    }
    return res;
}

// Stats & Users
async function loadStats() {
    try {
        const res = await fetchWithAuth('api/admin/stats');
        const data = await res.json();
        
        const spentEl = document.getElementById('admin-total-spent');
        const usersEl = document.getElementById('admin-total-users');
        const expEl = document.getElementById('admin-total-expenses');

        if (spentEl) spentEl.textContent = `Rs ${(data.totalAmount || 0).toFixed(2)}`;
        if (usersEl) usersEl.textContent = data.totalUsers || 0;
        if (expEl) expEl.textContent = data.totalExpenses || 0;
    } catch (e) {
        console.error('Error fetching stats', e);
    }
}

async function loadUsers() {
    try {
        const res = await fetchWithAuth('api/admin/users');
        const data = await res.json();
        const userList = document.getElementById('admin-user-list');
        if (!userList) return;

        userList.innerHTML = '';

        if (data.users.length === 0) {
            userList.innerHTML = '<div class="empty-state"><p>No users found</p></div>';
            return;
        }

        data.users.forEach(user => {
            const item = document.createElement('div');
            item.classList.add('expense-item');
            item.style.padding = '1rem 1.5rem';
            
            const isRoot = user.id == 1; // Assuming ID 1 is root admin
            
            item.innerHTML = `
                <div class="expense-info">
                    <span class="expense-title">${user.username}</span>
                    <div class="expense-meta">
                        <span class="category-pill" style="background: ${user.role === 'admin' ? 'rgba(236,72,153,0.1)' : 'rgba(99,102,241,0.1)'}; color: ${user.role === 'admin' ? '#ec4899' : '#818cf8'};">
                            ${user.role}
                        </span>
                        <span>${user.expenseCount} Expenses</span>
                    </div>
                </div>
                <div class="expense-amount">
                    ID: ${user.id}
                    <div class="item-actions">
                        ${!isRoot ? `
                            <button class="btn-delete" onclick="deleteUser(${user.id})" title="Delete User">
                                ${svgIcon('ic-trash', 'icon-xs')}
                            </button>
                        ` : '<span style="font-size: 0.75rem; color: var(--text-muted);">Root</span>'}
                    </div>
                </div>
            `;
            userList.appendChild(item);
        });
    } catch (e) {
        console.error('Error fetching users', e);
    }
}

async function deleteUser(id) {
    if (confirm('Are you sure you want to delete this user and all their expenses?')) {
        try {
            const res = await fetchWithAuth(`api/admin/users/${id}`, { method: 'DELETE' });
            if (res.ok) {
                showMessage('User deleted successfully', 'success');
                loadStats();
                loadUsers();
            } else {
                const data = await res.json();
                showMessage(data.error || 'Failed to delete user', 'error');
            }
        } catch (e) {
            showMessage('Error deleting user', 'error');
        }
    }
}

function showMessage(msg, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const iconIds = { success: 'ic-check', error: 'ic-trash', info: 'ic-note' };
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const iconId = iconIds[type] || 'ic-check';
    toast.innerHTML = `<svg class="icon icon-sm" aria-hidden="true"><use href="#${iconId}"/></svg><span>${msg}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('toast-fadeout');
        setTimeout(() => { if (container.contains(toast)) container.removeChild(toast); }, 320);
    }, 3200);
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    loadStats();
    loadUsers();
});
