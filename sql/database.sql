-- Create database
CREATE DATABASE IF NOT EXISTS movie_booking;
USE movie_booking;

-- Movies table
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    description TEXT,
    poster_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    movie_id INT NOT NULL,
    no_of_tickets INT NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    payment_id VARCHAR(100),
    booked_on TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Insert sample movies
INSERT INTO movies (title, date, time, description, poster_url) VALUES
('Avengers: Endgame', '2024-02-15', '19:00:00', 'The epic conclusion to the Infinity Saga.', '/placeholder.svg?height=400&width=300'),
('Spider-Man: No Way Home', '2024-02-16', '20:30:00', 'Spider-Man faces his greatest challenge yet.', '/placeholder.svg?height=400&width=300'),
('The Batman', '2024-02-17', '18:00:00', 'A new take on the Dark Knight.', '/placeholder.svg?height=400&width=300'),
('Top Gun: Maverick', '2024-02-18', '21:00:00', 'Maverick returns to the danger zone.', '/placeholder.svg?height=400&width=300');