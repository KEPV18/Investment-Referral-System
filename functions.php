
  <?php
// حساب العائدات
function calculateReturn($investment) {
    $returnPercentage = 300; // 300%
    $returnAmount = $investment * ($returnPercentage / 100);
    return $returnAmount;
}

// تحقق من قيمة السحب
function isValidWithdrawal($amount) {
    return $amount >= 300; // أقل سحب 300 جنيه مصري
}

// حساب المكافآت للإحالات
function calculateReferralBonus($investment) {
    $referralBonusPercentage = 25; // 25%
    $referralBonus = $investment * ($referralBonusPercentage / 100);
    return $referralBonus;
}
?>
