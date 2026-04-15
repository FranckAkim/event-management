<?php
// register.php - Updated with branding + session name
session_start();
require_once 'config/db.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$message = "";
$success = false;
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'Requester';

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 4) {
        $message = "Password must be at least 4 characters long.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT UserID FROM user WHERE Email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                $message = "This email is already registered.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO user (Name, Email, RoleName, password) 
                    VALUES (?, ?, ?, ?)
                ");

                if ($stmt->execute([$name, $email, $role, $password])) {
                    $success = true;
                    $redirect = true;
                    $message = "✅ Account created successfully!<br>Redirecting to login...";
                } else {
                    $message = "Failed to create account.";
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Event Scheduling System - Create Account</title>
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

        .register-box {
            max-width: 440px;
            margin: 80px auto;
            background: var(--panel);
            padding: 40px;
            border-radius: var(--radius2);
            box-shadow: var(--shadow);
        }

        .btn {
            width: 100%;
            padding: 14px;
            margin-top: 15px;
            font-size: 16px;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: var(--muted);
        }

        input,
        select {
            width: 100%;
            padding: 12px 14px;
            background: var(--panel2);
            border: 1px solid var(--line);
            border-radius: 8px;
            color: var(--text);
            font-size: 15px;
        }
    </style>
    <?php if ($redirect): ?>
        <script>
            setTimeout(function() {
                window.location.href = "login.php";
            }, 2000);
        </script>
    <?php endif; ?>
</head>

<body>
    <div class="register-box">
        <div style="text-align:center; margin-bottom:30px;">
            <h1>Event Scheduling System</h1>
            <p style="color:var(--muted);">Create New Account</p>
        </div>

        <?php if ($message): ?>
            <div style="padding:14px; margin-bottom:20px; border-radius:8px; 
                        background:<?= $success ? '#34d39930' : '#fb718530' ?>; 
                        color:<?= $success ? '#34d399' : '#fb7185' ?>; text-align:center;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <form method="POST" action="">
                <div class="field">
                    <label>Full Name</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" />
                </div>
                <div class="field">
                    <label>Email Address</label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                </div>
                <div class="field">
                    <label>Password</label>
                    <input type="password" name="password" required />
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required />
                </div>
                <div class="field">
                    <label>Account Type</label>
                    <select name="role" required>
                        <option value="Requester">Attendee / Requester</option>
                        <option value="Organiser">Organizer</option>
                    </select>
                </div>
                <button type="submit" class="btn primary">Create My Account</button>
            </form>
        <?php endif; ?>

        <?php if (!$success): ?>
            <p style="text-align:center; margin-top:25px; color:var(--muted);">
                Already have an account?
                <a href="login.php" style="color:var(--accent);">Login here</a>
            </p>
        <?php endif; ?>
    </div>
</body>

</html>