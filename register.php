<?php
require 'config.php'; // Database connection

// Set a default value for the referral code
$referral_code = '';

// Check for referral code in the URL
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $referral_code = htmlspecialchars($_GET['ref']);
    $referral_message = "Referral code found in the URL: " . $referral_code;
}

// Clear cache and cookies before registration
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Clear cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode('; ', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            setcookie($parts[0], '', time() - 3600, '/'); // Set cookie expiration in the past
        }
    }
    // Clear cache
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
    header("Pragma: no-cache"); // HTTP 1.0
    header("Expires: 0"); // Proxies

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Encrypt the password
    $wallet_number = $_POST['wallet_number'];
    $balance = 0; // Set an initial balance value
    $new_referral_code = bin2hex(random_bytes(5)); // Generate a new referral code

    // Check for a referral code in the input field if not present in the URL
    if (empty($referral_code) && isset($_POST['referral_code']) && !empty($_POST['referral_code'])) {
        $referral_code = htmlspecialchars($_POST['referral_code']);
        $referral_message = "Referral code found in the input field: " . $referral_code;
    }

    // Check if the user already exists based on phone number or email
    $check_sql = "SELECT * FROM users WHERE phone = ? OR email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $phone, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "Phone number or email is already registered.";
    } else {
        // Start a transaction to ensure the user and referral data are inserted correctly
        $conn->begin_transaction();

        try {
            // Insert the new user's data into the database
            $sql = "INSERT INTO users (name, phone, email, password, wallet_number, balance, referral_code) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssiss", $name, $phone, $email, $password, $wallet_number, $balance, $new_referral_code);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id; // Get the new user's ID

                // If there's a referral code, verify it
                if (!empty($referral_code)) {
                    $referral_sql = "SELECT id FROM users WHERE referral_code = ?";
                    $referral_stmt = $conn->prepare($referral_sql);
                    $referral_stmt->bind_param("s", $referral_code);
                    $referral_stmt->execute();
                    $referral_result = $referral_stmt->get_result();

                    if ($referral_result->num_rows > 0) {
                        $referral_user = $referral_result->fetch_assoc();
                        $referral_user_id = $referral_user['id'];

                        // Insert referral data into the referrals table
                        $insert_referral_sql = "INSERT INTO referrals (user_id, referral_id, date) VALUES (?, ?, NOW())";
                        $referral_insert_stmt = $conn->prepare($insert_referral_sql);
                        $referral_insert_stmt->bind_param("ii", $user_id, $referral_user_id);

                        if ($referral_insert_stmt->execute()) {
                            // Update the referral count for the referrer
                            $update_referral_count_sql = "UPDATE users SET referral_count = referral_count + 1 WHERE id = ?";
                            $update_referral_stmt = $conn->prepare($update_referral_count_sql);
                            $update_referral_stmt->bind_param("i", $referral_user_id);
                            $update_referral_stmt->execute();
                            $referral_message = "Referral code verified and registered successfully.";
                        } else {
                            throw new Exception("Error inserting referral data: " . $referral_insert_stmt->error);
                        }
                    } else {
                        throw new Exception("Invalid referral code.");
                    }
                } else {
                    $referral_message = "No referral code provided.";
                }

                $conn->commit();
                $success_message = "Registration successful! You will be redirected to the login page shortly.";
            } else {
                throw new Exception("Registration error: " . $stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = $e->getMessage();
        }
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
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
</head>
<body class="bg-background text-foreground">

    <!-- Include header -->
    <?php include 'header.php'; ?>

    <!-- Registration Form Container -->
    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <div class="max-w-md w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8 transition-transform transform hover:scale-105">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">Register</h2>

            <!-- Display error or success message -->
            <?php if (isset($error_message)): ?>
                <div class="bg-destructive text-destructive-foreground p-4 mb-4 rounded-lg">
                    <?php echo $error_message; ?>
                </div>
            <?php elseif (isset($success_message)): ?>
                <div class="bg-secondary text-secondary-foreground p-4 mb-4 rounded-lg">
                    <?php echo $success_message; ?>
                </div>
                <script>setTimeout(function() { window.location.href = 'login.php'; }, 7000);</script>
            <?php endif; ?>

            <!-- Display referral message -->
            <?php if (isset($referral_message)): ?>
                <div class="bg-muted text-muted-foreground p-4 mb-4 rounded-lg">
                    <?php echo $referral_message; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="post">
                <div class="mb-4">
                    <label for="name" class="block text-muted-foreground">Name:</label>
                    <input type="text" id="name" name="name" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-muted-foreground">Phone Number:</label>
                    <input type="text" id="phone" name="phone" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-muted-foreground">Email:</label>
                    <input type="email" id="email" name="email" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-muted-foreground">Password:</label>
                    <input type="password" id="password" name="password" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>

                <div class="mb-4">
                    <label for="wallet_number" class="block text-muted-foreground">Vodafone Cash Wallet Number:</label>
                    <input type="text" id="wallet_number" name="wallet_number" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                </div>

                <div class="mb-6">
                    <label for="referral_code" class="block text-muted-foreground">Referral Code (Optional):</label>
                    <input type="text" id="referral_code" name="referral_code" value="<?php echo htmlspecialchars($referral_code); ?>" class="w-full bg-input border border-muted rounded px-4 py-2">
                </div>

                <button type="submit" class="bg-primary text-primary-foreground w-full py-2 rounded hover:bg-secondary">Register</button>
            </form>
        </div>
    </div>

    <br><br><br><br>

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
