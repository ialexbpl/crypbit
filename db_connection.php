$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Database connection failed: " . $conn->connect_error);
}
