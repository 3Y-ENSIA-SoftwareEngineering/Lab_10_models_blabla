<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            color: white;
            text-align: center;
        }
        .button {
            margin-top: 20%;
            padding: 15px 30px;
            font-size: 20px;
            background-color: #864655;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #96635d;
        }
    </style>
</head>
<body>
    <h1>Welcome to Expense Tracker</h1>
    <button class="button" onclick="window.location.href='signup.php'">Get Started</button>
</body>
</html>
