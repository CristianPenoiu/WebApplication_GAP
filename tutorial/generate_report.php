<?php
session_start();
include("php/config.php");
require('lib/fpdf/fpdf.php');

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obținem mesajele salvate din baza de date
    $query = "SELECT * FROM chats WHERE user_id = '$user_id' ORDER BY timestamp ASC";
    $result = mysqli_query($con, $query);

    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row['message'];
    }

    class PDF extends FPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, 'Chat Report', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        }

        function ChapterTitle($num, $label)
        {
            $this->SetFont('Arial', 'B', 12);
            $this->Cell(0, 10, "Conversation {$num}: {$label}", 0, 1);
            $this->Ln(5);
        }

        function ChapterBody($body)
        {
            $this->SetFont('Arial', '', 12);
            $this->MultiCell(0, 10, $body);
            $this->Ln();
        }

        function PrintChapter($num, $title, $body)
        {
            $this->AddPage();
            $this->ChapterTitle($num, $title);
            $this->ChapterBody($body);
        }
    }

    $pdf = new PDF();
    $pdf->SetTitle('Chat Report');

    foreach ($messages as $key => $message) {
        $pdf->PrintChapter($key + 1, 'Conversation', $message);
    }

    $pdf->Output('D', 'Chat_Report.pdf');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Generate Report</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="user_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="edit_user.php">Change Profile</a>
            <a href="request_consultation.php">Consultation Schedule</a>
            <a href="my_consultation.php">My Consultations</a>
            <a href="chat.php">Chat</a>
            <a href="generate_report.php">Generate Report</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box">
            <h2>Generate Report</h2>
            <p>Click the button below to generate a report of your chat conversations.</p>
            <form action="generate_report.php" method="POST">
                <button type="submit" class="btn">Generate Report</button>
            </form>
        </div>
    </div>
</body>
</html>
