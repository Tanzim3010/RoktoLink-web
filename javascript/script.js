/* =====================================
   SIGN-UP AND SIGN-IN ELEMENTS
===================================== */

const signupButton = document.getElementById("signUP");
const signinButton = document.getElementById("signIn");

const signupForm = document.getElementById("signupForm");
const signinForm = document.getElementById("signinForm");

const signupName = document.getElementById("signupName");
const signupEmail = document.getElementById("signupEmail");
const nid = document.getElementById("nid");
const signupPassword = document.getElementById("signupPass");

const registerButton = document.getElementById("registerButton");

const loginEmail = document.getElementById("signinEmail");
const loginPassword = document.getElementById("signinPass");
const loginButton = document.getElementById("Login_button");


/* =====================================
   SWITCH BETWEEN SIGN-UP AND SIGN-IN
===================================== */

if (signupButton && signinForm && signupForm) {
    signupButton.onclick = function () {
        signinForm.style.display = "none";
        signupForm.style.display = "block";
    };
}

if (signinButton && signupForm && signinForm) {
    signinButton.onclick = function (event) {
        event.preventDefault();

        signupForm.style.display = "none";
        signinForm.style.display = "block";
    };
}


/* =====================================
   REGISTRATION VALIDATION
===================================== */

if (
    registerButton &&
    signupName &&
    signupEmail &&
    nid &&
    signupPassword
) {
    registerButton.onclick = function (event) {
        event.preventDefault();

        document.getElementById("nameError").innerText = "";
        document.getElementById("emailError").innerText = "";
        document.getElementById("nidError").innerText = "";
        document.getElementById("passError").innerText = "";

        if (signupName.value.trim() === "") {
            document.getElementById("nameError").innerText =
                "Please enter your name.";

            signupName.focus();
            return;
        }

        if (signupEmail.value.trim() === "") {
            document.getElementById("emailError").innerText =
                "Please fill email.";

            signupEmail.focus();
            return;
        }

        if (nid.value.trim() === "") {
            document.getElementById("nidError").innerText =
                "Please fill NID.";

            nid.focus();
            return;
        }

        if (nid.value.trim().length !== 10) {
            document.getElementById("nidError").innerText =
                "NID must be exactly 10 digits.";

            nid.focus();
            return;
        }

        if (signupPassword.value.trim() === "") {
            document.getElementById("passError").innerText =
                "Please fill password.";

            signupPassword.focus();
            return;
        }

        alert("Registration Successful");

        signupForm.style.display = "none";
        signinForm.style.display = "block";
    };
}


/* =====================================
   LOGIN VALIDATION
===================================== */

if (loginButton && loginEmail && loginPassword) {
    loginButton.onclick = function (event) {
        event.preventDefault();

        document.getElementById("inputName").innerText = "";
        document.getElementById("inputPass").innerText = "";

        if (loginEmail.value.trim() === "") {
            document.getElementById("inputName").innerText =
                "Please enter your email.";

            loginEmail.focus();
            return;
        }

        if (loginPassword.value.trim() === "") {
            document.getElementById("inputPass").innerText =
                "Please enter password.";

            loginPassword.focus();
            return;
        }

        alert("Login Successful");

        window.location.href = "../Html/dashboard.html";
    };
}


/* =====================================
   DASHBOARD ELEMENTS
===================================== */

const profileButton = document.getElementById("profile");
const profileContainer = document.getElementById("profileForm");

const createProfileForm =
    document.getElementById("createProfileForm");

const profilePicture =
    document.getElementById("profilePicture");

const previewImage =
    document.getElementById("previewImage");

const imageText =
    document.getElementById("imageText");

const profileCard =
    document.getElementById("profileCard");

const cardImage =
    document.getElementById("cardImage");

const defaultProfileImage =
    document.getElementById("defaultProfileImage");

const editProfileButton =
    document.getElementById("editProfile");

let selectedImage = "";


/* =====================================
   SHOW PROFILE FORM
===================================== */

if (profileButton && profileContainer) {
    profileButton.onclick = function () {
        profileContainer.style.display = "flex";
        profileButton.style.display = "none";

        profileContainer.scrollIntoView({
            behavior: "smooth"
        });
    };
}


/* =====================================
   PROFILE IMAGE PREVIEW
===================================== */

if (profilePicture && previewImage) {
    profilePicture.onchange = function () {
        const file = profilePicture.files[0];

        if (!file) {
            selectedImage = "";

            previewImage.src = "";
            previewImage.style.display = "none";

            if (imageText) {
                imageText.style.display = "block";
            }

            return;
        }

        if (!file.type.startsWith("image/")) {
            alert("Please select an image.");

            profilePicture.value = "";
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            selectedImage = event.target.result;

            previewImage.src = selectedImage;
            previewImage.style.display = "block";

            if (imageText) {
                imageText.style.display = "none";
            }
        };

        reader.readAsDataURL(file);
    };
}


/* =====================================
   CREATE AND SHOW PROFILE CARD
===================================== */

if (createProfileForm && profileCard) {
    createProfileForm.onsubmit = function (event) {
        event.preventDefault();

        const fullName =
            document.getElementById("fullName").value.trim();

        const bloodGroup =
            document.getElementById("bloodGroup").value;

        const phone =
            document.getElementById("phone").value.trim();

        const profileEmail =
            document.getElementById("profileEmail").value.trim();

        const dob =
            document.getElementById("dob").value;

        const address =
            document.getElementById("address").value.trim();

        const lastDonation =
            document.getElementById("lastDonation").value;

        const availability =
            document.getElementById("availability").value;


        document.getElementById("cardName").innerText =
            fullName;

        document.getElementById("cardBlood").innerText =
            bloodGroup;

        document.getElementById("cardPhone").innerText =
            phone;

        document.getElementById("cardEmail").innerText =
            profileEmail;

        document.getElementById("cardDob").innerText =
            formatDate(dob);

        document.getElementById("cardAddress").innerText =
            address;

        document.getElementById("cardDonation").innerText =
            lastDonation
                ? formatDate(lastDonation)
                : "Not donated yet";


        const cardAvailability =
            document.getElementById("cardAvailability");

        cardAvailability.innerText = availability;

        if (availability === "Yes") {
            cardAvailability.classList.add("available");
            cardAvailability.classList.remove("not-available");
        } else {
            cardAvailability.classList.add("not-available");
            cardAvailability.classList.remove("available");
        }


        /* Show image inside card circle */

        if (selectedImage !== "" && cardImage) {
            cardImage.src = selectedImage;
            cardImage.style.display = "block";

            if (defaultProfileImage) {
                defaultProfileImage.style.display = "none";
            }
        } else {
            if (cardImage) {
                cardImage.style.display = "none";
            }

            if (defaultProfileImage) {
                defaultProfileImage.style.display = "flex";
            }
        }


        profileCard.style.display = "block";

        alert("Profile created successfully!");
    };
}


/* =====================================
   EDIT PROFILE
===================================== */

if (editProfileButton && profileCard) {
    editProfileButton.onclick = function () {
        profileCard.style.display = "none";

        document.getElementById("fullName").focus();
    };
}


/* =====================================
   FORMAT DATE
===================================== */

function formatDate(dateValue) {
    if (!dateValue) {
        return "";
    }

    const parts = dateValue.split("-");

    return parts[2] + "/" + parts[1] + "/" + parts[0];
}


/* =====================================
   LOGOUT FUNCTIONALITY
===================================== */

const logoutBtn = document.getElementById("logoutBtn");

if (logoutBtn) {
    logoutBtn.onclick = function () {
        if (confirm("Are you sure you want to logout?")) {
            alert("Logged out successfully!");
            window.location.href = "../Html/index.html";
        }
    };
}







