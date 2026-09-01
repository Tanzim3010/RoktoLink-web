document.addEventListener("DOMContentLoaded", () => {
    // DOM Elements
    const profileBtn = document.getElementById("profile");
    const profileFormContainer = document.getElementById("profileForm");
    const createProfileForm = document.getElementById("createProfileForm");
    const formSection = document.querySelector(".form-section");
    const profileCard = document.getElementById("profileCard");
    const editProfileBtn = document.getElementById("editProfile");

    // Image Input Elements
    const profilePictureInput = document.getElementById("profilePicture");
    const previewImage = document.getElementById("previewImage");
    const imageText = document.getElementById("imageText");

    // Card Output Elements
    const cardImage = document.getElementById("cardImage");
    const defaultProfileImage = document.getElementById("defaultProfileImage");
    const cardName = document.getElementById("cardName");
    const cardBlood = document.getElementById("cardBlood");
    const cardPhone = document.getElementById("cardPhone");
    const cardEmail = document.getElementById("cardEmail");
    const cardDob = document.getElementById("cardDob");
    const cardHospital = document.getElementById("cardHospital");
    const cardAddress = document.getElementById("cardAddress");
    const cardBloodUnits = document.getElementById("cardBloodUnits");
    const cardRequiredDate = document.getElementById("cardRequiredDate");
    const cardUrgency = document.getElementById("cardUrgency");
    const cardMedicalInfo = document.getElementById("cardMedicalInfo");

    // Variable to hold Base64 string of the uploaded image
    let currentImageSrc = "";

    // 1. Show Form when clicking "Create Patient Profile" dashboard button
    if (profileBtn) {
        profileBtn.addEventListener("click", () => {
            profileFormContainer.style.display = "block";
            formSection.style.display = "block";
            profileCard.style.display = "none";
            profileBtn.scrollIntoView({ behavior: "smooth" });
        });
    }

    // 2. Handle Image Selection & Live Preview
    profilePictureInput.addEventListener("change", (e) => {
        const file = e.target.files[0];

        if (file) {
            const reader = new FileReader();

            reader.onload = function (event) {
                currentImageSrc = event.target.result;
                previewImage.src = currentImageSrc;
                previewImage.style.display = "block";
                imageText.style.display = "none";
            };

            reader.readAsDataURL(file);
        } else {
            resetImagePreview();
        }
    });

    // Reset Image Preview Helper
    function resetImagePreview() {
        currentImageSrc = "";
        previewImage.src = "";
        previewImage.style.display = "none";
        imageText.style.display = "block";
    }

    // 3. Handle Form Submission & Card Generation
    createProfileForm.addEventListener("submit", (e) => {
        e.preventDefault();

        // Extract Form Data
        const fullName = document.getElementById("fullName").value.trim();
        const bloodGroup = document.getElementById("bloodGroup").value;
        const phone = document.getElementById("phone").value.trim();
        const email = document.getElementById("profileEmail").value.trim();
        const dob = document.getElementById("dob").value;
        const hospital = document.getElementById("hospital").value.trim();
        const address = document.getElementById("address").value.trim();
        const bloodUnits = document.getElementById("bloodUnits").value;
        const requiredDate = document.getElementById("requiredDate").value;
        const urgency = document.getElementById("urgency").value;
        const medicalInfo = document.getElementById("medicalInfo").value.trim();

        // Populate Card Data
        cardName.textContent = fullName;
        cardBlood.textContent = bloodGroup;
        cardPhone.textContent = phone;
        cardEmail.textContent = email;
        cardDob.textContent = dob;
        cardHospital.textContent = hospital;
        cardAddress.textContent = address;
        cardBloodUnits.textContent = bloodUnits;
        cardRequiredDate.textContent = requiredDate;
        cardUrgency.textContent = urgency;
        cardMedicalInfo.textContent = medicalInfo || "N/A";

        // Dynamic styling class for Urgency badge
        cardUrgency.className = `availability-status urgency-${urgency.toLowerCase()}`;

        // Handle Profile Image Rendering
        if (currentImageSrc) {
            cardImage.src = currentImageSrc;
            cardImage.style.display = "block";
            defaultProfileImage.style.display = "none";
        } else {
            cardImage.src = "";
            cardImage.style.display = "none";
            defaultProfileImage.style.display = "flex";
        }

        // Hide Form and Display Created Card
        formSection.style.display = "none";
        profileCard.style.display = "block";
        profileCard.scrollIntoView({ behavior: "smooth" });
    });

    // 4. Handle Edit Profile Button
    editProfileBtn.addEventListener("click", () => {
        formSection.style.display = "block";
        profileCard.style.display = "none";
        formSection.scrollIntoView({ behavior: "smooth" });
    });
});

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