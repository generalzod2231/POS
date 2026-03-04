<?php
session_start(); // Starts the security session
include 'db.php';

$error = "";

// If already logged in, send them straight to the admin page
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find the user in the database
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Check if the password matches the hashed password in the database
        if (password_verify($password, $admin['password'])) {
            // Success! Create the session key.
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: admin.php"); // Go to dashboard
            exit();
        } else {
            $error = "<p style='color: #d9534f; font-weight: bold;'>Incorrect password.</p>";
        }
    } else {
        $error = "<p style='color: #d9534f; font-weight: bold;'>Username not found.</p>";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Brew & Bean Admin</title>
    <style>
        body { font-family: sans-serif; background-color: #f4ece1; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fffcf5; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); width: 320px; text-align: center; }
        .login-box h2 { color: #5a3d2b; margin-top: 0; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #5a3d2b; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { background-color: #5a3d2b; color: white; border: none; padding: 10px 15px; width: 100%; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background-color: #3e2723; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>☕ Admin Login</h2>
        <?= $error ?>
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>