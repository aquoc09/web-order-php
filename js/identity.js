function verifyLogin(event) {
    event.preventDefault();

    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value.trim();

    fetch('./modules/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}`
    })
    .then(res => res.json())
    .then(data => {
        const modalTitle = document.getElementById("title-text-modal");
        modalTitle.textContent = data.message;
        document.getElementById("btnModal").click(); // mở modal thông báo
        if (data.success) {
            setTimeout(() => {
                window.location.href = 'index.php'; // chuyển hướng sau login
            }, 1500);
        }
    })
    .catch(err => console.error(err));
}

function showPassword() {
    var togglePassword = document.getElementById('togglePassword');
    var password = document.getElementById('password');

    var type = password.getAttribute('type');

    if(type === 'password'){
        type = 'text';
        password.setAttribute('type',type);
        togglePassword.innerHTML = '    Ẩn mật khẩu'
    }else{
        type = 'password';
        password.setAttribute('type',type);
        togglePassword.innerHTML = '    Hiển thị mật khẩu'
    }
}

function confirmPassword() {
    var toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    var confirmPassword = document.getElementById('confirmPassword');

    var type = confirmPassword.getAttribute('type');

    if(type === 'password'){
        type = 'text';
        confirmPassword.setAttribute('type',type);
        toggleConfirmPassword.innerHTML = '    Ẩn mật khẩu'
    }else{
        type = 'password';
        confirmPassword.setAttribute('type',type);
        toggleConfirmPassword.innerHTML = '    Hiển thị mật khẩu'
    }
}
