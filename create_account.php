<?php
header('Content-Type: application/json');
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crypbit_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password are required."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format."]);
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate a random wallet address
    $walletAddress = "0x" . bin2hex(random_bytes(20)); // Creates a 40-character wallet address

    // Insert user into users table
    $stmt = $conn->prepare("INSERT INTO users (email, password, wallet_address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedPassword, $walletAddress);

    if ($stmt->execute()) {
        // Get the user ID of the newly created user
        $user_id = $conn->insert_id;

        // Insert the wallet with the generated address into the wallets table
        $wallet_stmt = $conn->prepare("INSERT INTO wallets (user_id, wallet_address, balance_usdc, balance_sol, balance_btc, balance_eth) VALUES (?, ?, 0.00, 0.00, 0.00, 0.00)");
        $wallet_stmt->bind_param("is", $user_id, $walletAddress);

        if ($wallet_stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Account and wallet created successfully.", "wallet_address" => $walletAddress]);
        } else {
            echo json_encode(["success" => false, "message" => "Account created, but wallet creation failed: " . $wallet_stmt->error]);
        }

        $wallet_stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Failed to create account: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
