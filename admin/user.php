<?php
include 'config2.php'; // تأكد من أن ملف config.php يحتوي على تعريف $conn

// استعلام للحصول على قائمة المستخدمين مع كلمة المرور
$sql = "SELECT id, name, phone, AES_DECRYPT(password, 'your_secret_key') AS decrypted_password FROM users ORDER BY id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المستخدمين</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
            <h2 class="text-center my-4">عرض المستخدمين</h2>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>الهاتف</th>
                            <th>كلمة المرور</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td><?php echo htmlspecialchars($user['decrypted_password']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-4">العودة إلى لوحة التحكم</a>
</body>
</html>
