<?php
session_start();
require_once 'db.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if job ID is provided
if (!isset($_GET['id'])) {
    header("Location: job_listings.php");
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
    if ($stmt->execute([(int)$_GET['id']])) {
        $_SESSION['success'] = "Job has been successfully deleted.";
    } else {
        $_SESSION['error'] = "Failed to delete the job.";
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: job_listings.php");
exit();
?> 