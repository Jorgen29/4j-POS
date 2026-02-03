function showPage(id, el) {
  document.querySelectorAll(".page").forEach((p) => p.classList.add("hidden"));
  document.getElementById(id).classList.remove("hidden");
  document
    .querySelectorAll(".nav-item")
    .forEach((i) => i.classList.remove("active"));
  el.classList.add("active");
  document.getElementById("pageTitle").innerText =
    id.charAt(0).toUpperCase() + id.slice(1);
}

function toggleUserMenu() {
  document.getElementById("userMenu").classList.toggle("hidden");
}

function toggleModal(id) {
  document.getElementById(id).classList.toggle("hidden");
}

function toggleMobileNav() {
  const mobileNav = document.getElementById("mobileNav");
  const hamburger = document.getElementById("hamburger");
  mobileNav.classList.toggle("active");
  hamburger.classList.toggle("active");
}

function toggleStoreNameField() {
  const role = document.getElementById("userRole").value;
  const storeNameContainer = document.getElementById("storeNameContainer");
  const storeNameInput = document.getElementById("userStoreName");

  if (role === "user") {
    storeNameContainer.classList.remove("hidden");
    storeNameInput.required = true;
  } else {
    storeNameContainer.classList.add("hidden");
    storeNameInput.required = false;
    storeNameInput.value = "";
  }
}

function toggleCreatePasswordVisibility() {
  const passwordInput = document.getElementById("userPassword");
  const icon = document.getElementById("createPasswordIcon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.innerText = "visibility_off";
  } else {
    passwordInput.type = "password";
    icon.innerText = "visibility";
  }
}

function toggleEditPasswordVisibility() {
  const passwordInput = document.getElementById("editUserPassword");
  const icon = document.getElementById("editPasswordIcon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.innerText = "visibility_off";
  } else {
    passwordInput.type = "password";
    icon.innerText = "visibility";
  }
}

function toggleProfilePasswordVisibility(fieldId) {
  const passwordInput = document.getElementById(fieldId);
  const icon = document.getElementById(fieldId + "Icon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.innerText = "visibility_off";
  } else {
    passwordInput.type = "password";
    icon.innerText = "visibility";
  }
}

function createUser(event) {
  event.preventDefault();

  const email = document.getElementById("userEmail").value;
  const password = document.getElementById("userPassword").value;
  const role = document.getElementById("userRole").value;
  const storeName = document.getElementById("userStoreName").value;

  // Store user data for confirmation
  window.userFormData = {
    email: email,
    password: password,
    role: role,
    storeName: storeName,
  };

  // Show confirmation details
  document.getElementById("confirmEmail").textContent = email;
  document.getElementById("confirmRole").textContent =
    role === "user" ? "User" : "Admin";

  if (role === "user") {
    document.getElementById("confirmStoreContainer").classList.remove("hidden");
    document.getElementById("confirmStore").textContent = storeName;
  } else {
    document.getElementById("confirmStoreContainer").classList.add("hidden");
  }

  // Close user creation modal and show confirmation modal
  toggleModal("userModal");
  toggleModal("confirmUserModal");
}

function closeConfirmModal() {
  toggleModal("confirmUserModal");
  toggleModal("userModal");
}

function confirmCreateUser() {
  const userData = window.userFormData;

  // Convert role string to int (0 = admin, 1 = user)
  const roleInt = userData.role === "user" ? 1 : 0;

  // Prepare form data for AJAX
  const formData = new FormData();
  formData.append("email", userData.email);
  formData.append("password", userData.password);
  formData.append("role", roleInt);
  if (userData.role === "user") {
    formData.append("storeName", userData.storeName);
  }

  // Send request to backend
  fetch("../../php/handlers/createUserHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Show success modal with returned user details
        document.getElementById("successEmail").textContent = userData.email;
        document.getElementById("successRole").textContent =
          userData.role === "user" ? "User" : "Admin";

        if (userData.role === "user") {
          document
            .getElementById("successStoreContainer")
            .classList.remove("hidden");
          document.getElementById("successStore").textContent =
            userData.storeName;
        } else {
          document
            .getElementById("successStoreContainer")
            .classList.add("hidden");
        }

        document.getElementById("successUserId").textContent =
          "U-" + data.user_id;

        window.isCreateOperation = true;

        // Close confirm modal and show success modal
        toggleModal("confirmUserModal");
        toggleModal("successModal");
      } else {
        // Show error
        alert("Error: " + data.message);
        toggleModal("confirmUserModal");
        toggleModal("userModal");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while creating the user.");
      toggleModal("confirmUserModal");
      toggleModal("userModal");
    });
}

function closeSuccessModal() {
  toggleModal("successModal");

  // Reset form and clear data
  document.getElementById("createUserForm").reset();
  document.getElementById("storeNameContainer").classList.add("hidden");
  window.userFormData = null;

  // Reload page if this was a create, edit, or delete operation
  if (window.isCreateOperation) {
    window.isCreateOperation = false;
    location.reload();
  } else if (window.isEditOperation) {
    window.isEditOperation = false;
    location.reload();
  } else if (window.isDeleteOperation) {
    window.isDeleteOperation = false;
    location.reload();
  }
}

function editUser(userId) {
  // Store user ID for later use
  window.currentEditUserId = userId;

  // Fetch user data and populate form
  fetch("../../php/handlers/getUserHandler.php?user_id=" + userId)
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const user = data.data;
        document.getElementById("editUserId").value = userId;
        document.getElementById("editUserEmail").value = user.email;
        document.getElementById("editUserPassword").value = "";
        document.getElementById("editUserRole").value = user.role;
        document.getElementById("editUserStatus").value = user.status;

        // Show/hide store name field based on role
        if (user.role == 1) {
          document
            .getElementById("editStoreNameContainer")
            .classList.remove("hidden");
          document.getElementById("editUserStoreName").value =
            user.store_name || "";
        } else {
          document
            .getElementById("editStoreNameContainer")
            .classList.add("hidden");
        }

        // Open modal
        toggleModal("editUserModal");
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while fetching user data.");
    });
}

function toggleEditStoreNameField() {
  const role = document.getElementById("editUserRole").value;
  const storeNameContainer = document.getElementById("editStoreNameContainer");

  if (role == 1) {
    storeNameContainer.classList.remove("hidden");
  } else {
    storeNameContainer.classList.add("hidden");
  }
}

function closeEditModal() {
  toggleModal("editUserModal");
}

function submitEditUser(event) {
  event.preventDefault();

  const userId = document.getElementById("editUserId").value;
  const password = document.getElementById("editUserPassword").value;
  const role = document.getElementById("editUserRole").value;
  const storeName = document.getElementById("editUserStoreName").value;
  const status = document.getElementById("editUserStatus").value;

  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("role", role);
  formData.append("status", status);
  if (password) {
    formData.append("password", password);
  }
  if (role == 1) {
    formData.append("storeName", storeName);
  }

  fetch("../../php/handlers/updateUserHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Get email from the form
        const email = document.getElementById("editUserEmail").value;
        const roleText = role == 1 ? "User" : "Admin";

        // Set success modal for edit scenario
        document.getElementById("successHeader").textContent =
          "User Updated Successfully";
        document.getElementById("successMessage").textContent =
          "The user information has been updated successfully!";

        // Populate success details
        document.getElementById("successEmail").textContent = email;
        document.getElementById("successRole").textContent = roleText;

        if (role == 1) {
          document
            .getElementById("successStoreContainer")
            .classList.remove("hidden");
          document.getElementById("successStore").textContent = storeName;
        } else {
          document
            .getElementById("successStoreContainer")
            .classList.add("hidden");
        }

        document.getElementById("successUserId").textContent = "U-" + userId;

        window.isEditOperation = true;

        toggleModal("editUserModal");
        toggleModal("successModal");
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while updating the user.");
    });
}

function deleteUser(userId, email) {
  // Store user data for confirmation
  window.currentDeleteUserId = userId;
  document.getElementById("deleteUserId").textContent =
    "U-" + String(userId).padStart(3, "0");
  document.getElementById("deleteUserEmail").textContent = email;

  // Open delete confirmation modal
  toggleModal("deleteUserModal");
}

function closeDeleteModal() {
  toggleModal("deleteUserModal");
  window.currentDeleteUserId = null;
}

function confirmDeleteUser() {
  const userId = window.currentDeleteUserId;
  const email = document.getElementById("deleteUserEmail").textContent;

  if (!userId) {
    alert("Error: User ID not found");
    return;
  }

  // Send delete request to backend
  const formData = new FormData();
  formData.append("user_id", userId);

  fetch("../../php/handlers/deleteUserHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Set success modal for delete scenario
        document.getElementById("successHeader").textContent =
          "User Deleted Successfully";
        document.getElementById("successMessage").textContent =
          "The user has been deleted successfully!";

        // Populate success details with deleted user info
        document.getElementById("successEmail").textContent = email;
        document.getElementById("successRole").textContent = "N/A";
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");
        document.getElementById("successUserId").textContent =
          "U-" + String(userId).padStart(3, "0");

        window.isDeleteOperation = true;

        toggleModal("deleteUserModal");
        toggleModal("successModal");
        window.currentDeleteUserId = null;
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while deleting the user.");
    });
}

document.addEventListener("click", (e) => {
  if (!e.target.closest(".relative"))
    document.getElementById("userMenu").classList.add("hidden");

  const mobileNav = document.getElementById("mobileNav");
  const hamburger = document.getElementById("hamburger");

  if (
    mobileNav.classList.contains("active") &&
    !e.target.closest(".mobile-nav-menu") &&
    !e.target.closest(".hamburger")
  ) {
    mobileNav.classList.remove("active");
    hamburger.classList.remove("active");
  }
});
