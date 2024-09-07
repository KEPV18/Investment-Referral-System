<?php
include 'config.php'; // Ensure config.php includes the $conn variable

// Check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's investments from the database
$query = "SELECT ip.name AS plan_name, ip.min_investment, ip.return_percentage, i.final_return, i.daily_profit, i.purchase_date, i.maturity_date, i.amount
          FROM investments i
          JOIN investment_plans ip ON i.plan_id = ip.id
          WHERE i.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$investments = [];
while ($row = $result->fetch_assoc()) {
    $investments[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Investments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        .footer {
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            background-color: #fff;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <div class="container mx-auto p-4">
        <?php include 'new_header.php'; ?>

        <h1 class="text-3xl font-semibold mb-6">My Investments</h1>

        <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-3 px-6 text-left">Plan Name</th>
                    <th class="py-3 px-6 text-left">Minimum Investment</th>
                    <th class="py-3 px-6 text-left">Return Percentage</th>
                    <th class="py-3 px-6 text-left">Final Return</th>
                    <th class="py-3 px-6 text-left">Daily Profit</th>
                    <th class="py-3 px-6 text-left">Purchase Date</th>
                    <th class="py-3 px-6 text-left">Maturity Date</th>
                    <th class="py-3 px-6 text-left">Investment Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($investments as $investment): ?>
                <tr>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['plan_name']); ?></td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['min_investment']); ?> EGP</td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['return_percentage']); ?>%</td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['final_return']); ?> EGP</td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['daily_profit']); ?> EGP</td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['purchase_date']); ?></td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['maturity_date']); ?></td>
                    <td class="py-3 px-6 border-b"><?php echo htmlspecialchars($investment['amount']); ?> EGP</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer text-gray-600 text-center mt-6 py-4 border-t">
            © 2024 Alibaba Investments. All rights reserved.
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
