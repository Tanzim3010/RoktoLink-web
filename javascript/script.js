const signupButton = document.getElementById("signUP");
const signinButton = document.getElementById("signIn");

const signupForm = document.getElementById("signupForm");
const signinForm = document.getElementById("signinForm");

const name = document.getElementById('signupName');
const email = document.getElementById('signupEmail');
const nid = document.getElementById('nid');
const password = document.getElementById('signupPass');


const Reg = document.getElementById('registerButton');
const loginemail = document.getElementById('signinEmail');
const loginpass = document.getElementById('signinPass');


const Login = document.getElementById('Login_button');
signupButton.onclick = function () {

    signinForm.style.display = "none";
    signupForm.style.display = "block";
};


signinButton.onclick = function (event) {
    event.preventDefault();
    signupForm.style.display = "none";
    signinForm.style.display = "block";

};

Reg.onclick = function (event){
    event.preventDefault();

    if (name.value.trim() == "") {
        document.getElementById("nameError").innerText =
            "Please enter your name.";
        name.focus();
        return;
    }
    if (!email.value.trim()) {
        document.getElementById('emailError').innerHTML = 
        "Please fill email";
        email.focus();
        document.getElementById('nameError').innerHTML = "";
        return;
    }

    if (!nid.value.trim()) {
        document.getElementById('nidError').innerHTML =
            "Please fill NID";
        nid.focus();
        document.getElementById('emailError').innerHTML = "";
        return;
    }

    if (!password.value.trim()) {
        document.getElementById('passError').innerHTML =
            "Please fill Password";
        password.focus();
        document.getElementById('nidError').innerHTML = "";
        return;
    }

    alert("Registration Successfull");
    signupForm.style.display = "none";
    signinForm.style.display = "block";

}

Login.onclick = function (event){
    event.preventDefault();
    if (loginemail.value == "") {
        loginemail.focus();
        document.getElementById("inputName").innerText =
            "Please enter your name.";
            return;
        
    }
    if (loginpass.value == "") {
        document.getElementById('inputPass').innerHTML =
            "Please Enter Password";
            loginpass.focus();
        document.getElementById("inputName").innerText =
            "";
      return;
    }
}