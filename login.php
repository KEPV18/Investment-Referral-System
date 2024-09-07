<?php
require 'config.php'; // Database connection file

// Process login data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Phone number not registered.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
</head>
<body class="bg-background text-foreground">

    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Login Form Container -->
    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <div class="max-w-md w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8 transition-transform transform hover:scale-105">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">Login</h2>

            <!-- Display error message if exists -->
            <?php if (isset($error_message)): ?>
                <div class="bg-destructive text-destructive-foreground p-4 mb-4 rounded-lg">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="post">
                <div class="mb-4">
                    <label for="phone" class="block text-muted-foreground">Phone Number:</label>
                    <input type="text" id="phone" name="phone" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-muted-foreground">Password:</label>
                    <input type="password" id="password" name="password" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>
                
                <button type="submit" class="bg-primary text-primary-foreground w-full py-2 rounded hover:bg-secondary">Login</button>
            </form>
        </div>
    </div>

    <!-- Footer - Fixed Bottom Navbar -->
    <footer>
        <nav class="navbar navbar-light bg-light navbar-expand fixed-bottom">
            <div class="container-fluid">
                <ul class="navbar-nav flex-row justify-content-between mx-auto">
                    <li class="nav-item">
                        <a class="nav-link text-center" href="login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-center" href="register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </footer>

</body>
</html>
