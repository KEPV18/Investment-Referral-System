<?php
session_start();
require 'config.php';

// عرض الأخطاء في حالة وجودها
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// استرجاع بيانات المستخدم
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$stmt_user->close();

if ($user) {
    $balance = $user['balance'];
    $referral_code = $user['referral_code'];
    $referral_bonus = 0; // Initialize referral_bonus to avoid undefined variable warning

    // استرجاع تفاصيل المكافآت من الإحالات
    $sql_detailed_referrals = "SELECT users.phone, users.balance, 
                               (SELECT SUM(reward_amount) FROM referral_rewards WHERE referral_id = users.id) AS bonus
                               FROM users WHERE id = ?";
    $stmt_detailed_referrals = $conn->prepare($sql_detailed_referrals);
    $stmt_detailed_referrals->bind_param("i", $referral_user_id); // Change $user_id to $referral_user_id
    $stmt_detailed_referrals->execute();
    $detailed_referrals_result = $stmt_detailed_referrals->get_result();

    // تخزين البيانات في مصفوفة مؤقتة
    $referral_details = [];
    if ($detailed_referrals_result->num_rows > 0) {
        while ($referral = $detailed_referrals_result->fetch_assoc()) {
            $referral_user_id = $referral['referral_user_id'];
            // تحقق مما إذا كان المكافأة قد تم احتسابها بالفعل لهذا المستخدم المُحال
            $sql_check_reward = "SELECT * FROM referral_rewards WHERE referral_id = ? AND user_id = ?";
            $stmt_check_reward = $conn->prepare($sql_check_reward);
            $stmt_check_reward->bind_param("ii", $referral_user_id, $user_id);
            $stmt_check_reward->execute();
            $reward_result = $stmt_check_reward->get_result();
            $reward_exists = $reward_result->num_rows > 0;
            $stmt_check_reward->close();

            if (!$reward_exists) {
                // تأكد من تعيين قيمة reward_amount
                $reward_amount = 0; // أو أي قيمة مناسبة حسب منطقك
                // إدراج المكافأة في الجدول referral_rewards
                $sql_insert_reward = "INSERT INTO referral_rewards (user_id, referral_id, reward_amount) VALUES (?, ?, ?)";
                $stmt_insert_reward = $conn->prepare($sql_insert_reward);
                $stmt_insert_reward->bind_param("iid", $user_id, $referral_user_id, $reward_amount);
                $stmt_insert_reward->execute();
                $stmt_insert_reward->close();
            }

            // تخزين بيانات الإحالات في مصفوفة مؤقتة
            $referral_details[] = $referral;
        }
    }

    // استرجاع عدد الإحالات
    $sql_count_referrals = "SELECT COUNT(*) AS referral_count FROM referrals WHERE referral_id = ?";
    $stmt_count_referrals = $conn->prepare($sql_count_referrals);
    $stmt_count_referrals->bind_param("i", $user_id);
    $stmt_count_referrals->execute();
    $stmt_count_referrals->bind_result($referral_count);
    $stmt_count_referrals->fetch();
    $stmt_count_referrals->close();

    // استرجاع مكافآت الإحالة من جدول referral_rewards
    $sql_referral_bonus = "SELECT SUM(reward_amount) AS total_bonus FROM referral_rewards WHERE user_id = ?";
    $stmt_referral_bonus = $conn->prepare($sql_referral_bonus);
    $stmt_referral_bonus->bind_param("i", $user_id);
    $stmt_referral_bonus->execute();
    $stmt_referral_bonus->bind_result($referral_bonus);
    $stmt_referral_bonus->fetch();
    $stmt_referral_bonus->close();
} else {
    // إذا لم يتم العثور على المستخدم، قم بتسجيل الخروج أو إعادة التوجيه
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
    <style>
        /* تحسين التباين بين النص والخلفية */
        .text-primary {
            color: #1a202c; /* لون النص الأساسي */
        }
        .text-secondary {
            color: #4a5568; /* لون النص الثانوي */
        }
        .text-muted-foreground {
            color: #718096; /* لون النص الباهت */
        }
        .bg-input {
            background-color: #edf2f7; /* لون الخلفية لحقل الإدخال */
        }
        .bg-primary {
            background-color: #2d3748; /* لون الخلفية الأساسي */
        }
        .text-primary-foreground {
            color: #edf2f7; /* لون النص الأساسي على خلفية داكنة */
        }
    </style>
</head>
<body class="bg-background text-foreground">

    <!-- Include header -->
    <?php include 'new_header.php'; ?>

    <!-- Dashboard Container -->
    <div class="flex flex-col items-center justify-center min-h-screen p-4">
        <div class="max-w-lg w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8">
            <h2 class="text-3xl font-bold mb-4 text-primary text-center">Dashboard</h2>

            <!-- User ID and Profile Image -->
            <div class="flex items-center justify-between mb-6 bg-secondary p-4 rounded-lg">
                <p class="text-xl font-semibold">ID: <?php echo htmlspecialchars($user_id ?? ''); ?></p>
                <img src="user.jpg" alt="User Image" class="w-16 h-16 rounded-full">
            </div>

            <!-- User Balance -->
            <div class="mb-4">
                <p class="text-muted-foreground text-sm">Current Balance:</p>
                <p class="text-2xl font-bold"><?php echo htmlspecialchars($balance ?? 0); ?> EGP</p>
            </div>

            <!-- Referral Bonus -->
            <div class="mb-4">
                <p class="text-muted-foreground text-sm">Referral Bonus:</p>
                <p class="text-2xl font-bold"><?php echo htmlspecialchars($referral_bonus ?? 0); ?> EGP</p>
            </div>

            <!-- Referral Count -->
            <div class="mb-4">
                <p class="text-muted-foreground text-sm">Number of Referrals:</p>
                <p class="text-2xl font-bold"><?php echo htmlspecialchars($referral_count ?? 0); ?></p>
            </div>

            <!-- Referral Link -->
            <div class="mb-6">
                <p class="text-muted-foreground text-sm">Referral Link:</p>
                <div class="flex items-center">
                    <input type="text" id="referral-link" value="http://alibaba1.000.pe/register.php?ref=<?php echo urlencode($referral_code); ?>" class="w-full bg-input border border-muted rounded px-4 py-2" readonly>
                    <button onclick="copyLink()" class="bg-primary text-primary-foreground ml-2 px-4 py-2 rounded">Copy</button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="grid grid-cols-2 gap-4">
                <a href="stripe.php" class="bg-primary text-primary-foreground py-2 text-center rounded hover:bg-secondary">Add Balance</a>
                <button onclick="showWithdrawBalanceModal()" class="bg-primary text-primary-foreground py-2 rounded hover:bg-secondary">Withdraw Balance</button>
            </div>

            <!-- Modal for Withdrawal -->
            <div id="withdrawBalanceModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-card p-6 rounded-lg max-w-md w-full">
                    <span class="text-muted-foreground cursor-pointer" onclick="closeWithdrawBalanceModal()">&times;</span>
                    <h2 class="text-xl font-bold mb-4 text-primary">Withdraw Balance</h2>
                    <p class="text-secondary">Withdrawals are available from 12 PM to 9 AM. The minimum withdrawal amount is 300 EGP. The withdrawn amount will be added to your account within 72 hours.</p>
                    <form id="withdrawForm" action="process_withdrawal.php" method="POST" onsubmit="return validateWithdrawal()">
                        <label for="withdraw-amount" class="block text-muted-foreground mt-4">Amount:</label>
                        <input type="number" id="withdraw-amount" name="withdraw-amount" class="w-full bg-input border border-muted rounded px-4 py-2" required>
                        <div class="mt-4 flex justify-between">
                            <button type="submit" class="bg-primary text-primary-foreground py-2 px-4 rounded hover:bg-secondary">Withdraw</button>
                            <button type="button" onclick="closeWithdrawBalanceModal()" class="bg-muted text-muted-foreground py-2 px-4 rounded hover:bg-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- More Actions -->
        <div class="flex flex-col space-y-4 max-w-lg w-full bg-card shadow-lg rounded-lg overflow-hidden p-6 mb-8">
            <a href="withdrawals.php" class="bg-primary text-primary-foreground py-2 text-center rounded hover:bg-secondary">Withdrawal History</a>
            <a href="https://t.me/alibaba0pe" class="bg-primary text-primary-foreground py-2 text-center rounded hover:bg-secondary" target="_blank">Contact Customer Service</a>
            <a href="code.php" class="bg-primary text-primary-foreground py-2 text-center rounded hover:bg-secondary">Redeem Rewards</a>
            <a href="logout.php" class="bg-primary text-primary-foreground py-2 text-center rounded hover:bg-secondary">Logout</a>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <script src="scripts.js"></script>
    <script>
        // Modal functionality
        function showWithdrawBalanceModal() {
            document.getElementById('withdrawBalanceModal').classList.remove('hidden');
        }

        function closeWithdrawBalanceModal() {
            document.getElementById('withdrawBalanceModal').classList.add('hidden');
        }

        // Copy referral link function
        function copyLink() {
            var copyText = document.getElementById("referral-link");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand("copy");
            alert("Referral link copied: " + copyText.value);
        }
    </script>

</body>
</html>
