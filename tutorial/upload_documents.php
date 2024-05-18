<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $document_name = mysqli_real_escape_string($con, $_POST['document_name']);
    $file = $_FILES['document'];

    if ($file['error'] == 0) {
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $file_new_name = uniqid() . '.' . $file_ext;
        $file_dest = 'uploads/' . $file_new_name;

        if (move_uploaded_file($file_tmp, $file_dest)) {
            $query = "INSERT INTO documents (user_id, document_name, file_name, category) 
                      VALUES ('$user_id', '$document_name', '$file_new_name', '$category')";
            mysqli_query($con, $query);
            $message = "Document uploaded successfully.";
        } else {
            $message = "Failed to upload document.";
        }
    } else {
        $message = "There was an error uploading the document.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/documents.css">
    <title>Upload Document</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="user_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="change_profile.php">Change Profile</a>
            <a href="consultation_schedule.php">Consultation Schedule</a>
            <a href="my_consultations.php">My Consultations</a>
            <a href="chat.php">Chat</a>
            <a href="generate_report.php">Generate Report</a>
            <a href="upload_documents.php">Upload Document</a>
            <a href="manage_documents.php">Manage Documents</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box">
            <h2>Upload Document</h2>
            <?php if (isset($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <form action="upload_documents.php" method="post" enctype="multipart/form-data">
                <div class="field">
                    <label for="category">Category</label>
                    <select name="category" id="category" required>
                        <option value="informative">Informative Documents</option>
                        <option value="approval">Documents for Approval</option>
                        <option value="modifiable">Documents that can be Modified for Approval</option>
                    </select>
                </div>
                <div class="field">
                    <label for="document_name">Document Name</label>
                    <input type="text" name="document_name" id="document_name" required>
                </div>
                <div class="field">
                    <label for="document">Select Document</label>
                    <input type="file" name="document" id="document" required>
                </div>
                <div class="field">
                    <button type="submit" class="btn">Upload Document</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
