<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an employer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'employer') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $employer_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, description, employer_id, posted_date) VALUES (?, ?, ?, ?, ?, NOW())");
    
    if ($stmt->execute([$title, $company, $location, $description, $employer_id])) {
        $success = "Job posted successfully!";
    } else {
        $error = "Failed to post job. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Post a Job</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/jobportal/style.css?v=<?php echo time(); ?>">
    <style>
        /* Inline fallback styles */
        .site-footer {
            background: linear-gradient(to right, #1e293b, #0f172a);
            color: #ffffff;
            padding: 4rem 0 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="job_listings.php">Job Listings</a></li>
                <li><a href="post_job.php">Post a Job</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1>Post a New Job</h1>

        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Job Title:</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="company">Company Name:</label>
                <input type="text" id="company" name="company" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div class="form-group">
                <label for="description">Job Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>

            <button type="submit" class="btn">Post Job</button>
        </form>
    </div>
    
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About Jobhunt.com</h3>
                    <p>Jobhunt.com is your premier destination for finding the perfect job or hiring the ideal candidate. We connect talented professionals with great opportunities.</p>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-envelope"></i> contact@jobhunt.com</li>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Job Street, Career City</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Jobhunt.com. All rights reserved.</p>
                <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html> 