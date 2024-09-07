<?php
session_start(); // بدء الجلسة

// التحقق مما إذا كان المستخدم قد سجل الدخول
if (isset($_SESSION['user_id'])) {
    // إنهاء الجلسة
    session_unset(); // إزالة جميع بيانات الجلسة
    session_destroy(); // تدمير الجلسة

    // إعادة التوجيه إلى صفحة تسجيل الدخول أو الصفحة الرئيسية
    header("Location: login.php");
    exit();
} else {
    // إذا لم يكن هناك جلسة مفتوحة، إعادة التوجيه إلى الصفحة الرئيسية
    header("Location: index.php");
    exit();
}
?>
