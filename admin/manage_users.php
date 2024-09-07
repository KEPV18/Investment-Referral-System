<?php
include 'config2.php'; // تأكد من أن ملف config.php يحتوي على تعريف $conn



// معالجة البحث
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = trim($_POST['search']);
}

// استعلام للحصول على قائمة المستخدمين
$sql = "SELECT * FROM users WHERE name LIKE ? OR phone LIKE ? ORDER BY id";
$stmt = $conn->prepare($sql);
$search_param = "%$search_query%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

// تحديث بيانات المستخدم إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_balance = $_POST['balance'];

    $update_sql = "UPDATE users SET name = ?, email = ?, phone = ?, balance = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssdi", $new_name, $new_email, $new_phone, $new_balance, $user_id);
    $update_stmt->execute();
    $update_stmt->close();

    echo "تم تحديث بيانات المستخدم بنجاح!";
}

// إضافة رصيد إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_balance'])) {
    $user_id = $_POST['user_id'];
    $additional_balance = $_POST['additional_balance'];

    $add_sql = "UPDATE users SET balance = balance + ? WHERE id = ?";
    $add_stmt = $conn->prepare($add_sql);
    $add_stmt->bind_param("di", $additional_balance, $user_id);
    $add_stmt->execute();
    $add_stmt->close();

    echo "<script>alert('تم إضافة الرصيد بنجاح!'); window.location.href='manage_users.php';</script>";
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* تنسيقات إضافية */
        .table {
            margin-top: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap; /* يجعل الأزرار تتكيف مع عرض الشاشة */
        }

        .edit-form, .add-balance-form {
            display: none;
            border: 1px solid #ccc;
            padding: 20px;
            margin-top: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
        }

        .edit-form label, .add-balance-form label {
            display: block;
            margin-bottom: 5px;
        }

        .edit-form input, .add-balance-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .edit-form button[type="submit"], .add-balance-form button[type="submit"] {
            background-color: #4CAF50;
            color: white;
        }

        .edit-form button[type="button"], .add-balance-form button[type="button"] {
            background-color: #f44336;
            color: white;
        }

        /* تحسين العرض على الشاشات الصغيرة */
        @media (max-width: 576px) {
            .table thead {
                display: none;
            }

            .table, .table tbody, .table tr, .table td {
                display: block;
                width: 100%;
            }

            .table tr {
                margin-bottom: 15px;
            }

            .table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .table td::before {
                content: attr(data-label);
                position: absolute;
                left: 15px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
    <script>
        function toggleEditForm(userId) {
            // إخفاء جميع نماذج التعديل الأخرى
            var editForms = document.querySelectorAll('.edit-form');
            editForms.forEach(function(form) {
                form.style.display = 'none';
            });

            // إظهار نموذج التعديل للمستخدم المحدد
            var form = document.getElementById('editForm-' + userId);
            if (form) {
                form.style.display = 'block';
            }
        }

        function hideForm(element) {
            element.closest('tr').style.display = 'none';
        }
    </script>
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
            <h2 class="text-center my-4">إدارة المستخدمين</h2>
            <form method="POST" class="form-inline justify-content-center mb-4">
                <input type="text" name="search" class="form-control mr-2" placeholder="ابحث عن مستخدم" value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="btn btn-primary">بحث</button>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الهاتف</th>
                            <th>الرصيد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><?php echo htmlspecialchars($user['id']); ?></td>
                                <td data-label="الاسم"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td data-label="البريد الإلكتروني"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td data-label="الهاتف"><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td data-label="الرصيد"><?php echo htmlspecialchars($user['balance']); ?> جنيه</td>
                                <td data-label="الإجراءات" class="action-buttons">
                                    <button class="btn btn-warning btn-sm" onclick="toggleEditForm(<?php echo $user['id']; ?>)">تعديل</button>
                                    <button class="btn btn-success btn-sm add-balance-btn" onclick="document.getElementById('addBalanceForm-<?php echo $user['id']; ?>').style.display='block'">إضافة رصيد</button>
                                </td>
                            </tr>
                            <!-- نموذج تعديل بيانات المستخدم -->
                            <tr id="editForm-<?php echo $user['id']; ?>" class="edit-form">
                                <td colspan="6">
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <label for="name">الاسم:</label>
                                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        <label for="email">البريد الإلكتروني:</label>
                                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        <label for="phone">الهاتف:</label>
                                        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                        <label for="balance">الرصيد:</label>
                                        <input type="number" name="balance" id="balance" value="<?php echo htmlspecialchars($user['balance']); ?>" required>
                                        <button type="submit" name="update_user" class="btn btn-primary">حفظ التعديلات</button>
                                        <button type="button" class="btn btn-danger" onclick="hideForm(this)">إلغاء</button>
                                    </form>
                                </td>
                            </tr>
                            <!-- نموذج إضافة رصيد -->
                            <tr id="addBalanceForm-<?php echo $user['id']; ?>" class="add-balance-form">
                                <td colspan="6">
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <label for="additional_balance">الرصيد الإضافي:</label>
                                        <input type="number" name="additional_balance" id="additional_balance" required>
                                        <button type="submit" name="add_balance" class="btn btn-success">إضافة الرصيد</button>
                                        <button type="button" class="btn btn-danger" onclick="hideForm(this)">إلغاء</button>
                                    </form>
                                </td>
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
