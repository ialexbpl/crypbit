<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting
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

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch wallet data
$stmt = $conn->prepare("SELECT wallet_address, balance_usdc, balance_sol, balance_btc, balance_eth FROM wallets WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $wallet = $result->fetch_assoc();
    echo json_encode(["success" => true, "wallet" => $wallet]);
} else {
    echo json_encode(["success" => false, "message" => "Wallet not found."]);
}

$stmt->close();
$conn->close();
?>
