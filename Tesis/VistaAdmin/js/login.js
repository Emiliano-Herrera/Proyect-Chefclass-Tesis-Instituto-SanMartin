let containerElement = document.querySelector("#container");


const registerBtn = document.querySelector("#register");


const loginBtn = document.querySelector("#login");


registerBtn.addEventListener('click', () => {


    container.classList.add("active");

});

loginBtn.addEventListener('click', () => {

    container.classList.remove("active");
});


/*Para el ojito de la contraseña*/
const togglePassword = (id) => {
    const passwordField = document.getElementById(id);
    const toggleIcon = document.querySelector(`#${id} ~ .input-group-text i`);
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    }
};

document.getElementById('login').addEventListener('click', () => {
    container.classList.remove('right-panel-active');
});

document.getElementById('register').addEventListener('click', () => {
    container.classList.add('right-panel-active');
});
