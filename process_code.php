<?php
// إعداد الاتصال بقاعدة البيانات
$pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');

// التحقق من وجود الكود في الطلب
if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $userId = 1; // استبدل هذا بقيمة المستخدم الحالي

    // التحقق من صحة الكود
    $stmt = $pdo->prepare("SELECT * FROM reward_codes WHERE code = ? AND used = FALSE");
    $stmt->execute([$code]);
    $rewardCode = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rewardCode) {
        // التحقق من وجود المستخدم
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // تحديث رصيد المستخدم
            $newBalance = $user['balance'] + $rewardCode['value'];
            $stmt = $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $userId]);

            // تحديث حالة الكود
            $stmt = $pdo->prepare("UPDATE reward_codes SET used = TRUE WHERE code = ?");
            $stmt->execute([$code]);

            // تسجيل الكود المستخدم
            $stmt = $pdo->prepare("INSERT INTO used_codes (code, user_id) VALUES (?, ?)");
            $stmt->execute([$code, $userId]);

            echo "تم استبدال الكود بنجاح!";
        } else {
            echo "المستخدم غير موجود.";
        }
    } else {
        echo "الكود غير صالح أو تم استخدامه.";
    }
} else {
    echo "يرجى إدخال كود.";
}
?>
