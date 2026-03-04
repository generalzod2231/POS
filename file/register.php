<?php
include 'db.php';

$message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- NEW CHECK: Does the username already exist? ---
    $check_stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // The username exists! Stop and show a polite error.
        $message = "<p style='color: #d9534f; font-weight: bold;'>Error: The username '$username' is already taken. Please choose another.</p>";
    } else {
        // The username is free! Hash the password and save.
        
        // SECURITY CAUTION: Never save passwords as plain text! 
        // This turns your password into a secure, scrambled hash.
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the database command to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            $message = "<p style='color: #5cb85c; font-weight: bold;'>Account created successfully! <a href='login.php' style='color: #3e2723;'>Click here to log in.</a></p>";
        } else {
            $message = "<p style='color: #d9534f; font-weight: bold;'>Error: Could not save to the database.</p>";
        }
        
        $stmt->close();
    }
    
    $check_stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Brew & Bean Admin</title>
    <style>
        body { 
            font-family: sans-serif; 
            background-color: #f4ece1; /* Matches your POS background */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .register-box { 
            background: #fffcf5; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
            width: 320px; 
            text-align: center; 
        }
        .register-box h2 { color: #5a3d2b; margin-top: 0; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; color: #5a3d2b; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        .btn { 
            background-color: #5a3d2b; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            width: 100%; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold; 
        }
        .btn:hover { background-color: #3e2723; }
    </style>
</head>
<body>

    <div class="register-box">
        <h2>🛠️ Setup Admin</h2>
        
        <?= $message ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Choose a Username</label>
                <input type="text" name="username" required placeholder="e.g. admin">
            </div>
            <div class="form-group">
                <label>Choose a Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn">Create Account</button>
        </form>
    </div>

</body>
</html>