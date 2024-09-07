// نسخ الرابط إلى الحافظة
function copyLink() {
    var copyText = document.getElementById("referral-link");
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices

    navigator.clipboard.writeText(copyText.value);
    alert("تم نسخ الرابط إلى الحافظة!");
}

// عرض وإخفاء نافذة إضافة الرصيد
function showAddBalanceModal() {
    document.getElementById("addBalanceModal").style.display = "block";
}

function closeAddBalanceModal() {
    document.getElementById("addBalanceModal").style.display = "none";
}

// عرض وإخفاء نافذة سحب الرصيد
function showWithdrawBalanceModal() {
    document.getElementById("withdrawBalanceModal").style.display = "block";
}

function closeWithdrawBalanceModal() {
    document.getElementById("withdrawBalanceModal").style.display = "none";
}

// إغلاق النافذة عند النقر خارج المحتوى
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        closeAddBalanceModal();
        closeWithdrawBalanceModal();
    }
}

// التحقق من قيمة السحب ووقت السحب
function validateWithdrawal() {
    var amount = document.getElementById('withdraw-amount').value;
    if (amount < 300) {
        alert("أقل مبلغ يمكن سحبه هو 300 جنيه.");
        return false;
    }

    var now = new Date();
    var hour = now.getHours();

    if (hour >= 9 && hour < 24) {
        alert("السحب متاح فقط من الساعة 12 مساءً حتى 9 صباحًا.");
        return false;
    }

    return true;
}

// عرض إشعار عند تحميل الصفحة
function showNotification() {
    var notification = document.getElementById('notification');
    if (notification) {
        notification.style.display = 'block';
    } else {
        console.error('العنصر مع ID "notification" غير موجود.');
    }
}

window.onload = function() {
    showNotification();
};
