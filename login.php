<?php
session_start(); // Start the session to manage authentication

// Database connection
$servername = "localhost";
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "crypbit_db"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for database connection errors
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit();
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Email and password are required."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Invalid email format."]);
        exit();
    }

    // Query to check user credentials
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user["password"])) {
            // Fetch wallet data
            $wallet_stmt = $conn->prepare("SELECT balance_usdc, balance_sol, balance_btc, balance_eth FROM wallets WHERE user_id = ?");
            $wallet_stmt->bind_param("i", $user["id"]);
            $wallet_stmt->execute();
            $wallet_result = $wallet_stmt->get_result();

            if ($wallet_result->num_rows === 1) {
                $wallet = $wallet_result->fetch_assoc();

                // Store user details in session
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["email"] = $email;

                // Respond with success and wallet balances
                echo json_encode([
                    "success" => true,
                    "message" => "Login successful.",
                    "balances" => $wallet,
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Wallet not found."]);
            }

            $wallet_stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "User not found."]);
    }

    $stmt->close();
}

$conn->close();
?>
