<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_number = '01552729859'; // رقم فودافون كاش

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $phone_number = $_POST['phone_number'];

    if ($payment_method == 'vodafone_cash') {
        $message = "يرجى تحويل المبلغ {$amount} إلى الرقم: $payment_number. الوقت المحدد للتحويل <span id='timer'>15:00</span>.";
        $_SESSION['transaction_start'] = time();
        $_SESSION['amount'] = $amount; // حفظ المبلغ في الجلسة
        $_SESSION['phone_number'] = $phone_number; // حفظ رقم الهاتف في الجلسة
        $_SESSION['payment_method'] = $payment_method; // حفظ طريقة الدفع في الجلسة
    } else {
        $error = "طريقة الدفع غير مدعومة.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة الدفع</title>
    <link rel="stylesheet" href="payment_gateway.css">
    <style>
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        input[type="number"],
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="number"]:focus,
        input[type="text"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        .buttona {
            display: inline-block;
            padding: 12px 25px;
            margin-top: 15px;
            font-size: 18px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .buttona:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .payment-info {
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #eaeaea;
            padding-top: 20px;
        }

        .payment-info p {
            margin-bottom: 10px;
            color: #333;
        }

        .timer {
            font-size: 24px;
            font-weight: bold;
            color: #d9534f;
        }

        .loading {
            display: none;
            text-align: center;
            font-size: 18px;
            color: #d9534f;
            margin-top: 20px;
        }

        .loading-circle {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'new_header.php'; ?>

    <div class="container">
        <h2>طلب إضافة رصيد</h2>
        <?php
        if (isset($message)) echo "<p class='success'>$message</p>";
        if (isset($error)) echo "<p class='error'>$error</p>";
        ?>
        <?php if (!isset($message)): ?>
        <form action="" method="POST">
            <label for="amount">المبلغ:</label>
            <input type="number" id="amount" name="amount" required min="300">

            <label for="phone_number">رقم الهاتف للتحويل:</label>
            <input type="text" id="phone_number" name="phone_number" required>

            <label for="payment_method">طريقة الدفع:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="vodafone_cash">فودافون كاش</option>
                <option value="bank_transfer">تحويل بنكي</option>
            </select>

            <button type="submit">بدء العملية</button>
        </form>
        <?php else: ?>
        <div class="payment-info">
            <p>يرجى تحويل المبلغ إلى الرقم التالي:</p>
            <p><?php echo $payment_number; ?> <button class="buttona" onclick="copyNumber()">نسخ الرقم</button></p>
            <button class="buttona" id="confirm-button" onclick="confirmTransfer()">تم التحويل</button>
            <p id="timer">15:00</p>
        </div>
        <p id="status-message"></p>
        <div id="loading" class="loading">جاري التحقق من التحويل...</div>
        <script>
            let attempts = 0;
            const maxAttempts = 20;
            const waitTime = 15 * 60 * 1000; // 15 minutes
            const startTime = <?php echo isset($_SESSION['transaction_start']) ? $_SESSION['transaction_start'] * 1000 : 0; ?>;
            const statusMessage = document.getElementById('status-message');
            const timerElement = document.getElementById('timer');
            const loadingElement = document.getElementById('loading');
            const confirmButton = document.getElementById('confirm-button');

            function updateTimer() {
                const elapsedTime = Date.now() - startTime;
                const remainingTime = waitTime - elapsedTime;
                if (remainingTime <= 0) {
                    timerElement.textContent = "انتهى الوقت";
                    return;
                }
                const minutes = Math.floor((remainingTime % (1000 * 3600)) / (1000 * 60));
                const seconds = Math.floor((remainingTime % (1000 * 60)) / 1000);
                timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            setInterval(updateTimer, 1000);

            function copyNumber() {
                navigator.clipboard.writeText('<?php echo $payment_number; ?>').then(() => {
                    alert("تم نسخ الرقم بنجاح.");
                });
            }

            function confirmTransfer() {
                attempts++;
                loadingElement.classList.remove('hidden');
                confirmButton.disabled = true;
                // إضافة دائرة التحميل
                const loadingCircle = document.createElement('div');
                loadingCircle.className = 'loading-circle'; // إضافة فئة CSS للدائرة
                loadingElement.appendChild(loadingCircle);
                statusMessage.textContent = "جاري التحقق من التحويل..."; // إضافة نص التحميل

                setTimeout(() => {
                    // إزالة دائرة التحميل بعد 2 ثانية
                    loadingElement.removeChild(loadingCircle);
                    if (attempts >= maxAttempts || Date.now() - startTime >= waitTime) {
                        statusMessage.textContent = "طلب الإيداع معلق. سيتم التواصل معك من قبل خدمة العملاء أو سيتم إضافة الرصيد خلال 24 ساعة.";
                        // عرض الرسالة لمدة 7 ثوانٍ قبل التحويل
                        setTimeout(() => {
                            // Send the data to the database
                            fetch('process_request.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    'amount': '<?php echo $_SESSION['amount']; ?>',
                                    'phone_number': '<?php echo $_SESSION['phone_number']; ?>',
                                    'payment_method': '<?php echo $_SESSION['payment_method']; ?>',
                                    'status': 'pending'
                                })
                            }).then(response => {
                                window.location.href = "dashboard.php"; // تحويل المستخدم إلى صفحة لوحة البيانات
                            });
                        }, 7000); // تأخير 7 ثوانٍ
                    } else {
                        statusMessage.textContent = "لم نستقبل التحويل بعد.";
                        loadingElement.classList.add('hidden');
                        confirmButton.disabled = false;
                    }
                }, 2000); // Delay to show loading effect
            }
        </script>
        <?php endif; ?>
    </div>
</body>
</html>