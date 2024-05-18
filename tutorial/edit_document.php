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
    $file_type = pathinfo($document['file_name'], PATHINFO_EXTENSION);
    if ($file_type == 'pdf') {
        $pdf_data = $_POST['pdf_data'];
        file_put_contents('uploads/' . $document['file_name'], base64_decode($pdf_data));
    } else if ($file_type == 'txt') {
        $text_data = $_POST['text_data'];
        file_put_contents('uploads/' . $document['file_name'], $text_data);
    }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf-lib/1.17.1/pdf-lib.min.js"></script>
    <script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@pdf-lib/fontkit"></script>
    <title>Edit Document</title>
    <style>
        .pdf-canvas, .text-area {
            border: 1px solid #ddd;
            margin-top: 20px;
            width: 100%;
        }
        .text-area {
            height: 500px;
        }
    </style>
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
            <h2>Edit Document</h2>
            <form id="edit-form" action="edit_document.php?id=<?php echo $document_id; ?>" method="post">
                <?php if (pathinfo($document['file_name'], PATHINFO_EXTENSION) == 'pdf'): ?>
                    <div class="field">
                        <label for="pdf_data">PDF Data</label>
                        <textarea id="pdf_data" name="pdf_data" style="display:none;"></textarea>
                    </div>
                    <canvas id="pdf-canvas" class="pdf-canvas"></canvas>
                <?php elseif (pathinfo($document['file_name'], PATHINFO_EXTENSION) == 'txt'): ?>
                    <div class="field">
                        <label for="text_data">Text Data</label>
                        <textarea id="text_data" name="text_data" class="text-area"></textarea>
                    </div>
                <?php endif; ?>
                <div class="field">
                    <button type="submit" class="btn">Save Document</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        <?php if (pathinfo($document['file_name'], PATHINFO_EXTENSION) == 'pdf'): ?>
        const url = 'uploads/<?php echo $document['file_name']; ?>';

        const loadPdf = async (url) => {
            const loadingTask = pdfjsLib.getDocument(url);
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);
            const scale = 1.5;
            const viewport = page.getViewport({ scale });

            const canvas = document.getElementById('pdf-canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            await page.render(renderContext).promise;

            const pdfDoc = await PDFLib.PDFDocument.load(await (await fetch(url)).arrayBuffer());
            const firstPage = pdfDoc.getPage(0);
            const { width, height } = firstPage.getSize();

            let isAddingText = false;

            canvas.addEventListener('dblclick', async (event) => {
                if (isAddingText) return;

                const x = event.offsetX;
                const y = event.offsetY;
                const text = prompt('Enter text:');
                if (text !== null) {
                    context.font = '16px Arial';
                    context.fillText(text, x, y);

                    isAddingText = true;

                    pdfDoc.registerFontkit(window.fontkit);
                    const fontBytes = await fetch('fonts/Roboto-Regular.ttf').then(res => res.arrayBuffer());
                    const customFont = await pdfDoc.embedFont(fontBytes);

                    firstPage.drawText(text, {
                        x: x / scale,
                        y: height - y / scale,
                        size: 16,
                        font: customFont
                    });

                    isAddingText = false;
                }
            });

            document.getElementById('edit-form').addEventListener('submit', async (event) => {
                const pdfBytes = await pdfDoc.saveAsBase64({ dataUri: false });
                document.getElementById('pdf_data').value = pdfBytes;
            });
        };

        loadPdf(url);
        <?php elseif (pathinfo($document['file_name'], PATHINFO_EXTENSION) == 'txt'): ?>
        const textArea = document.getElementById('text_data');
        fetch('uploads/<?php echo $document['file_name']; ?>')
            .then(response => response.text())
            .then(data => {
                textArea.value = data;
            });
        <?php endif; ?>
    </script>
</body>
</html>
