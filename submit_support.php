<?php
// Konfiguracja połączenia z bazą danych
$servername = "localhost";
$username = "root"; // Domyślny użytkownik XAMPP
$password = "";     // Domyślne hasło XAMPP
$dbname = "crypbit_db";

// Tworzenie połączenia z bazą danych
$conn = new mysqli($servername, $username, $password, $dbname);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Połączenie z bazą danych nie powiodło się: " . $conn->connect_error);
}

// Sprawdzenie, czy formularz został przesłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobieranie danych z formularza
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // Walidacja danych
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        die("Wszystkie pola formularza są wymagane.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Podano nieprawidłowy adres e-mail.");
    }

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare("INSERT INTO support_tickets (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Wykonanie zapytania
    if ($stmt->execute()) {
        echo "Zgłoszenie zostało pomyślnie wysłane.";
    } else {
        echo "Wystąpił błąd podczas przesyłania zgłoszenia: " . $stmt->error;
    }

    // Zamknięcie przygotowanego zapytania
    $stmt->close();
}

// Zamknięcie połączenia z bazą danych
$conn->close();
?>
