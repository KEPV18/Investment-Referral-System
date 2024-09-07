<?php
session_start();
require 'config2.php';


// استرجاع طلبات الدفع المعلقة
$sql = "SELECT pr.*, u.name as user_name, u.bonus, u.balance FROM payment_requests pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.status = 'pending'
        ORDER BY pr.created_at DESC";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        // الموافقة على الطلب
        $sql = "UPDATE payment_requests SET status = 'approved' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();

        // إضافة الرصيد للمستخدم
        $sql_user = "UPDATE users u
                     JOIN payment_requests pr ON u.id = pr.user_id
                     SET u.balance = u.balance + pr.amount
                     WHERE pr.id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $request_id);
        $stmt_user->execute();

        // إضافة تسجيل المكافأة في جدول referral_rewards
        $sql_referral_reward = "INSERT INTO referral_rewards (referral_id, reward_amount) VALUES (?, ?)";
        $stmt_referral = $conn->prepare($sql_referral_reward);
        $stmt_referral->bind_param("id", $referral_id, $reward_amount);
        $stmt_referral->execute();

    } elseif ($action == 'reject') {
        // رفض الطلب
        $sql = "UPDATE payment_requests SET status = 'rejected' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
}

// استرجاع طلبات الدفع المعلقة
$sql = "SELECT pr.*, u.name as user_name, u.bonus FROM payment_requests pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.status = 'pending'
        ORDER BY pr.created_at DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة طلبات الدفع</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
    <div class="flex h-screen">
        <div class="bg-primary w-1/5 p-4">
            <h2 class="text-primary-foreground text-lg font-semibold mb-4">لوحة التحكم</h2>
            <ul>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="index.php">الرئيسية</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="admin_payment_requests.php">طلبات الدفع</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="add_investment_plan.php">إضافة خطة استثمارية</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="manage_users.php">إدارة المستخدمين</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="manage_withdrawals.php">إدارة السحوبات</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer"><a href="user.php">عرض المستخدمين</a></li>
                <li class="py-2 hover:bg-primary/20 rounded-lg cursor-pointer">الإعدادات</li>
            </ul>
        </div>

        <div class="flex-1 bg-background p-4">
            <h2>طلبات الدفع المعلقة</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>المستخدم</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>التاريخ</th>
                        <th>المكافأة</th>
                        <th>رصيد الإحالات</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['amount']); ?></td>
                            <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($row['bonus']); ?></td>
                            <td><?php echo htmlspecialchars($row['balance']); ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="request_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">قبول</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger">رفض</button>
                                </form>
                                <a href="edit_bonus.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-primary">تعديل المكافأة</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="index.php" class="back-button btn btn-primary">العودة إلى لوحة التحكم</a>
</body>
</html>