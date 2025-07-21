<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit;
}

require 'db_connection.php'; // Replace with your database connection file

// Get input data
$data = json_decode(file_get_contents("php://input"), true);
$selling_crypto = strtolower($data['selling_crypto']);
$buying_crypto = strtolower($data['buying_crypto']);
$selling_amount = $data['selling_amount'];
$user_id = $_SESSION['user_id'];

// Fetch current balances
$query = $conn->prepare("SELECT * FROM crypbit_db_wallets WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$wallet = $result->fetch_assoc();

if (!$wallet) {
    echo json_encode(["success" => false, "message" => "Wallet not found."]);
    exit;
}

// Check if the user has enough balance
if ($wallet["balance_$selling_crypto"] < $selling_amount) {
    echo json_encode(["success" => false, "message" => "Insufficient balance."]);
    exit;
}

// Perform the swap (assume a mock conversion rate for now)
$conversion_rate = 0.05; // Example: 1 USDC = 0.05 SOL
$buying_amount = $selling_amount * $conversion_rate;

// Update balances
$new_selling_balance = $wallet["balance_$selling_crypto"] - $selling_amount;
$new_buying_balance = $wallet["balance_$buying_crypto"] + $buying_amount;

$update_query = $conn->prepare("UPDATE crypbit_db_wallets SET balance_$selling_crypto = ?, balance_$buying_crypto = ? WHERE user_id = ?");
$update_query->bind_param("ddi", $new_selling_balance, $new_buying_balance, $user_id);

if ($update_query->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Swap successful!",
        "balances" => [
            strtoupper($selling_crypto) => $new_selling_balance,
            strtoupper($buying_crypto) => $new_buying_balance,
        ],
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update wallet balances."]);
}
?>
