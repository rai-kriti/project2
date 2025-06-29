<?php
require_once 'includes/functions.php';
$movies = getAllMovies();

// Get latest 5 movies for carousel
$latestMovies = array_slice($movies, 0, 5);
// Get remaining movies for grid
$otherMovies = array_slice($movies, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CineBook - Premium Movie Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .hero-slide {
            background-size: cover;
            background-position: center;
        }
        .movie-card {
            transition: all 0.4s ease;
        }
        .movie-card:hover {
            transform: translateY(-8px) scale(1.02);
        }
        .carousel-container {
            position: relative;
            overflow: hidden;
        }
        .carousel-slide {
            display: none;
            animation: fadeIn 0.8s ease-in-out;
        }
        .carousel-slide.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .gradient-overlay {
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%);
        }
        .trailer-overlay {
            transition: opacity 0.3s ease;
        }
        /* Hero section spacing */
        .hero-section {
            margin-top: 5rem; /* Space for fixed header */
            height: calc(100vh - 5rem); /* Full viewport minus header */
            min-height: 600px;
            max-height: 900px;
        }
        @media (min-width: 768px) {
            .hero-section {
                margin-top: 6rem;
                height: calc(100vh - 6rem);
            }
        }
        .hero-content {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }
        /* Sleek text styling */
        .sleek-text {
            font-family: 'Segoe UI', system-ui, sans-serif;
            font-weight: 350;
            letter-spacing: -0.015em;
        }
        /* Wider cards */
        .wider-card {
            max-width: 380px;
        }
    </style>
</head>
<body class="bg-black text-white">
    <!-- Header -->
    <header class="fixed top-0 w-full z-50 bg-gradient-to-b from-black/80 to-transparent backdrop-blur-sm h-20 md:h-24">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                        🎬 CineBook
                    </h1>
                    <nav class="hidden md:flex space-x-6">
                        <a href="#" class="text-white/80 hover:text-white transition-colors">Home</a>
                        <a href="#movies" class="text-white/80 hover:text-white transition-colors">Movies</a>
                        <a href="#" class="text-white/80 hover:text-white transition-colors">About</a>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-right">
                        <p class="text-sm text-gray-300">Premium Experience</p>
                        <p class="text-lg font-semibold text-yellow-400">₹200 per ticket</p>
                    </div>
                    <a href="admin/" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Admin
                    </a>
                </div>
            </div>
        </div>
    </header>

    <?php if (!empty($latestMovies)): ?>
    <!-- Hero Carousel Section - Poster Dominant Layout -->
    <section class="hero-section rounded-b-3xl shadow-2xl overflow-hidden">
        <div class="relative h-full carousel-container">
            <?php foreach ($latestMovies as $index => $movie): ?>
                <div class="carousel-slide <?php echo $index === 0 ? 'active' : ''; ?> absolute inset-0 hero-slide"
                     style="background-image: url('<?php echo getImageUrl($movie['poster_url']); ?>');">
                     
                    <!-- Dark overlay for better text contrast -->
                    <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/60 to-black/80 z-0"></div>

                    <!-- Hero content with poster-dominant layout -->
                    <div class="relative z-10 flex items-center justify-center h-full px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col lg:flex-row items-center gap-8 max-w-7xl w-full hero-content">
                            
                            <!-- Larger Movie Poster -->
                            <div class="w-full lg:w-2/3 flex justify-center">
                                <img src="<?php echo getImageUrl($movie['poster_url']); ?>"
                                     class="w-full max-w-md rounded-2xl shadow-2xl border-4 border-white/20 transform transition-transform duration-500 hover:scale-105"
                                     alt="Poster of <?php echo htmlspecialchars($movie['title']); ?>">
                            </div>

                            <!-- Sleek Text Content -->
                            <div class="w-full lg:w-1/3 text-white space-y-5 sleek-text">
                                <div>
                                    <h2 class="text-4xl md:text-5xl font-bold leading-tight mb-3">
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </h2>
                                    
                                    <div class="flex flex-wrap items-center gap-3 text-lg text-gray-300 mb-4">
                                        <span class="flex items-center gap-1 bg-gray-800/50 px-3 py-1 rounded-full">
                                            <span>⭐</span>
                                            <span>4.8/5</span>
                                        </span>
                                        <span class="bg-gray-800/50 px-3 py-1 rounded-full">
                                            <?php echo formatDate($movie['date']); ?>
                                        </span>
                                        <span class="bg-gray-800/50 px-3 py-1 rounded-full">
                                            <?php echo formatTime($movie['time']); ?>
                                        </span>
                                    </div>
                                </div>

                                <p class="text-gray-200 leading-relaxed text-base md:text-lg mb-6">
                                    <?php echo htmlspecialchars(substr($movie['description'], 0, 180)) . '...'; ?>
                                </p>

                                <div class="flex flex-col gap-4">
                                    <a href="booking.php?movie_id=<?php echo $movie['id']; ?>" 
                                       class="bg-white text-black px-6 py-3 rounded-lg font-bold hover:bg-gray-300 transition flex items-center justify-center gap-2 text-center">
                                        <span>🎟️</span>
                                        <span>Book Now - ₹200</span>
                                    </a>

                                    <?php if (!empty($movie['trailer_url'])): ?>
                                        <button onclick="playTrailer('<?php echo getYouTubeEmbedUrl($movie['trailer_url']); ?>')" 
                                                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg font-bold hover:from-blue-700 hover:to-purple-700 transition flex items-center justify-center gap-2">
                                            <span>▶️</span>
                                            <span>Watch Trailer</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slide Indicators -->
                    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
                        <?php foreach ($latestMovies as $i => $m): ?>
                            <button onclick="goToSlide(<?php echo $i; ?>)" 
                                    class="w-3 h-3 rounded-full transition-all duration-300 <?php echo $i === $index ? 'bg-white scale-125' : 'bg-white/40'; ?>" 
                                    data-slide="<?php echo $i; ?>"></button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Carousel Navigation Arrows -->
            <button onclick="previousSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-black/70 p-3 rounded-full text-white hover:bg-black/90 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-black/70 p-3 rounded-full text-white hover:bg-black/90 shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </section>
    <?php endif; ?>

    <!-- All Movies Section with Wider Cards -->
    <section id="movies" class="py-16 bg-gradient-to-b from-black to-gray-900">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">All Movies</h2>
                <p class="text-xl text-gray-400">Discover our complete collection of amazing films</p>
            </div>
            
            <?php if (empty($movies)): ?>
                <div class="text-center py-20">
                    <div class="text-8xl mb-6">🎭</div>
                    <h3 class="text-3xl font-bold mb-4 text-gray-300">No Movies Available</h3>
                    <p class="text-xl text-gray-500">Check back soon for exciting new releases!</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 justify-center">
                    <?php foreach ($movies as $movie): ?>
                        <div class="movie-card wider-card mx-auto bg-gray-800/50 backdrop-blur-sm rounded-2xl overflow-hidden shadow-2xl hover:shadow-3xl border border-gray-700/50" 
                             onmouseenter="playCardTrailer(this)" 
                             onmouseleave="stopCardTrailer(this)">
                            <div class="relative group h-96">
                                <img src="<?php echo getImageUrl($movie['poster_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($movie['title']); ?>" 
                                     class="poster-image w-full h-full object-cover transition-opacity duration-300">
                                
                                <!-- Auto-playing trailer on hover -->
                                <?php if (!empty($movie['trailer_url'])): ?>
                                    <div class="trailer-overlay absolute inset-0 opacity-0 transition-opacity duration-300">
                                        <iframe 
                                            class="trailer-iframe w-full h-full object-cover"
                                            data-src="<?php echo getYouTubeEmbedUrl($movie['trailer_url']); ?>" 
                                            frameborder="0" 
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                    
                                    <!-- Play button overlay -->
                                    <div class="play-button absolute inset-0 flex items-center justify-center transition-opacity duration-300">
                                        <div class="bg-black bg-opacity-60 rounded-full p-4 backdrop-blur-sm">
                                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                            </svg>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Rating Badge -->
                                <div class="absolute top-4 right-4 bg-yellow-500 text-black px-3 py-1 rounded-full text-sm font-bold flex items-center">
                                    ⭐ 4.<?php echo rand(5, 9); ?>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <h3 class="text-2xl font-bold mb-3 text-white leading-tight">
                                    <?php echo htmlspecialchars($movie['title']); ?>
                                </h3>
                                
                                <p class="text-gray-300 mb-4 text-sm leading-relaxed line-clamp-3">
                                    <?php echo htmlspecialchars(substr($movie['description'], 0, 120)) . '...'; ?>
                                </p>
                                
                                <div class="space-y-3 mb-6">
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-3 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo formatDate($movie['date']); ?>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-400">
                                        <svg class="w-4 h-4 mr-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <?php echo formatTime($movie['time']); ?>
                                    </div>
                                </div>
                                
                                <!-- Card Buttons -->
                                <div class="flex flex-col gap-3">
                                    <a href="booking.php?movie_id=<?php echo $movie['id']; ?>" 
                                       class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-3 px-6 rounded-xl font-bold text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                        Book Now - ₹200
                                    </a>
                                    
                                    <?php if (!empty($movie['trailer_url'])): ?>
                                        <button onclick="playTrailer('<?php echo getYouTubeEmbedUrl($movie['trailer_url']); ?>')" 
                                                class="bg-gray-800/80 border border-gray-600 text-white py-3 px-6 rounded-xl font-bold hover:bg-gray-700 transition-all duration-300 flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                            </svg>
                                            Watch Trailer
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-black border-t border-gray-800">
        <div class="container mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <h3 class="text-3xl font-bold mb-4 bg-gradient-to-r from-blue-400 to-purple-500 bg-clip-text text-transparent">
                        🎬 CineBook
                    </h3>
                    <p class="text-gray-400 mb-6 text-lg leading-relaxed">
                        Your premium destination for the latest movies and unforgettable cinema experiences. 
                        Book your tickets now and enjoy the magic of cinema.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 p-3 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 p-3 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/></svg>
                        </a>
                        <a href="#" class="bg-gray-800 hover:bg-gray-700 p-3 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/></svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4 text-white">Admin</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="admin/" class="hover:text-blue-400 transition-colors">Admin Panel</a></li>
                        <li><a href="admin/add_movie.php" class="hover:text-blue-400 transition-colors">Add Movie</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-500">&copy; 2024 CineBook. All rights reserved. Made with ❤️ for movie lovers.</p>
            </div>
        </div>
    </footer>

    <!-- Trailer Modal -->
    <div id="trailerModal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="relative w-full max-w-4xl">
            <button onclick="closeTrailer()" class="absolute -top-12 right-0 text-white hover:text-gray-300 text-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <div class="relative w-full h-0 pb-[56.25%] bg-black rounded-lg overflow-hidden">
                <iframe id="trailerFrame" class="absolute inset-0 w-full h-full" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const indicators = document.querySelectorAll('[data-slide]');
        
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            indicators.forEach((indicator, i) => {
                indicator.classList.toggle('bg-white', i === index);
                indicator.classList.toggle('bg-white/40', i !== index);
                indicator.classList.toggle('scale-125', i === index);
            });
            currentSlide = index;
        }
        
        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }
        
        function previousSlide() {
            const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prevIndex);
        }
        
        function goToSlide(index) {
            showSlide(index);
        }
        
        // Auto-advance carousel
        setInterval(nextSlide, 6000);
        
        // Hero Trailer Modal Functions
        function playTrailer(url) {
            document.getElementById('trailerFrame').src = url;
            document.getElementById('trailerModal').classList.remove('hidden');
            document.getElementById('trailerModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        
        function closeTrailer() {
            document.getElementById('trailerFrame').src = '';
            document.getElementById('trailerModal').classList.add('hidden');
            document.getElementById('trailerModal').classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTrailer();
            }
        });
        
        // Close modal on background click
        document.getElementById('trailerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTrailer();
            }
        });
        
        // Movie Card Trailer Functions
        function playCardTrailer(card) {
            const trailerOverlay = card.querySelector('.trailer-overlay');
            const trailerIframe = card.querySelector('.trailer-iframe');
            const posterImage = card.querySelector('.poster-image');
            const playButton = card.querySelector('.play-button');
            
            if (trailerOverlay && trailerIframe) {
                // Load trailer src from data-src
                const trailerSrc = trailerIframe.getAttribute('data-src');
                if (trailerSrc && !trailerIframe.src) {
                    trailerIframe.src = trailerSrc;
                }
                
                // Show trailer, hide poster and play button
                trailerOverlay.style.opacity = '1';
                posterImage.style.opacity = '0';
                playButton.style.opacity = '0';
            }
        }
        
        function stopCardTrailer(card) {
            const trailerOverlay = card.querySelector('.trailer-overlay');
            const trailerIframe = card.querySelector('.trailer-iframe');
            const posterImage = card.querySelector('.poster-image');
            const playButton = card.querySelector('.play-button');
            
            if (trailerOverlay && trailerIframe) {
                // Hide trailer, show poster and play button
                trailerOverlay.style.opacity = '0';
                posterImage.style.opacity = '1';
                playButton.style.opacity = '1';
                
                // Stop video by removing src after a delay
                setTimeout(() => {
                    trailerIframe.src = '';
                }, 300);
            }
        }
    </script>
</body>
</html>