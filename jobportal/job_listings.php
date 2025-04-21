<?php
session_start();
require_once 'db.php';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total jobs and pages
$total_jobs = $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$total_pages = ceil($total_jobs / $per_page);

// Get jobs for current page
$stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY posted_date DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Portal - Job Listings</title>
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
        

        <h1>Job Listings</h1>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($jobs)): ?>
            <p>No jobs available at the moment.</p>
        <?php else: ?>
            <?php foreach($jobs as $job): ?>
                <div class="job-card">
                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                    <?php if (isset($job['salary']) && $job['salary'] > 0): ?>
                        <p><strong>Salary:</strong> $<?php echo number_format($job['salary']); ?></p>
                    <?php endif; ?>
                    <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                    <div class="job-meta">
                        <span>Posted: <?php echo date('M d, Y', strtotime($job['posted_date'])); ?></span>
                        <div class="action-buttons">
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'jobseeker'): ?>
                                <a href="apply.php?id=<?php echo $job['id']; ?>" class="btn">Apply Now</a>
                            <?php endif; ?>
                            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'admin'): ?>
                                <a href="delete_job.php?id=<?php echo $job['id']; ?>" class="btn btn-secondary" 
                                   onclick="return confirm('Are you sure you want to delete this job?');">Delete Job</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="btn">Previous</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i == $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="btn">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
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