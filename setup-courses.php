<?php
// Setup database tables for course system
require_once 'config.php';

// Create courses table
$courses_table = "CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($courses_table)) {
    echo "✓ Courses table created/exists<br>";
} else {
    echo "✗ Error creating courses table: " . $conn->error . "<br>";
}

// Create user_course_purchases table (tracks who paid for what)
$purchases_table = "CREATE TABLE IF NOT EXISTS user_course_purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_unlocked BOOLEAN DEFAULT FALSE,
    unlocked_by_admin INT,
    unlocked_date TIMESTAMP NULL,
    transaction_id VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    UNIQUE KEY unique_user_course (user_id, course_id)
)";

if ($conn->query($purchases_table)) {
    echo "✓ User course purchases table created/exists<br>";
} else {
    echo "✗ Error creating purchases table: " . $conn->error . "<br>";
}

// Insert the main trading course if it doesn't exist
$check_course = "SELECT id FROM courses WHERE name = 'Complete Trading Mastery'";
$result = $conn->query($check_course);

if ($result->num_rows == 0) {
    $insert_course = "INSERT INTO courses (name, price, description) 
    VALUES ('Complete Trading Mastery', 300, 'Access all trading videos: Basics, Advanced, and Risk Management modules')";

    if ($conn->query($insert_course)) {
        echo "✓ Trading course created<br>";
    } else {
        echo "✗ Error creating course: " . $conn->error . "<br>";
    }
} else {
    echo "✓ Trading course already exists<br>";
}

echo "<br><strong>Database setup complete!</strong>";
