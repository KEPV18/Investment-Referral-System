<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="alibaba-group-710666.jpg" type="image/x-icon">
    <title>استبدال المكافآت</title>
    <style>
        .reward-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .reward-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .reward-form button {
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reward-form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<?php include 'new_header.php'; ?>
    <div class="reward-form">
        <h2>استبدال الكود</h2>
        <form action="process_code.php" method="POST">
            <input type="text" name="code" placeholder="أدخل الكود هنا" required>
            <button type="submit">استبدال</button>
        </form>
    </div>
    <?php include 'footer.php'; ?>
<script src="scripts.js"></script>
</body>
</html>
