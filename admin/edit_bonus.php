<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المكافأة</title>
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
            <h2>تعديل المكافأة</h2>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="form-group">
                    <label for="bonus">المكافأة</label>
                    <input type="number" name="bonus" value="<?php echo $row['bonus']; ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">حفظ</button>
            </form>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary mt-4">العودة إلى لوحة التحكم</a>
</body>
</html>