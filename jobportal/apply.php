<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is a jobseeker
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'jobseeker') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$job = null;

// Get job details
if (isset($_GET['id'])) {
    $job_id = (int)$_GET['id'];
    
    // Check if already applied
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE job_id = ? AND user_id = ?");
    $stmt->execute([$job_id, $_SESSION['user_id']]);
    if ($stmt->fetch()) {
        $error = "You have already applied for this job.";
    } else {
        // Get job details
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        $job = $stmt->fetch();
        
        if (!$job) {
            $error = "Job not found.";
        }
    }
} else {
    $error = "No job specified.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$error) {
    try {
        $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, status) VALUES (?, ?, 'pending')");
        if ($stmt->execute([$job_id, $_SESSION['user_id']])) {
            $success = "Your application has been submitted successfully!";
        } else {
            $error = "Failed to submit application. Please try again.";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Job - Job Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="job_listings.php">Job Listings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <h1>Apply for Job</h1>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <p><a href="job_listings.php" class="btn">Back to Job Listings</a></p>
        <?php elseif($job): ?>
            <div class="job-details">
                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <?php if (isset($job['salary']) && $job['salary'] > 0): ?>
                    <p><strong>Salary:</strong> $<?php echo number_format($job['salary']); ?></p>
                <?php endif; ?>
                <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                <div class="job-meta">
                    <span>Posted: <?php echo date('M d, Y', strtotime($job['posted_date'])); ?></span>
                </div>
            </div>

            <form method="POST" action="" class="application-form">
                <div class="form-group">
                    <label for="cover_letter">Cover Letter:</label>
                    <textarea id="cover_letter" name="cover_letter" rows="6" required 
                        placeholder="Explain why you're a good fit for this position..."></textarea>
                </div>
                <button type="submit" class="btn">Submit Application</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html> 