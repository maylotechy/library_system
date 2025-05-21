<?php
// Include database connection
global $conn;
include 'config/db_connection.php'; // Make sure this sets up a PDO instance in $conn

echo "=== Admin User Registration ===\n";

// Get CLI input
function getInput($prompt) {
    echo $prompt;
    return trim(fgets(STDIN));
}

$username = getInput("Enter username: ");
$password = getInput("Enter password: ");
$email = getInput("Enter email: ");
$full_name = getInput("Enter full name: ");

// Predefined user type
$user_type = "admin";

// Validate input
if (empty($username) || empty($password) || empty($email) || empty($full_name)) {
    echo "All fields are required.\n";
    exit(1);
}

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute([':username' => $username]);
if ($stmt->fetch()) {
    echo "Username already exists. Please choose another one.\n";
    exit(1);
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert into database
$stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, user_type) VALUES (:username, :password, :email, :full_name, :user_type)");

$success = $stmt->execute([
    ':username'   => $username,
    ':password'   => $hashed_password,
    ':email'      => $email,
    ':full_name'  => $full_name,
    ':user_type'  => $user_type
]);

if ($success) {
    echo "Admin user registered successfully!\n";
} else {
    echo "Error: Could not insert admin user.\n";
}
