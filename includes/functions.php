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

// Image upload functions
function uploadPosterImage($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'No file uploaded or upload error'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size too large. Maximum 5MB allowed.'];
    }
    
    // Get file extension
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    // Check allowed extensions
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP allowed.'];
    }
    
    // Generate unique filename
    $filename = 'poster_' . uniqid() . '.' . $extension;
    $filepath = POSTER_DIR . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => 'uploads/posters/' . $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save uploaded file'];
    }
}

function deleteImageFile($imagePath) {
    if (!empty($imagePath) && file_exists($imagePath) && strpos($imagePath, 'placeholder') === false) {
        unlink($imagePath);
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

// Get image URL with fallback
function getImageUrl($imagePath) {
    if (empty($imagePath) || !file_exists($imagePath)) {
        return 'https://via.placeholder.com/300x400/cccccc/666666?text=No+Image';
    }
    return $imagePath;
}
?>