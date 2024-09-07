<?php
include 'config2.php'; // تأكد من أن ملف config.php يحتوي على تعريف $conn

// تحقق من تسجيل الدخول
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// الحصول على بيانات النموذج
$name = $_POST['name'];
$min_investment = $_POST['min_investment'];
$return_percentage = $_POST['return_percentage'];
$duration = $_POST['duration'];
$details = $_POST['details'];

// التحقق من صحة البيانات
if (empty($name) || empty($min_investment) || empty($return_percentage) || empty($duration) || empty($details)) {
    die("يرجى ملء جميع الحقول.");
}

// إدخال البيانات في قاعدة البيانات
$query = "INSERT INTO investment_plans (name, min_investment, return_percentage, duration, details) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sddis", $name, $min_investment, $return_percentage, $duration, $details);

if ($stmt->execute()) {
    echo "تم إضافة الباقة الاستثمارية بنجاح!";
} else {
    echo "حدث خطأ أثناء إضافة الباقة: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
