<?php
// login.php - Updated with new branding
session_start();
require_once 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $pdo->prepare("
            SELECT UserID, Name, Email, RoleName, password 
            FROM user 
            WHERE Email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['password'] === $password) {
            // Login successful - store user name
            $_SESSION['user_id']   = $user['UserID'];
            $_SESSION['user_name'] = $user['Name'];
            $_SESSION['email']     = $user['Email'];
            $_SESSION['role']      = $user['RoleName'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Event Scheduling System - Login</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
    <style>
        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            max-width: 420px;
            width: 100%;
            background: var(--panel);
            padding: 50px 40px;
            border-radius: var(--radius2);
            box-shadow: var(--shadow);
            margin: 20px;
        }

        .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .btn {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            margin-top: 10px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
        }

        input {
            width: 100%;
            padding: 12px 14px;
            background: var(--panel2);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            font-size: 15px;
        }

        .error {
            color: #fb7185;
            background: #fb718520;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="logo-area">
            <h1>Event Scheduling System</h1>
            <p style="color:var(--muted); margin-top:8px;">Weddings & Birthdays • Centralized Scheduling</p>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="field">
                <label>Email Address</label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="admin@festivalsystem.com" />
            </div>

            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required
                    placeholder="Enter your password" />
            </div>

            <button type="submit" class="btn primary">Login</button>
        </form>

        <p style="text-align: center; margin-top: 25px; color: var(--muted);">
            Don't have an account?
            <a href="register.php" style="color: var(--accent);">Create New Account</a>
        </p>

        <div style="text-align: center; margin-top: 30px; font-size: 13px; color: var(--muted);">
            Demo: admin@festivalsystem.com / admin123
        </div>
    </div>
</body>

</html>