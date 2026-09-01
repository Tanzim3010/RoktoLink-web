document.addEventListener("DOMContentLoaded", () => {
    // DOM Elements
    const profileBtn = document.getElementById("profile");
    const profileFormContainer = document.getElementById("profileForm");

    // Image Input Elements
    const profilePictureInput = document.getElementById("profilePicture");
    const previewImage = document.getElementById("previewImage");
    const imageText = document.getElementById("imageText");

    // 1. Show Form when clicking "Create Patient Profile" dashboard button
    if (profileBtn && profileFormContainer) {
        profileBtn.addEventListener("click", () => {
            profileFormContainer.style.display = "flex";
            profileBtn.style.display = "none";
            profileFormContainer.scrollIntoView({ behavior: "smooth" });
        });
    }

    // 2. Handle Image Selection & Live Preview
    if (profilePictureInput && previewImage) {
        profilePictureInput.addEventListener("change", (e) => {
            const file = e.target.files[0];

            if (file) {
                // Validate it's actually an image
                if (!file.type.startsWith("image/")) {
                    alert("Please select an image.");
                    profilePictureInput.value = "";
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    previewImage.style.display = "block";
                    if (imageText) imageText.style.display = "none";
                };

                reader.readAsDataURL(file);
            } else {
                resetImagePreview();
            }
        });
    }

    // Reset Image Preview Helper
    function resetImagePreview() {
        previewImage.src = "";
        previewImage.style.display = "none";
        if (imageText) imageText.style.display = "block";
    }
});

/* =====================================
   LOGOUT FUNCTIONALITY
===================================== */
const logoutBtn = document.getElementById("logoutBtn");

if (logoutBtn) {
    logoutBtn.onclick = function () {
        if (confirm("Are you sure you want to logout?")) {
            // Redirect to PHP script to destroy the session securely
            window.location.href = "../php/logout.php";
        }
    };
}