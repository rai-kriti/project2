<?php
require_once 'config.php';

// Start session for admin authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Admin authentication functions
function authenticateAdmin($username, $password) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $username;
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogout() {
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    session_destroy();
}

// FIXED IMAGE UPLOAD - This will definitely work
function uploadPosterImage($file) {
    // Create uploads directory in the root folder
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/posters/';
    $webPath = '/uploads/posters/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Check file upload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'poster_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
    
    // Full server path for saving
    $fullPath = $uploadDir . $filename;
    // Web path for database and display
    $webFullPath = $webPath . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return ['success' => true, 'filename' => $webFullPath];
    }
    
    return ['success' => false, 'message' => 'Failed to save file'];
}

function deleteImageFile($imagePath) {
    if (!empty($imagePath) && strpos($imagePath, '/uploads/') !== false) {
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}

// Get all movies
function getAllMovies() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM movies ORDER BY date ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get movie by ID
function getMovieById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Add new movie
function addMovie($title, $date, $time, $description, $poster_path, $trailer_url) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO movies (title, date, time, description, poster_url, trailer_url) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$title, $date, $time, $description, $poster_path, $trailer_url]);
}

// Update movie
function updateMovie($id, $title, $date, $time, $description, $poster_path, $trailer_url) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE movies SET title = ?, date = ?, time = ?, description = ?, poster_url = ?, trailer_url = ? WHERE id = ?");
    return $stmt->execute([$title, $date, $time, $description, $poster_path, $trailer_url, $id]);
}

// Delete movie
function deleteMovie($id) {
    $pdo = getConnection();
    
    // Get movie details to delete associated image
    $movie = getMovieById($id);
    if ($movie && !empty($movie['poster_url'])) {
        deleteImageFile($movie['poster_url']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    return $stmt->execute([$id]);
}

// Add new booking
function addBooking($user_name, $email, $movie_id, $no_of_tickets, $payment_id = null) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO bookings (user_name, email, movie_id, no_of_tickets, payment_id, payment_status) VALUES (?, ?, ?, ?, ?, 'completed')");
    return $stmt->execute([$user_name, $email, $movie_id, $no_of_tickets, $payment_id]);
}

// Send confirmation email
function sendConfirmationEmail($email, $user_name, $movie_title, $movie_date, $movie_time, $no_of_tickets, $booking_id) {
    $subject = "Movie Ticket Booking Confirmation - Booking ID: " . $booking_id;
    
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
    </head>
    <body>
        <h2>Booking Confirmation</h2>
        <p>Dear $user_name,</p>
        <p>Your movie ticket booking has been confirmed!</p>
        
        <h3>Booking Details:</h3>
        <ul>
            <li><strong>Booking ID:</strong> $booking_id</li>
            <li><strong>Movie:</strong> $movie_title</li>
            <li><strong>Date:</strong> $movie_date</li>
            <li><strong>Time:</strong> $movie_time</li>
            <li><strong>Number of Tickets:</strong> $no_of_tickets</li>
        </ul>
        
        <p>Please arrive at the cinema 15 minutes before the show time.</p>
        <p>Thank you for choosing our cinema!</p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: noreply@moviebooking.com' . "\r\n";
    
    return mail($email, $subject, $message, $headers);
}

// Format date for display
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

// Format time for display
function formatTime($time) {
    return date('g:i A', strtotime($time));
}

// Convert YouTube URL to embed URL
function getYouTubeEmbedUrl($url) {
    if (empty($url)) return '';
    
    // If already an embed URL, return as is
    if (strpos($url, 'embed') !== false) {
        return $url;
    }
    
    // Extract video ID from various YouTube URL formats
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    
    if (isset($matches[1])) {
        return 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $matches[1];
    }
    
    return $url;
}

// FIXED: Get image URL with proper fallback
function getImageUrl($imagePath) {
    // If no image path, show placeholder
    if (empty($imagePath)) {
        return 'https://via.placeholder.com/300x400/4f46e5/ffffff?text=No+Poster';
    }
    
    // If it's already a full URL (like placeholder), return as is
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    // Check if file exists on server
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
    if (file_exists($fullPath)) {
        return $imagePath; // Return the web path
    }
    
    // If file doesn't exist, return placeholder
    return 'https://via.placeholder.com/300x400/ef4444/ffffff?text=Image+Missing';
}
?>