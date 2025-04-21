<?php
session_start();
require_once 'db.php';

// Check if user is already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if(empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Redirect based on user type
            if($user['user_type'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Jobhunt.com</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/jobportal/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <!-- <a href="index.php">Jobhunt.com</a> -->
            </div>
            <nav class="navbar">
                <div class="container">
                    <ul>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><span class="user-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
                        <?php endif; ?>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="job_listings.php">Job Listings</a></li>
                        <?php if(!isset($_SESSION['user_id'])): ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Register</a></li>
                        <?php else: ?>
                            <?php if($_SESSION['user_type'] === 'employer'): ?>
                                <li><a href="post_job.php">Post a Job</a></li>
                            <?php endif; ?>
                            <li><a href="logout.php">Logout</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="login-container" style="max-width: 400px; margin: 30px auto; padding: 20px;">
                <h1 style="font-size: 24px; margin-bottom: 20px;">Login to Your Account</h1>
                
                <?php if($error): ?>
                    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="POST" action="">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="email" style="font-size: 14px;">Email</label>
                        <input type="email" id="email" name="email" required style="padding: 8px 12px; font-size: 14px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label for="password" style="font-size: 14px;">Password</label>
                        <input type="password" id="password" name="password" required style="padding: 8px 12px; font-size: 14px;">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="padding: 8px 12px; font-size: 14px;">Login</button>
                    </div>
                    <div style="margin-top: 15px; text-align: center; font-size: 13px;">
                        <a href="forgot_password.php" style="color: #4a90e2; text-decoration: none; margin-right: 10px;">Forgot Password?</a>
                        <a href="register.php" style="color: #4a90e2; text-decoration: none;">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

   
   
</body>
</html>