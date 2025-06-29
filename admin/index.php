<?php
require_once '../includes/functions.php';

// Require admin login
requireAdminLogin();

$movies = getAllMovies();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (deleteMovie($_GET['delete'])) {
        header('Location: index.php?success=deleted');
        exit;
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    adminLogout();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Movie Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-red-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold">Admin Panel</h1>
                <p class="text-red-200">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="../index.php" class="text-red-200 hover:text-white">View Website</a>
                <a href="?logout=1" class="bg-red-700 hover:bg-red-800 px-3 py-1 rounded text-sm">Logout</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php
                switch ($_GET['success']) {
                    case 'added': echo 'Movie added successfully!'; break;
                    case 'updated': echo 'Movie updated successfully!'; break;
                    case 'deleted': echo 'Movie deleted successfully!'; break;
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Manage Movies</h2>
            <a href="add_movie.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Add New Movie
            </a>
        </div>

        <!-- Movies Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trailer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($movies as $movie): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-16 w-12 object-cover rounded" src="<?php echo getImageUrl($movie['poster_url']); ?>" alt="">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($movie['title']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($movie['description'], 0, 50)) . '...'; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo formatDate($movie['date']); ?><br>
                                <?php echo formatTime($movie['time']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php if (!empty($movie['trailer_url'])): ?>
                                    <a href="<?php echo htmlspecialchars($movie['trailer_url']); ?>" target="_blank" 
                                       class="text-blue-600 hover:text-blue-900">
                                        🎬 View Trailer
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400">No trailer</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <a href="?delete=<?php echo $movie['id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this movie? This will also delete all associated bookings.')"
                                   class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>