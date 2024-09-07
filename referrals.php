<?php
session_start();
require 'config.php';

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch referral reward details
$sql_detailed_referrals = "SELECT users.phone, users.balance, 
                           (SELECT SUM(reward_amount) FROM referral_rewards WHERE referral_id = users.id) AS bonus
                           FROM users 
                           JOIN referrals ON users.id = referrals.user_id 
                           WHERE referrals.referral_id = ?";
$stmt_detailed_referrals = $conn->prepare($sql_detailed_referrals);
$stmt_detailed_referrals->bind_param("i", $user_id);
$stmt_detailed_referrals->execute();
$detailed_referrals_result = $stmt_detailed_referrals->get_result();

$referral_details = [];
if ($detailed_referrals_result->num_rows > 0) {
    while ($referral = $detailed_referrals_result->fetch_assoc()) {
        $referral_details[] = $referral;
    }
}
$stmt_detailed_referrals->close();

// Fetch the number of referrals
$sql_count_referrals = "SELECT COUNT(*) AS referral_count FROM referrals WHERE referral_id = ?";
$stmt_count_referrals = $conn->prepare($sql_count_referrals);
$stmt_count_referrals->bind_param("i", $user_id);
$stmt_count_referrals->execute();
$stmt_count_referrals->bind_result($referral_count);
$stmt_count_referrals->fetch();
$stmt_count_referrals->close();

// Fetch total team earnings
$sql_total_earnings = "SELECT SUM(balance * 0.25) AS total_earnings FROM users 
                       JOIN referrals ON users.id = referrals.user_id 
                       WHERE referrals.referral_id = ?";
$stmt_total_earnings = $conn->prepare($sql_total_earnings);
$stmt_total_earnings->bind_param("i", $user_id);
$stmt_total_earnings->execute();
$stmt_total_earnings->bind_result($total_earnings);
$stmt_total_earnings->fetch();
$stmt_total_earnings->close();

// Fetch referral code and link
$sql_user = "SELECT referral_code FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$referral_code = $user['referral_code'];
$referral_link = "http://alibaba1.000.pe/register.php?ref=" . urlencode($referral_code);
$stmt_user->close();

// Fetch referral balance
$sql_referral_balance = "SELECT SUM(reward_amount) AS referral_balance FROM referral_rewards WHERE user_id = ?";
$stmt_referral_balance = $conn->prepare($sql_referral_balance);
$stmt_referral_balance->bind_param("i", $user_id);
$stmt_referral_balance->execute();
$stmt_referral_balance->bind_result($referral_balance);
$stmt_referral_balance->fetch();
$stmt_referral_balance->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral Details</title>
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<?php include 'new_header.php'; ?>

<div class="container mx-auto p-4">
    <h3 class="text-2xl font-semibold mb-4">Referral Rewards Details</h3>
    <div class="info-box mb-4">
        <p class="font-bold">Number of Registered Members with Your Code:</p>
        <p class="text-lg"><?php echo htmlspecialchars($referral_count); ?></p>
    </div>
    <div class="info-box mb-4">
        <p class="font-bold">Total Earnings from the Team:</p>
        <p class="text-lg"><?php echo htmlspecialchars($total_earnings ?? '0'); ?> EGP</p>
    </div>
    <div class="info-box mb-4">
        <p class="font-bold">Your Referral Code:</p>
        <p class="text-lg"><?php echo htmlspecialchars($referral_code); ?></p>
    </div>
    <div class="info-box mb-4">
        <p class="font-bold">Referral Link:</p>
        <div class="flex items-center">
            <input type="text" id="referral-link" class="border rounded p-2 flex-grow" value="<?php echo htmlspecialchars($referral_link); ?>" readonly>
            <button class="ml-2 bg-blue-500 text-white px-4 py-2 rounded" onclick="copyLink()">Copy Link</button>
        </div>
        <div class="qrcode-container mt-4">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data=<?php echo urlencode($referral_link); ?>" alt="QR Code">
        </div>
    </div>
    <div class="info-box mb-4">
        <p class="font-bold">Referral Balance:</p>
        <p class="text-lg"><?php echo htmlspecialchars($referral_balance ?? '0'); ?> EGP</p>
    </div>
    <div class="table-container overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-6 text-left">Referral</th>
                    <th class="py-3 px-6 text-left">Added Amount</th>
                    <th class="py-3 px-6 text-left">Your Bonus (25%)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($referral_details as $referral): ?>
                    <tr>
                        <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($referral['phone']); ?></td>
                        <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($referral['balance']); ?> EGP</td>
                        <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($referral['bonus']); ?> EGP</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
<script>
function copyLink() {
    var copyText = document.getElementById("referral-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices
    document.execCommand("copy");
    alert("Link copied: " + copyText.value);
}
</script>
</body>
</html>
