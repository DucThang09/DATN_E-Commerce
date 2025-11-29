// Animations
const registerButton = document.getElementById("register");
const loginButton = document.getElementById("login");
const container = document.getElementById("container");

registerButton.addEventListener("click", () => {
    container.classList.add("right-panel-active");
    const errorMessage = document.getElementById("error-message");

    // Nếu tồn tại thẻ <small> có id là "error-message"
    if (errorMessage) {
        // Đợi 5 giây sau khi trang tải xong
        setTimeout(() => {
            // Xóa thẻ <small> có id là "error-message"
            errorMessage.remove();
        }, 5000); // 5000 milliseconds = 5 giây
    }
});

loginButton.addEventListener("click", () => {
    container.classList.remove("right-panel-active");
});

// Check Register Error
const registerForm = document.querySelector('.form-register');
const regUsername = document.getElementById('username');
const regEmail = document.getElementById('email');
const regPassword = document.getElementById('password');

// Check required fields
function checkRequired(inputArr) {
    let isRequired = false;
    inputArr.forEach(function (input) {
        if (input.value.trim() === '') {
            showError(input, `${getFieldName(input)} is required`);
            isRequired = true;
        } else {
            showSuccess(input);
        }
    });

    return isRequired;
}

// Check input length
function checkLength(input, min, max) {
    if (input.value.length < min) {
        showError(input, `${getFieldName(input)} must be at least ${min} characters.`);
    } else if (input.value.length > max) {
        showError(input, `${getFieldName(input)} must be less than ${max} characters.`);
    }
}

// Hàm hiển thị lỗi
function showError(input, message) {
    const errorElement = input.nextElementSibling; // Đây bạn cần xác định cách hiển thị lỗi trong DOM, có thể sử dụng nextElementSibling hoặc các cách khác.
    errorElement.textContent = message;
}

// Sử dụng hàm checkDuplicate trong biểu mẫu đăng ký
registerForm.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!checkRequired([regUsername, regEmail, regPassword])) {
        checkLength(regUsername, 4, 20);
        checkEmail(regEmail);
        checkLength(regPassword, 8, 20);

        if (!hasError()) {
            // Nếu không có lỗi, kiểm tra trùng lặp
            checkDuplicate(regUsername.value, regEmail.value);
        }
    }
});

// Hàm kiểm tra email
function checkEmail(input) {
    const emailRegex = /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/;
    if (!emailRegex.test(input.value.trim())) {
        showError(input, "Email is not valid");
        return false;
    }

    return true;
}

// Hàm kiểm tra trùng lặp
async function checkDuplicate(username, email) {
    const requestData = {
        username: username,
        email: email,
    };

    try {
        const response = await fetch('CheckDuplicate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData),
        });

        const data = await response.json();

        if (data.isDuplicate) {
            showError(regEmail, "Email or username is already in use");
        }
    } catch (error) {
        console.error('Lỗi:', error);
    }
}

// Kiểm tra xem có lỗi không
function hasError() {
    const errorElements = document.querySelectorAll('.error-message'); // Điều này phụ thuộc vào cách bạn cài đặt lớp CSS cho thông báo lỗi.

    for (const errorElement of errorElements) {
        if (errorElement.textContent !== '') {
            return true;
        }
    }

    return false;
}

// Làm tương tự cho biểu mẫu đăng nhập nếu cần
