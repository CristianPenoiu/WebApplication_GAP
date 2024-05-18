<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];
$user_role = $_SESSION['role_id'];

// Permitem accesul doar pentru admin (role_id = 2) și employee (role_id = 1)
if ($user_role != 1 && $user_role != 2) {
    echo "Access denied.";
    exit();
}

// Selectăm toate documentele
$query = "SELECT d.*, u.Username FROM documents d JOIN users u ON d.user_id = u.Id";
$result = mysqli_query($con, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/documents.css">
    <link rel="stylesheet" href="style/table_style.css">
    <title>Manage Documents</title>
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
            <h2>Manage Documents</h2>
            <table>
                <thead>
                    <tr>
                        <th>Document Name</th>
                        <th>Category</th>
                        <th>Uploaded By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['document_name']; ?></td>
                            <td><?php echo ucfirst($row['category']); ?></td>
                            <td><?php echo $row['Username']; ?></td>
                            <td>
                                <a href="view_document.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                                <?php if ($row['category'] == 'approval' || $row['category'] == 'modifiable'): ?>
                                    <a href="sign_document.php?id=<?php echo $row['id']; ?>" class="btn">Sign</a>
                                <?php endif; ?>
                                <?php if ($row['category'] == 'modifiable'): ?>
                                    <a href="edit_document.php?id=<?php echo $row['id']; ?>" class="btn">Edit</a>
                                <?php endif; ?>
                                <!-- Add more actions like Delete if needed -->
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
