<?php
// Create uploads directory structure
$directories = [
    'uploads',
    'uploads/posters'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Create a simple placeholder image using GD
if (extension_loaded('gd')) {
    $width = 300;
    $height = 400;
    
    $image = imagecreate($width, $height);
    $bg_color = imagecolorallocate($image, 204, 204, 204);
    $text_color = imagecolorallocate($image, 102, 102, 102);
    
    imagefill($image, 0, 0, $bg_color);
    
    $text = "No Image";
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    imagejpeg($image, 'uploads/posters/placeholder.jpg', 90);
    imagedestroy($image);
    
    echo "Created placeholder image: uploads/posters/placeholder.jpg\n";
} else {
    echo "GD extension not loaded. Please create a placeholder image manually.\n";
}

echo "Setup complete!\n";
?>