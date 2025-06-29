<?php
require_once 'includes/functions.php';
$movies = getAllMovies();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Ticket Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .movie-card {
            transition: all 0.3s ease;
        }
        .movie-card:hover {
            transform: translateY(-5px);
        }
        .trailer-overlay {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold flex items-center">
                        🎬 <span class="ml-2">CineBook</span>
                    </h1>
                    <p class="text-blue-200 mt-1">Your favorite movies, just a click away!</p>
                </div>
                <div class="hidden md:flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-sm text-blue-200">Book Now</p>
                        <p class="text-lg font-semibold">₹200 per ticket</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Now Showing</h2>
            <p class="text-gray-600">Discover amazing movies and book your tickets instantly</p>
        </div>
        
        <?php if (empty($movies)): ?>
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🎭</div>
                <p class="text-xl text-gray-600 mb-2">No movies available at the moment</p>
                <p class="text-gray-500">Check back soon for exciting new releases!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl">
                        <div class="relative group h-80">
                            <img src="<?php echo getImageUrl($movie['poster_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                 class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-0">
                            
                            <?php if (!empty($movie['trailer_url'])): ?>
                                <div class="trailer-overlay absolute inset-0 opacity-0 group-hover:opacity-100">
                                    <iframe 
                                        src="<?php echo getYouTubeEmbedUrl($movie['trailer_url']); ?>" 
                                        class="w-full h-full object-cover"
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                </div>
                                
                                <!-- Play button overlay -->
                                <div class="absolute inset-0 flex items-center justify-center group-hover:hidden">
                                    <div class="bg-black bg-opacity-60 rounded-full p-4 backdrop-blur-sm">
                                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                        </svg>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Movie rating badge -->
                            <div class="absolute top-3 right-3 bg-yellow-400 text-yellow-900 px-2 py-1 rounded-full text-xs font-bold">
                                ⭐ 4.5
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($movie['title']); ?></h3>
                            <p class="text-gray-600 mb-4 text-sm leading-relaxed"><?php echo htmlspecialchars(substr($movie['description'], 0, 100)) . '...'; ?></p>
                            
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php echo formatDate($movie['date']); ?>
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php echo formatTime($movie['time']); ?>
                                </div>
                            </div>
                            
                            <a href="booking.php?movie_id=<?php echo $movie['id']; ?>" 
                               class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 px-4 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 inline-block text-center font-semibold shadow-md hover:shadow-lg">
                                Book Now - ₹200
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center">
                <h3 class="text-2xl font-bold mb-2">CineBook</h3>
                <p class="text-gray-400 mb-4">Your premier destination for movie tickets</p>
                <div class="flex justify-center space-x-6 mb-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">About</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms</a>
                    <a href="admin/" class="text-blue-400 hover:text-blue-300 transition-colors">Admin Panel</a>
                </div>
                <p class="text-gray-500 text-sm">&copy; 2024 CineBook. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>