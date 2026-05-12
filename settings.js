const token = localStorage.getItem('token');

async function fetchProfile() {
    try {
        const res = await fetch('api/profile.php', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const data = await res.json();
        if (data.user) {
            document.getElementById('email').value = data.user.email || '';
        }
    } catch (err) {
        console.error('Error fetching profile', err);
    }
}

document.getElementById('email-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const btn = document.getElementById('btn-save-email');
    
    btn.disabled = true;
    btn.textContent = 'Saving...';

    try {
        const res = await fetch('api/profile.php', {
            method: 'PUT',
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email })
        });
        const data = await res.json();
        if (res.ok) {
            showMessage('Email updated successfully!', 'success');
        } else {
            showMessage(data.error || 'Update failed', 'error');
        }
    } catch (err) {
        showMessage('Network error', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Save Email';
    }
});

document.getElementById('password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const old_password = document.getElementById('old_password').value;
    const new_password = document.getElementById('new_password').value;
    const confirm_password = document.getElementById('confirm_password').value;
    const btn = document.getElementById('btn-save-password');

    if (new_password !== confirm_password) {
        showMessage('New passwords do not match!', 'error');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Updating...';

    try {
        const res = await fetch('api/profile.php', {
            method: 'PUT',
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                email: document.getElementById('email').value,
                old_password,
                new_password 
            })
        });
        const data = await res.json();
        if (res.ok) {
            showMessage('Password updated successfully!', 'success');
            document.getElementById('password-form').reset();
        } else {
            showMessage(data.error || 'Update failed', 'error');
        }
    } catch (err) {
        showMessage('Network error', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Update Password';
    }
});

// Reuse showMessage from app.js (actually needs to be defined if not shared)
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

fetchProfile();
