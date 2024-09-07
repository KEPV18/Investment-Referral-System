لوحة تحكم خاصة بالإداريين لإدارة المستخدمين، الباقات، طلبات السحب، والإعلانات.


<?php
include 'config.php';

// تحقق من صلاحيات الإدارة
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// عرض واجهة الإدارة لإدارة المستخدمين والباقات والطلبات
// إضافة منطق لإدارة المستخدمين، الباقات، والطلبات هنا

echo "Welcome to the admin panel.";
?>
