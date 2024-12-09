<!-- LOGIN PAGE -->
<?php
session_start(); // Start the session
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-left">Fernandes' Library</div>
        <div class="dropdown">
            <button>Menu</button>
            <div class="dropdown-content">
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="welcome-message">
            <p>Welcome to <strong>Fernandes' Library</strong> – Your Gateway to Knowledge, Inspiration, and Endless Stories.</p>
        </div>

        <div class="login-box">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <!-- Input for Username -->
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" 
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                </div>

                <!-- Input for Password -->
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <!-- Submit Button -->
                <div class="input-group">
                    <button type="submit" class="btn">Login</button>
                </div>
            </form>

            <!-- "Do not have an Account?" Text and Register Button -->
            <div class="no-account">
                <p>Do not have an Account?</p>
                <button type="button" class="btn" onclick="location.href='register.php';">Register</button>
            </div>

            <!-- Success/Error Message -->
            <div id="message">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Step 1: Connect to the database
                    $conn = new mysqli("localhost", "root", "", "LibraryDB");

                    // Check connection
                    if ($conn->connect_error) {
                        echo "<p class='error-box'>Connection failed: " . $conn->connect_error . "</p>";
                    } else {
                        // Step 2: Retrieve form data
                        $username = $_POST['username'];
                        $password = $_POST['password'];

                        // Step 3: Query the database for the user
                        $sql = "SELECT * FROM Users WHERE Username = '$username' AND Password = '$password'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            // Successful login
                            $_SESSION['username'] = $username; // Store username in session
                            echo "<script>setTimeout(() => { window.location.href = 'search.php'; });</script>"; // Redirect after 2 seconds
                        } else {
                            // Invalid credentials
                            echo "<p class='error-box'>Invalid username or password. Please try again.</p>";
                        }
                    }
                    // Close connection
                    $conn->close();
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>Copyright &copy; 2024. Fernandes' Library, by Pedro Fernandes</p>
    </footer>
</body>
</html>
