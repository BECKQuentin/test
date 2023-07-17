
import '../../styles/security/login.scss';


let passwordInput = document.getElementById('password');

document.querySelector('#login_password_show').addEventListener('click', () => {
    togglePasswordType();
})

function togglePasswordType() {
    // Si le type actuel est "password", le changer en "text"
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    }
    // Sinon, le changer en "password"
    else {
        passwordInput.type = "password";
    }
}