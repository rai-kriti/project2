<?php
require_once '../includes/functions.php';
requireAdminLogin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $description = $_POST['description'] ?? '';
    $trailer_url = $_POST['trailer_url'] ?? '';
    
    if ($title && $date && $time && $description) {
        $poster_path = '';
        
        // Handle image upload
        if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadPosterImage($_FILES['poster_image']);
            if ($uploadResult['success']) {
                $poster_path = $uploadResult['filename'];
            } else {
                $error = $uploadResult['message'];
            }
        }
        
        if (empty($error)) {
            if (addMovie($title, $date, $time, $description, $poster_path, $trailer_url)) {
                header('Location: index.php?success=added');
                exit;
            } else {
                $error = 'Failed to add movie. Please try again.';
            }
        }
    } else {
        $error = 'Please fill all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Movie - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-red-600 text-white p-4 shadow-lg">
        <div class="container mx-auto">
            <a href="index.php" class="text-red-200 hover:text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Admin Panel
            </a>
            <h1 class="text-3xl font-bold mt-2">Add New Movie</h1>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Movie Details</h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Movie Title *</label>
                        <input type="text" id="title" name="title" required
                               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Show Date *</label>
                            <input type="date" id="date" name="date" required
                                   value="<?php echo htmlspecialchars($_POST['date'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                        
                        <div>
                            <label for="time" class="block text-sm font-medium text-gray-700 mb-2">Show Time *</label>
                            <input type="time" id="time" name="time" required
                                   value="<?php echo htmlspecialchars($_POST['time'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                  placeholder="Enter movie description..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label for="poster_image" class="block text-sm font-medium text-gray-700 mb-2">Movie Poster</label>
                        <input type="file" id="poster_image" name="poster_image" accept="image/*" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <p class="text-xs text-gray-500 mt-1">Upload JPG, PNG, or GIF (max 5MB)</p>
                    </div>
                    
                    <div>
                        <label for="trailer_url" class="block text-sm font-medium text-gray-700 mb-2">Trailer URL (Optional)</label>
                        <input type="url" id="trailer_url" name="trailer_url" 
                               value="<?php echo htmlspecialchars($_POST['trailer_url'] ?? ''); ?>"
                               placeholder="https://www.youtube.com/watch?v=VIDEO_ID"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <p class="text-xs text-gray-500 mt-1">Enter YouTube URL for the movie trailer</p>
                    </div>
                    
                    <div class="flex gap-4 pt-6">
                        <button type="submit" 
                                class="flex-1 bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md hover:shadow-lg">
                            Add Movie
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-400 transition-colors text-center font-semibold">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>