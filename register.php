<!-- REGISTER PAGE -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

    <!-- Main Content -->
    <div class="container">
        <div class="register-box">
            <h2>Register</h2>

            <!-- Success/Error Message -->
            <div id="message">
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $conn = new mysqli("localhost", "root", "", "LibraryDB");

                    if ($conn->connect_error) {
                        echo "<p class='error-box'>Connection failed: " . $conn->connect_error . "</p>";
                    } else {
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        $confirm_password = $_POST['confirm_password'];
                        $firstname = $_POST['firstname'];
                        $surname = $_POST['surname'];
                        $addressline1 = $_POST['addressline1'];
                        $addressline2 = $_POST['addressline2'];
                        $city = $_POST['city'];
                        $telephone = $_POST['telephone'];
                        $mobile = $_POST['mobile'];
                        $errors = [];

                        if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
                        if (strlen($password) != 6) $errors[] = "Password must be exactly 6 characters long.";
                        if (!is_numeric($mobile) || strlen($mobile) != 10) $errors[] = "Mobile number must be numeric and exactly 10 digits.";
                        if (!is_numeric($telephone)) $errors[] = "Telephone number must be numeric.";

                        $sql = "SELECT * FROM Users WHERE Username = '$username'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) $errors[] = "Username already exists. Please choose a different username.";

                        if (!empty($errors)) {
                            foreach ($errors as $error) {
                                echo "<p class='error-box'>$error</p>";
                            }
                        } else {
                            $sql = "INSERT INTO Users (Username, Password, FirstName, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile)
                                    VALUES ('$username', '$password', '$firstname', '$surname', '$addressline1', '$addressline2', '$city', '$telephone', '$mobile')";

                            if ($conn->query($sql) === TRUE) {
                                echo "<p class='success-box'>Registration successful! Return to the Login Page.</p>";
                            } else {
                                echo "<p class='error-box'>Error: " . $conn->error . "</p>";
                            }
                        }
                    }
                    $conn->close();
                }
                ?>
            </div>

            <form action="register.php" method="POST">
                <!-- Input for Username -->
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" 
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>
                </div>

                <!-- Input for Password -->
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password (6 characters)" required>
                </div>

                <!-- Input for Confirm Password -->
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

                <!-- Input for First Name -->
                <div class="input-group">
                    <input type="text" name="firstname" placeholder="First Name" 
                        value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>" required>
                </div>

                <!-- Input for Last Name -->
                <div class="input-group">
                    <input type="text" name="surname" placeholder="Last Name" 
                        value="<?= isset($_POST['surname']) ? htmlspecialchars($_POST['surname']) : '' ?>" required>
                </div>

                <!-- Input for Address Line 1 -->
                <div class="input-group">
                    <input type="text" name="addressline1" placeholder="Address Line 1" 
                        value="<?= isset($_POST['addressline1']) ? htmlspecialchars($_POST['addressline1']) : '' ?>" required>
                </div>

                <!-- Input for Address Line 2 -->
                <div class="input-group">
                    <input type="text" name="addressline2" placeholder="Address Line 2" 
                        value="<?= isset($_POST['addressline2']) ? htmlspecialchars($_POST['addressline2']) : '' ?>" required>
                </div>

                <!-- Input for City -->
                <div class="input-group">
                    <input type="text" name="city" placeholder="City" 
                        value="<?= isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '' ?>" required>
                </div>

                <!-- Input for Telephone -->
                <div class="input-group">
                    <input type="text" name="telephone" placeholder="Telephone" 
                        value="<?= isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : '' ?>" required>
                </div>

                <!-- Input for Mobile Number -->
                <div class="input-group">
                    <input type="text" name="mobile" placeholder="Mobile Number (10 digits)" 
                        value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>" required>
                </div>

                <!-- Buttons -->
                <div class="input-group">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Fernandes' Library, by Pedro Fernandes</p>
    </footer>
</body>
</html>
