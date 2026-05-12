<?php
require_once 'api/session_check.php';
redirectIfLoggedIn();

$pageTitle = "Login";
include 'includes/header.php';
?>

<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }
    .login-container {
        width: 100%;
        max-width: 400px;
        padding: 2.5rem;
        text-align: center;
    }
    .login-container .logo-icon {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        margin-bottom: 1rem;
        color: white;
    }
    .login-container h2 {
        margin-bottom: 2rem;
        color: var(--text-main);
    }
    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        font-weight: 500;
    }
    .form-group input {
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 8px;
        border: 1px solid var(--glass-border);
        background: var(--glass-bg);
        color: var(--text-main);
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
    }
    .btn-login {
        width: 100%;
        padding: 0.8rem;
        margin-top: 1rem;
        font-size: 1rem;
    }
    .error-msg {
        color: var(--danger);
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: none;
    }
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-toggle {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        padding: 0;
        margin: 0;
        cursor: pointer;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.3s ease;
    }
    .password-toggle:hover {
        color: var(--primary);
    }
</style>

<div class="glass-panel login-container">
    <div class="logo-icon">
        <svg class="icon" style="width: 32px; height: 32px;"><use href="#ic-wallet"/></svg>
    </div>
    <h2>Expense Tracker</h2>
    
    <form id="login-form">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" placeholder="admin email" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" placeholder="********" required style="padding-right: 3rem;">
                <button type="button" id="toggle-password" class="password-toggle" aria-label="Show password">
                    <svg class="icon icon-sm" id="eye-icon"><use href="#ic-eye"/></svg>
                </button>
            </div>
        </div>
        <div id="login-error" class="error-msg">Invalid username or password</div>
        <button type="submit" class="btn-primary btn-login">Login</button>
    </form>
</div>

<script>
    // Password Toggle Logic
    const passwordInput = document.getElementById('password');
    const togglePasswordBtn = document.getElementById('toggle-password');
    const eyeIconUse = document.getElementById('eye-icon').querySelector('use');

    togglePasswordBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        eyeIconUse.setAttribute('href', isPassword ? '#ic-eye-slash' : '#ic-eye');
        togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    });

    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const errorEl = document.getElementById('login-error');
        const btn = document.querySelector('.btn-login');
        
        btn.disabled = true;
        btn.textContent = 'Logging in...';
        errorEl.style.display = 'none';

        try {
            const res = await fetch('api/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });

            const data = await res.json();

            if (res.ok) {
                // We still set localStorage for the JS to function, but PHP will handle redirects
                localStorage.setItem('token', data.token);
                localStorage.setItem('role', data.role);
                localStorage.setItem('username', data.username);
                
                if (data.role === 'admin') {
                    window.location.href = 'admin.php';
                } else {
                    window.location.href = 'index.php';
                }
            } else {
                errorEl.textContent = data.error || 'Login failed';
                errorEl.style.display = 'block';
            }
        } catch (err) {
            errorEl.textContent = 'Network error. Please try again.';
            errorEl.style.display = 'block';
        } finally {
            btn.disabled = false;
            btn.textContent = 'Login';
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
