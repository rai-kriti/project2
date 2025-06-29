<?php
require_once 'includes/functions.php';

if (!isset($_GET['movie_id']) || !is_numeric($_GET['movie_id'])) {
    header('Location: index.php');
    exit;
}

$movie_id = $_GET['movie_id'];
$movie = getMovieById($movie_id);

if (!$movie) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Tickets - <?php echo htmlspecialchars($movie['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white p-4">
        <div class="container mx-auto">
            <a href="index.php" class="text-blue-200 hover:text-white">&larr; Back to Movies</a>
            <h1 class="text-2xl font-bold mt-2">Book Tickets</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Movie Details -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-1/3">
                        <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" 
                             alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                             class="w-full h-64 lg:h-80 object-cover rounded">
                    </div>
                    
                    <div class="lg:w-1/3">
                        <h2 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($movie['title']); ?></h2>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($movie['description']); ?></p>
                        
                        <div class="space-y-2">
                            <p class="text-sm">
                                <span class="font-semibold">Date:</span> <?php echo formatDate($movie['date']); ?>
                            </p>
                            <p class="text-sm">
                                <span class="font-semibold">Time:</span> <?php echo formatTime($movie['time']); ?>
                            </p>
                            <p class="text-sm">
                                <span class="font-semibold">Price per ticket:</span> ₹200
                            </p>
                        </div>
                    </div>
                    
                    <?php if (!empty($movie['trailer_url'])): ?>
                        <div class="lg:w-1/3">
                            <h3 class="text-lg font-semibold mb-2">Watch Trailer</h3>
                            <div class="relative">
                                <iframe 
                                    src="<?php echo getYouTubeEmbedUrl($movie['trailer_url']); ?>" 
                                    class="w-full h-48 lg:h-64 rounded"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-4">Booking Details</h3>
                
                <form id="bookingForm" class="space-y-4">
                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                    
                    <div>
                        <label for="user_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" id="user_name" name="user_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="no_of_tickets" class="block text-sm font-medium text-gray-700 mb-1">Number of Tickets *</label>
                        <select id="no_of_tickets" name="no_of_tickets" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select tickets</option>
                            <option value="1">1 Ticket</option>
                            <option value="2">2 Tickets</option>
                            <option value="3">3 Tickets</option>
                            <option value="4">4 Tickets</option>
                            <option value="5">5 Tickets</option>
                        </select>
                    </div>
                    
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold">Total Amount:</span>
                            <span id="totalAmount" class="text-xl font-bold text-blue-600">₹0</span>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 transition-colors font-semibold">
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Calculate total amount
        document.getElementById('no_of_tickets').addEventListener('change', function() {
            const tickets = parseInt(this.value) || 0;
            const pricePerTicket = 200;
            const total = tickets * pricePerTicket;
            document.getElementById('totalAmount').textContent = '₹' + total;
        });

        // Form validation and Razorpay integration
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const tickets = parseInt(formData.get('no_of_tickets'));
            const amount = tickets * 200 * 100; // Amount in paise
            
            if (!formData.get('user_name') || !formData.get('email') || !tickets) {
                alert('Please fill all required fields');
                return;
            }
            
            // Razorpay payment
            const options = {
                key: '<?php echo RAZORPAY_KEY_ID; ?>',
                amount: amount,
                currency: 'INR',
                name: 'Movie Ticket Booking',
                description: 'Ticket booking for <?php echo htmlspecialchars($movie['title']); ?>',
                handler: function(response) {
                    // Add payment ID to form data
                    formData.append('payment_id', response.razorpay_payment_id);
                    
                    // Submit booking
                    fetch('process_booking.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Booking confirmed! Check your email for details.');
                            window.location.href = 'index.php';
                        } else {
                            alert('Booking failed: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('An error occurred. Please try again.');
                    });
                },
                prefill: {
                    name: formData.get('user_name'),
                    email: formData.get('email')
                }
            };
            
            const rzp = new Razorpay(options);
            rzp.open();
        });
    </script>
</body>
</html>