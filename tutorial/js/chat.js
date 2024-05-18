// chat.js
const output = document.getElementById('output');
const messageInput = document.getElementById('message');

function sendMessage() {
    const message = messageInput.value;
    if (message.trim() === '') {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/send_message.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            const response = JSON.parse(this.responseText);
            output.innerHTML += `<p><strong>User:</strong> ${message}</p>`;
            output.innerHTML += `<p><strong>Consultant:</strong> ${response.response}</p>`;
            messageInput.value = '';
        }
    }
    xhr.send("message=" + encodeURIComponent(message));
}

// Adaugă evenimentul pentru tasta Enter
messageInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});
