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

// Get the logged-in user's username
$username = $_SESSION['username'];

// Handle removing a reservation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservationId = $_POST['reservation_id'];

    // Remove the reservation from the ReservedBooks table
    $deleteQuery = "DELETE FROM ReservedBooks WHERE ReservationID = '$reservationId'";
    if ($conn->query($deleteQuery) === TRUE) {
        // Update the Reserved field in the Books table back to 'N'
        $isbn = $_POST['isbn'];
        $updateBookQuery = "UPDATE Books SET Reserved = 'N' WHERE ISBN = '$isbn'";
        $conn->query($updateBookQuery);

        $message = "<p class='success-box'>Reservation removed successfully!</p>";
    } else {
        $message = "<p class='error-box'>Error removing reservation: " . $conn->error . "</p>";
    }
}

// Fetch reserved books for the logged-in user
$query = "SELECT rb.ReservationID, b.BookTitle, b.Author, b.Year, b.Category, rb.ReservedDate, b.ISBN
          FROM ReservedBooks rb
          INNER JOIN Books b ON rb.ISBN = b.ISBN
          WHERE rb.Username = '$username'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserved Books</title>
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
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </header>


    <!-- Main Content -->
    <div class="container">
        <div class="reserved-books-box">
            <h2>My Reserved Books</h2>
            
            <!-- Display Success/Error Message -->
            <?= isset($message) ? $message : '' ?>

            <?php if ($result->num_rows > 0) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th style="text-align: center;">Year</th>
                            <th style="text-align: center;">Category</th>
                            <th style="text-align: center;">Reserved Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['BookTitle']) ?></td>
                                <td><?= htmlspecialchars($row['Author']) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row['Year']) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row['Category']) ?></td>
                                <td style="text-align: center;"><?= htmlspecialchars($row['ReservedDate']) ?></td>
                                <td>
                                    <!-- Remove Reservation Form -->
                                    <form method="POST" action="">
                                        <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($row['ReservationID']) ?>">
                                        <input type="hidden" name="isbn" value="<?= htmlspecialchars($row['ISBN']) ?>">
                                        <button type="submit" class="btn btn-small">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No books currently reserved.</p>
            <?php endif; ?>

            <!-- Return to Search Page Button -->
            <div class="button-group">
                <a href="search.php" class="btn">Return to Search Page</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>Copyright &copy; 2024. Fernandes' Library, by Pedro Fernandes</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>