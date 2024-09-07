<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $phone_number = $_POST['phone_number'];
    $payment_method = $_POST['payment_method'];
    $status = $_POST['status'];

    $sql = "INSERT INTO payment_requests (user_id, amount, phone_number, payment_method, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idsss", $_SESSION['user_id'], $amount, $phone_number, $payment_method, $status);

    if ($stmt->execute()) {
        echo "تم تسجيل الطلب بنجاح.";
    } else {
        echo "حدث خطأ أثناء تسجيل الطلب.";
    }
    $stmt->close();
    $conn->close();
}
?>
