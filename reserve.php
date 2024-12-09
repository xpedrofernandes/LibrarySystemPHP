<?php
session_start(); // Start the session
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect if the user is not logged in
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "LibraryDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Reserve book
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isbn'])) {
    $isbn = $_POST['isbn'];
    $username = $_SESSION['username'];

    // Check if the book is already reserved
    $checkQuery = "SELECT Reserved FROM Books WHERE ISBN = '$isbn'";
    $checkResult = $conn->query($checkQuery);
    if ($checkResult->num_rows > 0) {
        $book = $checkResult->fetch_assoc();
        if ($book['Reserved'] === 'Y') {
            $message = "Book not available.";
            $messageClass = "error-box";
        } else {
            // Reserve the book
            $reserveQuery = "INSERT INTO ReservedBooks (ISBN, Username, ReservedDate) VALUES ('$isbn', '$username', NOW())";
            $updateBookQuery = "UPDATE Books SET Reserved = 'Y' WHERE ISBN = '$isbn'";

            if ($conn->query($reserveQuery) === TRUE && $conn->query($updateBookQuery) === TRUE) {
                $message = "Book reserved successfully!";
                $messageClass = "success-box";
            } else {
                $message = "Error reserving the book: " . $conn->error;
                $messageClass = "error-box";
            }
        }
    } else {
        $message = "Error: Book not found.";
        $messageClass = "error-box";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Book</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-left">Fernandes' Library</div>
        <div class="dropdown">
            <button>Menu</button>
            <div class="dropdown-content">
                <a href="search.php">Search for a Book</a>
                <a href="reserved_books.php">See Reserved Books</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="search-box">
            <h2>Reserve Book</h2>
            <p class="<?= htmlspecialchars($messageClass) ?>"><?= htmlspecialchars($message) ?></p>
            <div class="button-group">
                <a href="search.php" class="btn">Return to Search Page</a>
                <a href="reserved_books.php" class="btn">See Reserved Books</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>Copyright © 2024. Fernandes' Library, by Pedro Fernandes</p>
    </footer>
</body>
</html>