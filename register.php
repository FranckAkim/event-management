<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db.php';

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = 'requester'; // all self-registered users are attendees

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $check = $pdo->prepare("SELECT UserID FROM user WHERE Email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'That email address is already registered. Please sign in instead.';
        } else {
            $pdo->prepare("INSERT INTO user (Name, Email, RoleName, password) VALUES (?, ?, ?, ?)")
                ->execute([$name, $email, $role, $password]);
            $success = 'Account created! You can now sign in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CelebrateHub — Create Account</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <script>
        (function() {
            if (localStorage.getItem('ch-theme') === 'dark') {
                document.body && document.body.classList.add('dark');
            }
            document.addEventListener('DOMContentLoaded', function() {
                if (localStorage.getItem('ch-theme') === 'dark') {
                    document.body.classList.add('dark');
                }
            });
        })();
    </script>
</head>

<body>
    <div class="auth-wrap">
        <div class="auth-card">

            <div style="text-align:center;margin-bottom:16px;">
                <img src="assets/images/logo.svg" alt="CelebrateHub" width="54" height="54"
                    style="border-radius:50%;box-shadow:0 6px 20px rgba(255,107,157,.32);" />
            </div>
            <div class="auth-logo">
                Celebrate<span>Hub</span>
            </div>
            <p class="auth-sub">Create your account to get started</p>

            <?php if ($error): ?>
                <div class="auth-error show"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background:rgba(92,230,200,.14);border:1px solid rgba(92,230,200,.35);
                    border-radius:8px;color:#1a9e86;font-size:.82rem;
                    padding:10px 14px;margin-bottom:14px;">
                    ✅ <?= htmlspecialchars($success) ?>
                    <a href="login.php" style="color:var(--rose);font-weight:600;margin-left:6px;">Sign In →</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Alice Johnson"
                        value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                        autocomplete="name" required />
                </div>
                <div class="field" style="margin-top:12px;">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="alice@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autocomplete="email" required />
                </div>
                <div class="field" style="margin-top:12px;">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 6 characters"
                        autocomplete="new-password" required />
                </div>
                <button type="submit" class="btn primary" style="margin-top:20px;width:100%;border-radius:12px;padding:12px;">
                    Create Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account?
                <a href="login.php">Sign in</a>
            </div>

        </div>
    </div>
</body>

</html>