<?php
session_start();
require 'config.php'; // ملف الاتصال بقاعدة البيانات

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// التحقق من وجود القيم المطلوبة
if (!isset($_POST['withdraw-amount']) || empty($_POST['withdraw-amount'])) {
    echo "يرجى إدخال مبلغ السحب.";
    exit();
}

$withdraw_amount = trim($_POST['withdraw-amount']);

// استعلام للحصول على الرصيد الحالي
$query = "SELECT balance FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();

if ($balance >= $withdraw_amount) {
    $balance_before = $balance;
    $balance_after = $balance - $withdraw_amount;

    // تحديث الرصيد في جدول المستخدمين
    $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
    $stmt = $conn->prepare($update_balance_query);
    $stmt->bind_param("di", $balance_after, $user_id);
    $stmt->execute();
    $stmt->close();

    // إدخال بيانات السحب في جدول withdrawals
    $insert_withdrawal_query = "INSERT INTO withdrawals (user_id, phone_number, amount, balance_before, balance_after, status, date) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    
    // سيتم الحصول على رقم الهاتف من مكان آخر أو يمكنك تجاهل هذا الحقل إذا كان غير مطلوب
    $phone_number = ''; // تأكد من تحديد رقم الهاتف المناسب أو تحديث النموذج ليشمل هذا الحقل
    
    $stmt = $conn->prepare($insert_withdrawal_query);
    $stmt->bind_param("isddd", $user_id, $phone_number, $withdraw_amount, $balance_before, $balance_after);
    $stmt->execute();
    $stmt->close();

    echo "تم تقديم طلب السحب بنجاح! سيتم معالجة الطلب في غضون 72 ساعة.";
} else {
    echo "الرصيد غير كافٍ لإتمام عملية السحب.";
}

$conn->close();
?>
