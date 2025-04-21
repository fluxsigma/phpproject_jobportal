<?php
session_start();
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Home</title>
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

    <div class="container">
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'admin'): ?>
            <div class="alert alert-success" style="text-align: center; margin-bottom: 2rem;">
                <strong>Admin Dashboard</strong> - Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!
            </div>
        <?php endif; ?>

        <div class="hero">
            <h1>Welcome to Job Portal</h1>
            <p>Find your dream job or hire the perfect candidate.</p>
            <div class="center-button">
                <a href="job_listings.php" class="btn">Browse Jobs</a>
            </div>
        </div>

        <div class="featured-jobs">
            <h2>Featured Jobs</h2>
            <?php
            $stmt = $pdo->query("SELECT * FROM jobs ORDER BY posted_date DESC LIMIT 5");
            while($job = $stmt->fetch()) {
                echo '<div class="job-card">';
                echo '<h3>' . htmlspecialchars($job['title']) . '</h3>';
                echo '<p><strong>Company:</strong> ' . htmlspecialchars($job['company']) . '</p>';
                echo '<p><strong>Location:</strong> ' . htmlspecialchars($job['location']) . '</p>';
                if (isset($job['salary']) && $job['salary'] > 0) {
                    echo '<p><strong>Salary:</strong> $' . number_format($job['salary']) . '</p>';
                }
                echo '<p>' . nl2br(htmlspecialchars($job['description'])) . '</p>';
                echo '<div class="job-meta">';
                echo '<span>Posted: ' . date('M d, Y', strtotime($job['posted_date'])) . '</span>';
                echo '<a href="apply.php?id=' . $job['id'] . '" class="btn">Apply Now</a>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
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
                <a href="#" >Privacy Policy</a> | <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>