<?php
include 'config2.php'; // تأكد من أن ملف config.php يحتوي على تعريف $conn

// استعلام للحصول على طلبات السحب
$sql = "SELECT * FROM withdrawals ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

// تحديث حالة الطلب إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_withdrawal'])) {
    $withdrawal_id = $_POST['withdrawal_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE withdrawals SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $withdrawal_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo "تم تحديث حالة الطلب بنجاح!";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة طلبات السحب</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
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
            <h2>إدارة طلبات السحب</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>معرف المستخدم</th>
                        <th>المبلغ</th>
                        <th>رقم الهاتف</th>
                        <th>الرصيد قبل السحب</th>
                        <th>الرصيد بعد السحب</th>
                        <th>تاريخ الطلب</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($withdrawal = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($withdrawal['id']); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['user_id']); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['amount']); ?> جنيه</td>
                            <td><?php echo htmlspecialchars($withdrawal['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['balance_before']); ?> جنيه</td>
                            <td><?php echo htmlspecialchars($withdrawal['balance_after']); ?> جنيه</td>
                            <td><?php echo htmlspecialchars($withdrawal['date']); ?></td>
                            <td><?php echo htmlspecialchars($withdrawal['status']); ?></td>
                            <td>
                                <button onclick="document.getElementById('editForm-<?php echo $withdrawal['id']; ?>').style.display='block'">تعديل</button>
                                <!-- نموذج تعديل حالة الطلب -->
                                <div id="editForm-<?php echo $withdrawal['id']; ?>" style="display: none;">
                                    <form method="POST">
                                        <input type="hidden" name="withdrawal_id" value="<?php echo $withdrawal['id']; ?>">
                                        <label for="status">الحالة الجديدة:</label>
                                        <select name="status" id="status" required>
                                            <option value="pending" <?php echo $withdrawal['status'] === 'pending' ? 'selected' : ''; ?>>قيد الانتظار</option>
                                            <option value="approved" <?php echo $withdrawal['status'] === 'approved' ? 'selected' : ''; ?>>موافق عليها</option>
                                            <option value="rejected" <?php echo $withdrawal['status'] === 'rejected' ? 'selected' : ''; ?>>مرفوضة</option>
                                        </select>
                                        <button type="submit" name="update_withdrawal">حفظ التعديلات</button>
                                        <button type="button" onclick="this.parentElement.style.display='none'">إلغاء</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="index.php" class="back-button">العودة إلى لوحة التحكم</a>
</body>
</html>