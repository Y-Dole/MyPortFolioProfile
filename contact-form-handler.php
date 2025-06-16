<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfolio_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$full_name = htmlspecialchars($_POST['full-name']);
$email = htmlspecialchars($_POST['email']);
$message = htmlspecialchars($_POST['message']);

$success = false;

if ($stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, message) VALUES (?, ?, ?)")) {
    $stmt->bind_param("sss", $full_name, $email, $message);

    if ($stmt->execute()) {
        $success = true;

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'yourgmail@gmail.com'; // 🔁 your Gmail
            $mail->Password = 'your-app-password';    // 🔁 App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom('yourgmail@gmail.com', 'Portfolio Website');
            $mail->addAddress('abdulhamidmuhammad0147@gmail.com'); // Your inbox

            // Content
            $mail->isHTML(true);
            $mail->Subject = '📬 New Message from Portfolio Site';
            $mail->Body = "
                <strong>Full Name:</strong> {$full_name}<br>
                <strong>Email:</strong> {$email}<br>
                <strong>Message:</strong><br>{$message}
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email failed: {$mail->ErrorInfo}");
        }
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thank You | Amd-Sec</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #e3f2fd, #ffffff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .thank-you-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            animation: fadeIn 0.7s ease-in-out;
        }

        .thank-you-box h1 {
            font-size: 2rem;
            font-weight: 600;
        }

        .thank-you-box p {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .success-icon {
            font-size: 50px;
            color: #198754;
            margin-bottom: 20px;
        }

        .error-icon {
            font-size: 50px;
            color: #dc3545;
            margin-bottom: 20px;
        }

        .btn-custom {
            padding: 10px 25px;
            font-weight: 500;
            font-size: 1rem;
            border-radius: 30px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

    <div class="thank-you-box">
        <?php if ($success): ?>
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1>Thank You!</h1>
            <p>Your message has been successfully sent and emailed. I’ll respond shortly.</p>
            <a href="contact.html" class="btn btn-success btn-custom">Back to Contact</a>
        <?php else: ?>
            <div class="error-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <h1>Oops!</h1>
            <p>Something went wrong while sending your message. Please try again later.</p>
            <a href="contact.html" class="btn btn-outline-danger btn-custom">Try Again</a>
        <?php endif; ?>
    </div>

</body>
</html>
