<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// Database connection (adjust if your MySQL credentials differ)
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "contact_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    // Redirect with friendly error (avoid exposing DB details)
    header("Location: index.html?status=error&msg=" . urlencode('Cannot connect to database. Please run create_databases.php or import db_init.sql'));
    exit;
}

// Get form data
$name    = $_POST['name'] ?? '';
$email   = $_POST['email'] ?? '';
$phone   = $_POST['phone'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

// Save to database (prepared statement)
$stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);

$execOk = $stmt->execute();
$stmt->close(); // Keep this line to close the statement

if (!$execOk) {
    header("Location: index.html?status=error&msg=" . urlencode('Failed to save message.'));
    exit;
}

// Send email
$mail = new PHPMailer(true);

try {
    // $mail->SMTPDebug = 2; // Uncomment for debugging
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'kyllemitra15@gmail.com';
    $mail->Password   = 'uhnvrvdiojpivjac'; // Gmail App Password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->Timeout    = 15; // Prevents long waiting

    // Set sender & recipient
    $mail->setFrom('kyllemitra15@gmail.com', $name);
    $mail->addReplyTo($email, $name);
    $mail->addAddress('kyllemitra15@gmail.com');

    // Email content
    $mail->Subject = $subject;
    $mail->Body    = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";

    $mail->send();

    // Redirect with success status
    header("Location: index.html?status=success");
    exit;

} catch (Exception $e) {
    // Redirect with error status
    header("Location: index.html?status=error&msg=" . urlencode($mail->ErrorInfo));
    exit;
}

$conn->close();
?>
