
import '../../styles/base/login.scss';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min';


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