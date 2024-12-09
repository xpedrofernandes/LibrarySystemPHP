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

// Handle search and pagination
$searchQuery = ""; // Search query for title/author
$selectedCategory = ""; // Selected category from dropdown
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$itemsPerPage = 5; // Number of books per page
$offset = ($page - 1) * $itemsPerPage;

// Fetch categories for the dropdown menu
$categoryQuery = "SELECT CategoryID, CategoryDescription FROM Categories";
$categoryResult = $conn->query($categoryQuery);

// Build the search query with a join to include category descriptions
$sql = "SELECT b.*, c.CategoryDescription 
        FROM Books b
        LEFT JOIN Categories c ON b.Category = c.CategoryID
        WHERE 1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
    $sql .= " AND (b.BookTitle LIKE '%$searchQuery%' OR b.Author LIKE '%$searchQuery%')";
}
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $selectedCategory = $conn->real_escape_string($_GET['category']);
    $sql .= " AND b.Category = '$selectedCategory'";
}
$sql .= " LIMIT $offset, $itemsPerPage";

// Fetch books based on the search query
$bookResult = $conn->query($sql);

// Fetch total rows for pagination
$totalQuery = "SELECT COUNT(*) AS total 
               FROM Books b
               LEFT JOIN Categories c ON b.Category = c.CategoryID
               WHERE 1=1";
if (!empty($searchQuery)) {
    $totalQuery .= " AND (b.BookTitle LIKE '%$searchQuery%' OR b.Author LIKE '%$searchQuery%')";
}
if (!empty($selectedCategory)) {
    $totalQuery .= " AND b.Category = '$selectedCategory'";
}
$totalResult = $conn->query($totalQuery);
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header with Dropdown -->
    <header>
        <div class="header-left">Fernandes' Library</div>
        <div class="dropdown">
            <button>Menu</button>
            <div class="dropdown-content">
                <a href="reserved_books.php">See Reserved Books</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="search-box">
            <h2>Search for a Book</h2>
            <form method="GET" action="">
                <!-- Search by Title or Author -->
                <div class="input-group">
                    <input type="text" name="search" placeholder="Search by Title or Author" value="<?= htmlspecialchars($searchQuery) ?>">
                </div>

                <!-- Filter by Category -->
                <div class="input-group">
                    <select name="category" class="styled-select">
                        <option value="">All Categories</option>
                        <?php while ($categoryRow = $categoryResult->fetch_assoc()) : ?>
                            <option value="<?= $categoryRow['CategoryID'] ?>" <?= $selectedCategory == $categoryRow['CategoryID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($categoryRow['CategoryDescription']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="input-group">
                    <button type="submit" class="btn">Search</button>
                </div>
            </form>

            <!-- Display Results -->
            <?php if ($bookResult->num_rows > 0) : ?>
                <div class="results-box">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th style="text-align: center;">Year</th>
                                <th style="text-align: center;">Category</th>
                                <th>Reserve</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($bookRow = $bookResult->fetch_assoc()) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($bookRow['BookTitle']) ?></td>
                                    <td><?= htmlspecialchars($bookRow['Author']) ?></td>
                                    <td style="text-align: center;"><?= htmlspecialchars($bookRow['Year']) ?></td>
                                    <td style="text-align: center;"><?= htmlspecialchars($bookRow['CategoryDescription']) ?></td>
                                    <td>
                                        <form method="POST" action="reserve.php">
                                            <input type="hidden" name="isbn" value="<?= htmlspecialchars($bookRow['ISBN']) ?>">
                                            <button type="submit" class="btn btn-small">
                                                <?= $bookRow['Reserved'] === 'Y' ? 'Not Available' : 'Reserve' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <a href="?search=<?= urlencode($searchQuery) ?>&category=<?= urlencode($selectedCategory) ?>&page=<?= $i ?>" 
                               class="pagination-link <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>
            <?php else : ?>
                <p>No books found matching your search criteria.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>Copyright © 2024. Fernandes' Library, by Pedro Fernandes</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>