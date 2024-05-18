<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_documents.php");
    exit();
}

$document_id = $_GET['id'];
$query = "SELECT * FROM documents WHERE id = '$document_id'";
$result = mysqli_query($con, $query);
$document = mysqli_fetch_assoc($result);

if (!$document) {
    header("Location: manage_documents.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $signature = $_POST['signature']; // Vom primi semnătura ca un base64 string
    $signature = str_replace('data:image/png;base64,', '', $signature);
    $signature = base64_decode($signature);

    // Salvăm semnătura ca fișier temporar
    $signature_file = 'temp_signature.png';
    file_put_contents($signature_file, $signature);

    // Generăm documentul PDF cu semnătura
    require('lib/fpdf/fpdf.php');

    class PDF extends FPDF {
        function Header() {
            // Putem adăuga un antet aici
        }

        function Footer() {
            // Putem adăuga un subsol aici
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->Image('uploads/' . $document['file_name'], 10, 10, 180);
    $pdf->Image($signature_file, 10, 250, 50); // Afișăm semnătura în partea de jos a paginii
    $pdf->Output('F', 'uploads/' . $document['file_name']);

    // Ștergem fișierul temporar
    unlink($signature_file);

    header("Location: manage_documents.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/documents.css">
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <title>Sign Document</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="change_profile.php">Change Profile</a>
            <a href="upload_document.php">Upload Document</a>
            <a href="manage_documents.php">Manage Documents</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box">
            <h2>Sign Document</h2>
            <?php if (isset($error_message)): ?>
                <div class="message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form id="sign-form" action="sign_document.php?id=<?php echo $document_id; ?>" method="post">
                <div class="field">
                    <label for="signature">Signature</label>
                    <canvas id="signature-pad" width="400" height="200" style="border: 1px solid #ddd;"></canvas>
                    <input type="hidden" name="signature" id="signature">
                </div>
                <div class="field">
                    <button type="button" class="btn" id="clear">Clear</button>
                    <button type="submit" class="btn">Sign Document</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        var canvas = document.getElementById('signature-pad');
        var signaturePad = new SignaturePad(canvas);

        document.getElementById('clear').addEventListener('click', function () {
            signaturePad.clear();
        });

        document.getElementById('sign-form').addEventListener('submit', function (event) {
            if (signaturePad.isEmpty()) {
                alert("Please provide a signature first.");
                event.preventDefault();
            } else {
                var dataURL = signaturePad.toDataURL();
                document.getElementById('signature').value = dataURL;
            }
        });
    </script>
</body>
</html>
