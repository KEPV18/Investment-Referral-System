<?php
require 'config.php';

// وظيفة لاختبار التسجيل مع كود إحالة صحيح
function test_registration_with_valid_referral($conn) {
    // إدخال مستخدم جديد ليكون صاحب الإحالة
    $referral_code = bin2hex(random_bytes(5));
    $sql = "INSERT INTO users (name, phone, email, password, wallet_number, balance, referral_code) VALUES ('Test User', '0123456789', 'testuser@example.com', 'password_hash', '1111', 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $referrer_id = $stmt->insert_id;
    $stmt->close();

    // إدخال مستخدم جديد باستخدام كود الإحالة
    $_POST = [
        'name' => 'New User',
        'phone' => '0123456790',
        'email' => 'newuser@example.com',
        'password' => 'newpassword',
        'wallet_number' => '2222'
    ];
    $_GET = ['ref' => $referral_code];

    ob_start();
    include 'register.php';
    ob_end_clean();

    // التحقق من إضافة المستخدم الجديد والإحالة في قاعدة البيانات
    $check_user_sql = "SELECT * FROM users WHERE phone = '0123456790'";
    $user_result = $conn->query($check_user_sql);
    $new_user = $user_result->fetch_assoc();

    $check_referral_sql = "SELECT * FROM referrals WHERE user_id = ?";
    $stmt = $conn->prepare($check_referral_sql);
    $stmt->bind_param("i", $new_user['id']);
    $stmt->execute();
    $referral_result = $stmt->get_result();

    $stmt->close();

    // نتائج الاختبارات
    assert($new_user !== null, 'User registration failed.');
    assert($referral_result->num_rows > 0, 'Referral registration failed.');
    echo "Test registration with valid referral passed.\n";
}

// تنفيذ الاختبارات
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
test_registration_with_valid_referral($conn);
$conn->close();
?>
