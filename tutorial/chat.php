<?php
session_start();
include("php/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id'];

// Gestionăm trimiterea mesajelor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($con, $_POST['message']);
    $response = '';

    // Verificăm dacă există un răspuns prestabilit
    $response_query = "SELECT response FROM responses WHERE '$message' LIKE CONCAT('%', keyword, '%') LIMIT 1";
    $response_result = mysqli_query($con, $response_query);
    $response_row = mysqli_fetch_assoc($response_result);

    if ($response_row) {
        $response = $response_row['response'];
    }

    header("Content-Type: application/json");
    echo json_encode(["status" => "success", "response" => $response]);
    exit();
}

// Gestionăm salvarea conversației
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_conversation'])) {
    $conversation = mysqli_real_escape_string($con, $_POST['conversation']);
    $query = "INSERT INTO chats (user_id, message, sender) VALUES ('$user_id', '$conversation', 'conversation')";
    mysqli_query($con, $query);

    header("Content-Type: application/json");
    echo json_encode(["status" => "success"]);
    exit();
}

// Gestionăm obținerea mesajelor
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_messages'])) {
    $query = "SELECT * FROM chats WHERE user_id = '$user_id' ORDER BY timestamp ASC";
    $result = mysqli_query($con, $query);

    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row['message'];
    }

    header("Content-Type: application/json");
    echo json_encode(["status" => "success", "messages" => $messages]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Real-Time Chat Application</title>
    <style>
        /* Global Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        /* Chat Window Styles */
        .chat-window {
            width: 500px;
            background-color: #f0f0f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .chat-area {
            height: 400px;
            overflow-y: scroll;
            padding: 20px;
        }

        .chat-messages {
            display: flex;
            flex-direction: column;
        }

        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }

        .message.user {
            background-color: #e1ffc7;
            align-self: flex-start;
        }

        .message.system {
            background-color: #c7e1ff;
            align-self: flex-end;
        }

        .user-input {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: #fff;
        }

        #message-input {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #send-button, #save-button {
            padding: 8px 16px;
            margin-left: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
        }

        #send-button:hover, #save-button:hover {
            background-color: #0056b3;
        }
    </style>
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
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="chat-window">
            <div class="chat-area">
                <div class="chat-messages">
                    <!-- Chat Messages -->
                </div>
            </div>
            <div class="user-input">
                <input type="text" id="message-input" placeholder="Type your message...">
                <button id="send-button">Send</button>
            </div>
        </div>
        <button id="save-button">Save Conversation</button>
    </div>

    <script>
        // Function to handle sending a message
        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            if (message !== '') {
                const chatMessages = document.querySelector('.chat-messages');
                const messageElement = document.createElement('div');
                messageElement.classList.add('message', 'user');
                messageElement.textContent = message;
                chatMessages.appendChild(messageElement);
                messageInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Send message to the server
                fetch('chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `message=${message}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.response) {
                        const responseElement = document.createElement('div');
                        responseElement.classList.add('message', 'system');
                        responseElement.textContent = data.response;
                        chatMessages.appendChild(responseElement);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });
            }
        }

        // Function to save the conversation
        function saveConversation() {
            const chatMessages = document.querySelectorAll('.chat-messages .message');
            let conversation = '';
            chatMessages.forEach(messageElement => {
                conversation += messageElement.textContent + '\n';
            });

            fetch('chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `save_conversation=true&conversation=${encodeURIComponent(conversation)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Conversation saved successfully.');
                }
            });
        }

        // Function to load messages
        function loadMessages() {
            fetch('chat.php?fetch_messages=true')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const chatMessages = document.querySelector('.chat-messages');
                        chatMessages.innerHTML = '';
                        data.messages.forEach(message => {
                            const messageElement = document.createElement('div');
                            messageElement.classList.add('message');
                            messageElement.textContent = message;
                            chatMessages.appendChild(messageElement);
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                });
        }

        // Event listener
        document.getElementById('send-button').addEventListener('click', sendMessage);
        document.getElementById('save-button').addEventListener('click', saveConversation);
        document.getElementById('message-input').addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
            }
        });

        // Load messages when the page loads
        window.onload = loadMessages;
    </script>
</body>
</html>
