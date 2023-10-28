const form = document.querySelector(".typing-area");
const incoming_id = form.querySelector(".incoming_id").value;
const inputField = form.querySelector(".input-field");
const inputFile = form.querySelector(".input-file");
const sendBtn = form.querySelector("button");
const chatBox = document.querySelector(".chat-box");

form.onsubmit = (e) => {
    e.preventDefault();
};

inputField.addEventListener("input", checkInput);
inputFile.addEventListener("input", checkInput);

function checkInput() {
    if (inputField.value.trim() !== "" || inputFile.value.trim() !== "") {
        sendBtn.classList.add("active");
    } else {
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = () => {
    if (inputField.value.trim() === "" && inputFile.value.trim() === "") {
        return; // Don't send if there is no message or file
    }

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                inputField.value = "";
                inputFile.value = "";
                sendBtn.classList.remove("active");
                scrollToBottom();
            }
        }
    };

    const formData = new FormData(form);
    xhr.send(formData);
};

chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
};

chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
};

setInterval(() => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                const data = xhr.response;
                chatBox.innerHTML = data;
                if (!chatBox.classList.contains("active")) {
                    scrollToBottom();
                }
            }
        }
    };
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id=" + incoming_id);
}, 500);

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}
