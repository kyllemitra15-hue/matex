<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "newsletter_db";

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo "<script>alert('Database unavailable. Please run create_databases.php or import db_init.sql'); window.location.href='index.html';</script>";
    exit;
}

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Process subscription
if (isset($_POST['subscribe'])) {
    $email = trim($_POST['email']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email address'); window.history.back();</script>";
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Your SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kyllemitra15@gmail.com'; // Your email
            $mail->Password   = 'plhizapobpesgmpm';   // App password (NOT your Gmail login password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // From & To
            $mail->setFrom('yourgmail@gmail.com', 'Website Newsletter');
            $mail->addAddress('yourgmail@gmail.com', 'Your Name');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'New Newsletter Subscription';
            $mail->Body    = "A new subscriber has joined: <b>$email</b>";

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        echo "<script>alert('Thank you for subscribing!'); window.location.href='index.html';</script>";
    } else {
        // Check duplicate key error (MySQL error code 1062)
        if ($conn->errno === 1062) {
            echo "<script>alert('This email is already subscribed.'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('An error occurred while subscribing.'); window.history.back();</script>";
        }
    }

    $stmt->close();
}

$conn->close();
?>
