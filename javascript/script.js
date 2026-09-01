/* =====================================
   SIGN-UP AND SIGN-IN ELEMENTS
===================================== */
const signupButton = document.getElementById("signUP");
const signinButton = document.getElementById("signIn");

const signupForm = document.getElementById("signupForm");
const signinForm = document.getElementById("signinForm");


/* =====================================
   SWITCH BETWEEN SIGN-UP AND SIGN-IN
===================================== */
if (signupButton && signinForm && signupForm) {
    signupButton.onclick = function (event) {
        event.preventDefault();
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
   DASHBOARD ELEMENTS
===================================== */
const profileButton = document.getElementById("profile");
const profileContainer = document.getElementById("profileForm");

const profilePicture = document.getElementById("profilePicture");
const previewImage = document.getElementById("previewImage");
const imageText = document.getElementById("imageText");


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
            previewImage.src = event.target.result;
            previewImage.style.display = "block";

            if (imageText) {
                imageText.style.display = "none";
            }
        };

        reader.readAsDataURL(file);
    };
}


/* =====================================
   LOGOUT FUNCTIONALITY
===================================== */
const logoutBtn = document.getElementById("logoutBtn");

if (logoutBtn) {
    logoutBtn.onclick = function () {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "../php/logout.php";
        }
    };
}