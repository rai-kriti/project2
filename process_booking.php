<?php
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $user_name = $_POST['user_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $movie_id = $_POST['movie_id'] ?? '';
    $no_of_tickets = $_POST['no_of_tickets'] ?? '';
    $payment_id = $_POST['payment_id'] ?? '';
    
    // Validate input
    if (empty($user_name) || empty($email) || empty($movie_id) || empty($no_of_tickets)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    if (!is_numeric($movie_id) || !is_numeric($no_of_tickets)) {
        throw new Exception('Invalid movie ID or number of tickets');
    }
    
    // Get movie details
    $movie = getMovieById($movie_id);
    if (!$movie) {
        throw new Exception('Movie not found');
    }
    
    // Add booking to database
    if (addBooking($user_name, $email, $movie_id, $no_of_tickets, $payment_id)) {
        $pdo = getConnection();
        $booking_id = $pdo->lastInsertId();
        
        // Send confirmation email
        sendConfirmationEmail(
            $email, 
            $user_name, 
            $movie['title'], 
            formatDate($movie['date']), 
            formatTime($movie['time']), 
            $no_of_tickets, 
            $booking_id
        );
        
        echo json_encode(['success' => true, 'booking_id' => $booking_id]);
    } else {
        throw new Exception('Failed to save booking');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>