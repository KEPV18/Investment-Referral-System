<?php
include 'config.php'; // تأكد من أن ملف config.php يحتوي على تعريف $conn

// تحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// جلب تفاصيل الباقات من قاعدة البيانات
$query = "SELECT * FROM investment_plans";
$result = $conn->query($query);

if (!$result) {
    die("Invalid query: " . $conn->error);
}

$plans = [];
while ($row = $result->fetch_assoc()) {
    $min_investment = $row['min_investment'];
    $return_percentage = $row['return_percentage'];
    $duration = $row['duration'];
    
    // حساب العائد النهائي والربح اليومي
    $final_return = $min_investment + ($min_investment * $return_percentage / 100);
    $daily_profit = ($final_return - $min_investment) / $duration;
    
    // إضافة القيم المحسوبة إلى المصفوفة
    $row['final_return'] = $final_return;
    $row['daily_profit'] = $daily_profit;
    
    $plans[] = $row;
}

// معالجة طلب الاستثمار
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan_id'])) {
    $plan_id = intval($_POST['plan_id']);
    $user_id = $_SESSION['user_id'];

    // الحصول على معلومات الخطة
    $query = "SELECT min_investment, return_percentage, duration FROM investment_plans WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $plan = $stmt->get_result()->fetch_assoc();
    $min_investment = $plan['min_investment'];
    $return_percentage = $plan['return_percentage'];
    $duration = $plan['duration'];
    $final_return = $min_investment + ($min_investment * $return_percentage / 100);
    $daily_profit = ($final_return - $min_investment) / $duration;
    $stmt->close(); // إغلاق الاستعلام

    // التحقق من الرصيد
    $query = "SELECT balance FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();
    
    if ($balance >= $min_investment) {
        // خصم ثمن الباقة من الرصيد
        $new_balance = $balance - $min_investment;
        $query = "UPDATE users SET balance = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("di", $new_balance, $user_id);
        $stmt->execute();
        $stmt->close(); // إغلاق الاستعلام

        // تسجيل العملية في قاعدة بيانات الاستثمارات
        $query = "INSERT INTO investments (user_id, plan_id, amount, final_return, daily_profit, purchase_date, maturity_date) VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY))";
        $stmt = $conn->prepare($query);
        $maturity_date = $duration;
        $stmt->bind_param("iidddi", $user_id, $plan_id, $min_investment, $final_return, $daily_profit, $maturity_date);
        $stmt->execute();
        $stmt->close(); // إغلاق الاستعلام

        $message = "<p class='text-green-600'>Plan purchased successfully!</p>";
    } else {
        $message = "<div class='alert text-red-600'>Insufficient balance. <a href='dashboard.php' class='underline text-blue-500'>Go to recharge page</a> to top up your balance.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Plans</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="container mx-auto p-4">
        <?php include 'new_header.php'; ?>

        <?php echo $message; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($plans as $plan): ?>
            <div class="card bg-white p-6 rounded-lg shadow-md">
                <img src="alibaba-group-710666.jpg" alt="Investment Plan" class="rounded-t-lg">
                <h2 class="text-xl font-bold my-2"><?php echo htmlspecialchars($plan['name']); ?></h2>
                <p>Minimum Investment: <?php echo htmlspecialchars($plan['min_investment']); ?> EGP</p>
                <p>Return Percentage: <?php echo htmlspecialchars($plan['return_percentage']); ?>%</p>
                <p>Final Return: <?php echo htmlspecialchars($plan['final_return']); ?> EGP</p>
                <p>Plan Duration: <?php echo htmlspecialchars($plan['duration']); ?> days</p>
                <p>Daily Profit: <?php echo htmlspecialchars($plan['daily_profit']); ?> EGP</p>
                <p><?php echo htmlspecialchars($plan['details']); ?></p>
                <form method="post" class="mt-4">
    <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($plan['id']); ?>">
    <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Invest Now</button>
</form>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
