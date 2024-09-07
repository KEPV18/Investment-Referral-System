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

// استرجاع جميع عمليات السحب الخاصة بالمستخدم من جدول withdrawals
$sql_withdrawals = "SELECT * FROM withdrawals WHERE user_id = ? ORDER BY date DESC";
$stmt_withdrawals = $conn->prepare($sql_withdrawals);
$stmt_withdrawals->bind_param("i", $user_id);
$stmt_withdrawals->execute();
$withdrawals_result = $stmt_withdrawals->get_result();

$withdrawals = [];
if ($withdrawals_result->num_rows > 0) {
    while ($withdrawal = $withdrawals_result->fetch_assoc()) {
        $withdrawals[] = $withdrawal;
    }
}

$stmt_withdrawals->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات السحب</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
</head>
<body>
<?php include 'new_header.php'; ?>

<div class="container dashboard">
    <h2>طلبات السحب</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>تاريخ الطلب</th>
                    <th>المبلغ المطلوب</th>
                    <th>الحالة</th>
                    <th>تفاصيل الطلب</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($withdrawals as $withdrawal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($withdrawal['date']); ?></td>
                        <td><?php echo htmlspecialchars($withdrawal['amount']); ?> جنيه</td>
                        <td><?php echo htmlspecialchars($withdrawal['status']); ?></td>
                        <td><?php echo htmlspecialchars($withdrawal['details']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
<script src="scripts.js"></script>
</body>
</html>