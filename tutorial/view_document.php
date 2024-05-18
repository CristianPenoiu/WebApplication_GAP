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

$file_path = 'uploads/' . $document['file_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/documents.css">
    <title>View Document</title>
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
            <h2>View Document</h2>
            <p><strong>Document Name:</strong> <?php echo $document['document_name']; ?></p>
            <p><strong>Category:</strong> <?php echo ucfirst($document['category']); ?></p>
            <embed src="<?php echo $file_path; ?>" type="application/pdf" width="100%" height="600px" />
        </div>
    </div>
</body>
</html>
