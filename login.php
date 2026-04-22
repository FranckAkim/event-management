<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db.php';

    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT UserID, Name, Email, RoleName, password FROM user WHERE Email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['email']     = $user['Email'];
            $_SESSION['role']      = strtolower($user['RoleName']);
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Incorrect email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>CelebrateHub — Sign In</title>
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
            <p class="auth-sub">Weddings · Birthdays · Celebrations</p>

            <?php if ($error): ?>
                <div class="auth-error show"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="field">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@example.com"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autocomplete="email" required />
                </div>
                <div class="field" style="margin-top:12px;">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••"
                        autocomplete="current-password" required />
                </div>
                <button type="submit" class="btn primary" style="margin-top:20px;width:100%;border-radius:12px;padding:12px;">
                    Sign In
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account?
                <a href="register.php">Create one</a>
            </div>

        </div>
    </div>
</body>

</html>