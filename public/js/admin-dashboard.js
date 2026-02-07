// Global variables
let shouldReloadPage = false;
let barcodeScannerSource = "add";
let barcodeScanning = false;
let detectedBarcodeValue = null;
let allProducts = [];

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

  // If success modal is closed and we need to reload, do it now
  if (
    id === "successModal" &&
    document.getElementById("successModal").classList.contains("hidden") &&
    shouldReloadPage
  ) {
    console.log("[Page] Reloading after modal close");
    shouldReloadPage = false; // Reset flag
    setTimeout(() => {
      location.reload();
    }, 500);
  }
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

// Subscription Functions
function createSubscription() {
  const name = document.getElementById("subscriptionName").value;
  const duration = document.getElementById("subscriptionDuration").value;
  const price = document.getElementById("subscriptionPrice").value;
  const errorDiv = document.getElementById("subscriptionError");
  const errorText = document.getElementById("subscriptionErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate inputs
  if (!name || !duration || !price) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please fill in all fields";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[Subscription Debug] Creating subscription:", {
    name,
    duration,
    price,
  });

  // Create FormData
  const formData = new FormData();
  formData.append("name", name);
  formData.append("duration", duration);
  formData.append("price", price);

  // Send request to backend
  fetch("../../php/handlers/createSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      console.log("[Subscription Debug] Response status:", response.status);
      return response.json();
    })
    .then((data) => {
      console.log("[Subscription Debug] Response data:", data);
      if (data.status === "success") {
        console.log(
          "[Subscription Debug] Success! Subscription ID:",
          data.subscription_id,
        );

        // Set success modal for subscription creation
        document.getElementById("successHeader").textContent =
          "Subscription Created Successfully";
        document.getElementById("successMessage").textContent =
          "The subscription has been created successfully!";

        // Populate success details
        document.getElementById("successEmail").textContent = name;
        document.getElementById("successRole").textContent =
          duration + " Month(s) - PHP " + price;
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");
        document.getElementById("successUserId").textContent =
          "SUB-" + String(data.subscription_id).padStart(3, "0");

        window.isCreateOperation = true;

        toggleModal("subscriptionModal");
        toggleModal("successModal");

        // Reset form
        document.getElementById("createSubscriptionForm").reset();
      } else {
        console.log("[Subscription Debug] Error:", data.message);
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[Subscription Debug] Fetch Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

function clearSubscriptionForm() {
  const form = document.getElementById("createSubscriptionForm");
  const errorDiv = document.getElementById("subscriptionError");

  if (form) {
    form.reset();
  }
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }
}

function deleteSubscription(subscriptionId, subscriptionName) {
  window.currentDeleteSubscriptionId = subscriptionId;
  document.getElementById("deleteSubscriptionId").textContent =
    "SUB-" + String(subscriptionId).padStart(3, "0");
  document.getElementById("deleteSubscriptionName").textContent =
    subscriptionName;

  toggleModal("deleteSubscriptionModal");
}

function confirmDeleteSubscription() {
  const subscriptionId = window.currentDeleteSubscriptionId;
  const subscriptionName = document.getElementById(
    "deleteSubscriptionName",
  ).textContent;

  if (!subscriptionId) {
    alert("Error: Subscription ID not found");
    return;
  }

  const formData = new FormData();
  formData.append("subscription_id", subscriptionId);

  fetch("../../php/handlers/deleteSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Set success modal for delete scenario
        document.getElementById("successHeader").textContent =
          "Subscription Deleted Successfully";
        document.getElementById("successMessage").textContent =
          "The subscription has been deleted successfully!";

        // Populate success details with deleted subscription info
        document.getElementById("successEmail").textContent = subscriptionName;
        document.getElementById("successRole").textContent = "N/A";
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");
        document.getElementById("successUserId").textContent =
          "SUB-" + String(subscriptionId).padStart(3, "0");

        window.isDeleteOperation = true;

        toggleModal("deleteSubscriptionModal");
        toggleModal("successModal");
        window.currentDeleteSubscriptionId = null;
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while deleting the subscription.");
    });
}

function editSubscription(subscriptionId) {
  window.currentEditSubscriptionId = subscriptionId;

  // Fetch subscription data and populate form
  fetch(
    "../../php/handlers/getSubscriptionHandler.php?subscription_id=" +
      subscriptionId,
  )
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const subscription = data.data;
        document.getElementById("editSubscriptionId").value = subscriptionId;
        document.getElementById("editSubscriptionName").value =
          subscription.name;
        document.getElementById("editSubscriptionDuration").value =
          subscription.duration;
        document.getElementById("editSubscriptionPrice").value =
          subscription.price;

        // Open modal
        toggleModal("editSubscriptionModal");
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred while fetching the subscription.");
    });
}

function submitEditSubscription() {
  const subscriptionId = document.getElementById("editSubscriptionId").value;
  const name = document.getElementById("editSubscriptionName").value;
  const duration = document.getElementById("editSubscriptionDuration").value;
  const price = document.getElementById("editSubscriptionPrice").value;
  const errorDiv = document.getElementById("editSubscriptionError");
  const errorText = document.getElementById("editSubscriptionErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate inputs
  if (!subscriptionId || !name || !duration || !price) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please fill in all fields";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[Subscription Debug] Updating subscription:", {
    subscriptionId,
    name,
    duration,
    price,
  });

  // Create FormData
  const formData = new FormData();
  formData.append("subscription_id", subscriptionId);
  formData.append("name", name);
  formData.append("duration", duration);
  formData.append("price", price);

  // Send request to backend
  fetch("../../php/handlers/updateSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[Subscription Debug] Response data:", data);
      if (data.status === "success") {
        console.log("[Subscription Debug] Success updating subscription!");

        // Set success modal for subscription update
        document.getElementById("successHeader").textContent =
          "Subscription Updated Successfully";
        document.getElementById("successMessage").textContent =
          "The subscription has been updated successfully!";

        // Populate success details
        document.getElementById("successEmail").textContent = name;
        document.getElementById("successRole").textContent =
          duration + " Month(s) - PHP " + price;
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");
        document.getElementById("successUserId").textContent =
          "SUB-" + String(subscriptionId).padStart(3, "0");

        window.isEditOperation = true;

        toggleModal("editSubscriptionModal");
        toggleModal("successModal");

        // Reset form
        document.getElementById("editSubscriptionForm").reset();
      } else {
        console.log("[Subscription Debug] Error:", data.message);
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[Subscription Debug] Fetch Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

// ========== USER SUBSCRIPTIONS FUNCTIONS ==========

function renewUserSubscription(userId, userEmail) {
  console.log(
    "[User Subscription Debug] Renewing subscription for user:",
    userId,
    userEmail,
  );
  window.currentUserId = userId;
  window.currentUserEmail = userEmail;

  // Set user info in modal
  document.getElementById("renewUserId").value = userId;
  document.getElementById("renewUserEmail").textContent = userEmail;

  // Clear previous selection
  document.getElementById("renewSubscriptionSelect").value = "";
  document.getElementById("renewDurationDisplay").value = "";
  document.getElementById("renewPriceDisplay").value = "";

  // Fetch current subscription for this user
  const formData = new FormData();
  formData.append("user_id", userId);

  fetch("../../php/handlers/getUserCurrentSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log(
        "[User Subscription Debug] Current subscription fetched:",
        data,
      );
      if (data.status === "success" && data.data) {
        const currentSub = data.data;
        const selectElement = document.getElementById(
          "renewSubscriptionSelect",
        );

        // Populate with only the current subscription
        selectElement.innerHTML = "";
        const option = document.createElement("option");
        option.value = currentSub.subscription_id;
        option.setAttribute("data-duration", currentSub.duration);
        option.setAttribute("data-price", currentSub.price);
        option.textContent = currentSub.name + " (Current)";
        option.selected = true;
        selectElement.appendChild(option);
        selectElement.disabled = true; // Make it read-only

        // Update display fields
        document.getElementById("renewDurationDisplay").value =
          currentSub.duration + " Month(s)";
        document.getElementById("renewPriceDisplay").value =
          "₱" + parseInt(currentSub.price).toLocaleString();

        toggleModal("renewSubscriptionModal");
      } else {
        alert("Error: " + (data.message || "No current subscription found"));
      }
    })
    .catch((error) => {
      console.error(
        "[User Subscription Debug] Error fetching current subscription:",
        error,
      );
      alert("Error loading subscription");
    });
}

function updateRenewSubscriptionDisplay() {
  const selectElement = document.getElementById("renewSubscriptionSelect");
  const selectedOption = selectElement.options[selectElement.selectedIndex];

  if (selectedOption.value) {
    const duration = selectedOption.getAttribute("data-duration");
    const price = selectedOption.getAttribute("data-price");

    document.getElementById("renewDurationDisplay").value =
      duration + " Month(s)";
    document.getElementById("renewPriceDisplay").value =
      "₱" + parseInt(price).toLocaleString();
  } else {
    document.getElementById("renewDurationDisplay").value = "";
    document.getElementById("renewPriceDisplay").value = "";
  }
}

function submitRenewSubscription() {
  const userId = document.getElementById("renewUserId").value;
  const subscriptionId = document.getElementById(
    "renewSubscriptionSelect",
  ).value;
  const errorDiv = document.getElementById("renewSubscriptionError");
  const errorText = document.getElementById("renewSubscriptionErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate
  if (!subscriptionId) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please select a subscription";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[User Subscription Debug] Renewing subscription:", {
    userId,
    subscriptionId,
  });

  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("subscription_id", subscriptionId);

  fetch("../../php/handlers/renewSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[User Subscription Debug] Renewal response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Subscription Renewed";
        document.getElementById("successMessage").textContent =
          `Subscription renewed successfully for ${document.getElementById("renewUserEmail").textContent}`;
        document.getElementById("successEmail").textContent =
          data.data.subscription_name;
        document.getElementById("successRole").textContent =
          data.data.duration +
          " Month(s) - ₱" +
          parseInt(data.data.price).toLocaleString();
        document.getElementById("successUserId").textContent =
          "USR-" + String(data.data.user_id).padStart(3, "0");
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("renewSubscriptionModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[User Subscription Debug] Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

function resetUserSubscription(userId, userEmail) {
  console.log(
    "[User Subscription Debug] Resetting subscription for user:",
    userId,
    userEmail,
  );

  // Set modal values
  document.getElementById("resetConfirmUserId").value = userId;
  document.getElementById("resetConfirmUserEmail").value = userEmail;
  document.getElementById("resetConfirmEmail").textContent = userEmail;

  // Show confirmation modal
  toggleModal("resetSubscriptionConfirmModal");
}

function confirmResetSubscription() {
  const userId = document.getElementById("resetConfirmUserId").value;
  const userEmail = document.getElementById("resetConfirmUserEmail").value;

  console.log(
    "[User Subscription Debug] Confirming subscription reset for user:",
    userId,
    userEmail,
  );

  const formData = new FormData();
  formData.append("user_id", userId);

  fetch("../../php/handlers/deleteUserSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[User Subscription Debug] Delete response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Subscription Reset";
        document.getElementById("successMessage").textContent =
          `Subscription reset successfully for ${userEmail}`;
        document.getElementById("successEmail").textContent = "No Subscription";
        document.getElementById("successRole").textContent =
          "Subscription has been removed";
        document.getElementById("successUserId").textContent =
          "USR-" + String(userId).padStart(3, "0");
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("resetSubscriptionConfirmModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error(
        "[User Subscription Debug] Error deleting subscription:",
        error,
      );
      alert("Error deleting subscription: " + error.message);
    });
}

function deleteUserSubscription() {
  const userId = document.getElementById("renewUserId").value;
  const userEmail = document.getElementById("renewUserEmail").textContent;

  // Confirm deletion
  if (
    !confirm(
      "Are you sure you want to reset the subscription for " +
        userEmail +
        "? This will remove their current subscription.",
    )
  ) {
    return;
  }

  console.log(
    "[User Subscription Debug] Deleting subscription for user:",
    userId,
  );

  const formData = new FormData();
  formData.append("user_id", userId);

  fetch("../../php/handlers/deleteUserSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[User Subscription Debug] Delete response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Subscription Reset";
        document.getElementById("successMessage").textContent =
          `Subscription reset successfully for ${userEmail}`;
        document.getElementById("successEmail").textContent = "No Subscription";
        document.getElementById("successRole").textContent =
          "Subscription has been removed";
        document.getElementById("successUserId").textContent =
          "USR-" + String(userId).padStart(3, "0");
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("renewSubscriptionModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error(
        "[User Subscription Debug] Error deleting subscription:",
        error,
      );
      alert("Error deleting subscription: " + error.message);
    });
}

// Add event listener for subscription select change
document.addEventListener("DOMContentLoaded", function () {
  const selectElement = document.getElementById("renewSubscriptionSelect");
  if (selectElement) {
    selectElement.addEventListener("change", updateRenewSubscriptionDisplay);
  }
});

function addUserSubscription(userId, userEmail) {
  console.log(
    "[User Subscription Debug] Adding subscription for user:",
    userId,
    userEmail,
  );
  window.currentUserId = userId;
  window.currentUserEmail = userEmail;

  // Set user info in modal
  document.getElementById("assignUserId").value = userId;
  document.getElementById("assignUserEmail").textContent = userEmail;

  // Clear previous selection
  document.getElementById("assignSubscriptionSelect").value = "";
  document.getElementById("assignDurationDisplay").value = "";
  document.getElementById("assignPriceDisplay").value = "";

  // Fetch all subscriptions
  fetch("../../php/handlers/getAllSubscriptionsHandler.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("[User Subscription Debug] Subscriptions fetched:", data);
      if (data.status === "success") {
        const selectElement = document.getElementById(
          "assignSubscriptionSelect",
        );
        selectElement.innerHTML =
          '<option value="">-- Choose Subscription --</option>';

        data.data.forEach((subscription) => {
          const option = document.createElement("option");
          option.value = subscription.subscription_id;
          option.setAttribute("data-duration", subscription.duration);
          option.setAttribute("data-price", subscription.price);
          option.textContent = subscription.name;
          selectElement.appendChild(option);
        });

        // Attach change event listener
        selectElement.removeEventListener(
          "change",
          updateAssignSubscriptionDisplay,
        );
        selectElement.addEventListener(
          "change",
          updateAssignSubscriptionDisplay,
        );

        toggleModal("assignSubscriptionModal");
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((error) => {
      console.error(
        "[User Subscription Debug] Error fetching subscriptions:",
        error,
      );
      alert("Error loading subscriptions");
    });
}

function updateAssignSubscriptionDisplay() {
  const selectElement = document.getElementById("assignSubscriptionSelect");
  const selectedOption = selectElement.options[selectElement.selectedIndex];

  if (selectedOption.value) {
    const duration = selectedOption.getAttribute("data-duration");
    const price = selectedOption.getAttribute("data-price");

    document.getElementById("assignDurationDisplay").value =
      duration + " Month(s)";
    document.getElementById("assignPriceDisplay").value =
      "₱" + parseInt(price).toLocaleString();
  } else {
    document.getElementById("assignDurationDisplay").value = "";
    document.getElementById("assignPriceDisplay").value = "";
  }
}

function submitAssignSubscription() {
  const userId = document.getElementById("assignUserId").value;
  const subscriptionId = document.getElementById(
    "assignSubscriptionSelect",
  ).value;
  const errorDiv = document.getElementById("assignSubscriptionError");
  const errorText = document.getElementById("assignSubscriptionErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate
  if (!subscriptionId) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please select a subscription";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[User Subscription Debug] Assigning subscription:", {
    userId,
    subscriptionId,
  });

  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("subscription_id", subscriptionId);

  fetch("../../php/handlers/renewSubscriptionHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[User Subscription Debug] Assignment response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Subscription Assigned";
        document.getElementById("successMessage").textContent =
          `Subscription assigned successfully to ${document.getElementById("assignUserEmail").textContent}`;
        document.getElementById("successEmail").textContent =
          data.data.subscription_name;
        document.getElementById("successRole").textContent =
          data.data.duration +
          " Month(s) - ₱" +
          parseInt(data.data.price).toLocaleString();
        document.getElementById("successUserId").textContent =
          "USR-" + String(data.data.user_id).padStart(3, "0");
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("assignSubscriptionModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[User Subscription Debug] Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

function openUserSubscriptionModal() {
  console.log(
    "[User Subscription Debug] Opening user subscription assignment modal",
  );
  // Placeholder - will add modal for bulk subscription assignment
  alert("Subscription assignment form coming soon");
}

document.addEventListener("click", (e) => {
  const userMenu = document.getElementById("userMenu");
  if (userMenu && !e.target.closest(".relative")) {
    userMenu.classList.add("hidden");
  }

  const mobileNav = document.getElementById("mobileNav");
  const hamburger = document.getElementById("hamburger");

  if (
    mobileNav &&
    hamburger &&
    mobileNav.classList.contains("active") &&
    !e.target.closest(".mobile-nav-menu") &&
    !e.target.closest(".hamburger")
  ) {
    mobileNav.classList.remove("active");
    hamburger.classList.remove("active");
  }
});

// ============================================
// PRODUCT MANAGEMENT FUNCTIONS
// ============================================

function openProductModal() {
  console.log("[Product Debug] Opening product modal");

  // Clear form
  document.getElementById("productForm").reset();
  document.getElementById("productError").classList.add("hidden");

  // Fetch stores for dropdown
  fetch("../../php/handlers/getAllStoresHandler.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("[Product Debug] Stores fetched:", data);
      if (data.status === "success") {
        const storeSelect = document.getElementById("productStore");
        storeSelect.innerHTML = '<option value="">-- Select Store --</option>';

        data.data.forEach((store) => {
          const option = document.createElement("option");
          option.value = store.store_id;
          option.textContent = store.store_name;
          storeSelect.appendChild(option);
        });
      }
    })
    .catch((error) => {
      console.error("[Product Debug] Error fetching stores:", error);
    });

  toggleModal("productModal");
}

function submitAddProduct() {
  const barcode = document.getElementById("productBarcode").value;
  const productName = document.getElementById("productName").value;
  const price = document.getElementById("productPrice").value;
  const quantity = document.getElementById("productQuantity").value;
  const storeId = document.getElementById("productStore").value;
  const errorDiv = document.getElementById("productError");
  const errorText = document.getElementById("productErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate - SKU is now auto-generated, so don't require it
  if (!barcode || !productName || !price || !quantity || !storeId) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please fill in all required fields";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[Product Debug] Creating product:", {
    barcode,
    productName,
    price,
    quantity,
    storeId,
  });

  const formData = new FormData();
  formData.append("barcode", barcode);
  // SKU is no longer sent - it will be generated on backend
  formData.append("product_name", productName);
  formData.append("price", price);
  formData.append("quantity", quantity);
  formData.append("store_id", storeId);

  fetch("../../php/handlers/createProductHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[Product Debug] Create response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent = "Product Added";
        document.getElementById("successMessage").textContent =
          `Product "${productName}" created successfully`;
        document.getElementById("successEmail").textContent = data.data.barcode;
        document.getElementById("successRole").textContent =
          "Qty: " + quantity + " - ₱" + parseFloat(price).toLocaleString();
        // Show the auto-generated SKU
        document.getElementById("successUserId").textContent =
          "SKU: " + data.data.sku;
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("productModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[Product Debug] Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

// ========================
// Barcode Scanner Functions
// ========================

function openBarcodeScanner() {
  console.log("[Barcode Scanner] Opening scanner modal");

  // Determine which modal is open
  const addProductModal = document.getElementById("productModal");
  const editProductModal = document.getElementById("editProductModal");

  if (!addProductModal.classList.contains("hidden")) {
    barcodeScannerSource = "add";
  } else if (!editProductModal.classList.contains("hidden")) {
    barcodeScannerSource = "edit";
  }

  const scannerModal = document.getElementById("barcodeScannerModal");
  const errorDiv = document.getElementById("scanner-error");
  const errorText = document.getElementById("scanner-error-text");
  const resultDiv = document.getElementById("scanner-result");
  const detectedBarcodeElement = document.getElementById("detected-barcode");
  const useBarcodeBtn = document.getElementById("use-barcode-btn");

  // Show modal
  scannerModal.classList.remove("hidden");

  // Hide previous results
  resultDiv.classList.add("hidden");
  errorDiv.classList.add("hidden");
  useBarcodeBtn.classList.add("hidden");
  barcodeScanning = true;
  detectedBarcodeValue = null;

  const video = document.getElementById("scanner-video");

  // Check if site is secure (HTTPS or localhost)
  const isSecureContext =
    window.isSecureContext ||
    window.location.hostname === "localhost" ||
    window.location.hostname === "127.0.0.1";

  if (!isSecureContext) {
    errorText.textContent =
      "⚠️ Camera requires HTTPS or localhost. Please access via https:// or use localhost. You can still enter barcode manually.";
    errorDiv.classList.remove("hidden");
    barcodeScanning = false;
    return;
  }

  // Request camera access
  navigator.mediaDevices
    .getUserMedia({ video: { facingMode: "environment" } })
    .then(function (stream) {
      console.log("[Barcode Scanner] Camera access granted");
      video.srcObject = stream;
      video.style.display = "block";
      document.getElementById("scanner-placeholder").style.display = "none";

      // Initialize Quagga after video is ready
      setTimeout(() => {
        try {
          Quagga.init(
            {
              inputStream: {
                type: "VideoStream",
                target: video,
                constraints: {
                  facingMode: "environment",
                  width: { ideal: 1280 },
                  height: { ideal: 720 },
                },
              },
              decoder: {
                readers: [
                  "code_128_reader",
                  "ean_reader",
                  "ean_8_reader",
                  "upc_reader",
                  "upc_e_reader",
                  "code_39_reader",
                  "code_93_reader",
                  "codabar_reader",
                ],
                debug: {
                  showPatternInResult: false,
                },
              },
            },
            function (err) {
              if (err) {
                console.error("[Barcode Scanner] Quagga init error:", err);
                errorText.textContent =
                  "Scanner initialization error: " + err.message;
                errorDiv.classList.remove("hidden");
                return;
              }
              console.log("[Barcode Scanner] Quagga initialized successfully");
              Quagga.start();

              Quagga.onDetected(function (result) {
                if (result && result.codeResult && result.codeResult.code) {
                  const barcode = result.codeResult.code;
                  console.log("[Barcode Scanner] Detected barcode:", barcode);

                  detectedBarcodeValue = barcode;
                  detectedBarcodeElement.textContent = barcode;
                  resultDiv.classList.remove("hidden");
                  useBarcodeBtn.classList.remove("hidden");

                  Quagga.stop();
                  barcodeScanning = false;
                }
              });
            },
          );
        } catch (error) {
          console.error("[Barcode Scanner] Error initializing Quagga:", error);
          errorText.textContent = "Error: " + error.message;
          errorDiv.classList.remove("hidden");
          barcodeScanning = false;
        }
      }, 300);
    })
    .catch(function (err) {
      console.error("[Barcode Scanner] Camera access error:", err);
      let errorMessage = "Camera access denied";

      if (err.name === "NotAllowedError") {
        errorMessage = "Camera permission denied. Please allow camera access.";
      } else if (
        err.name === "NotFoundError" ||
        err.name === "NotSupportedError"
      ) {
        errorMessage = "No camera found or not supported.";
      } else if (err.name === "NotSecureError") {
        errorMessage =
          "⚠️ HTTPS required for camera access. Use https:// or localhost.";
      }

      errorText.textContent = errorMessage;
      errorDiv.classList.remove("hidden");
      barcodeScanning = false;
    });
}

function closeBarcodeScanner() {
  console.log("[Barcode Scanner] Closing scanner");

  const scannerModal = document.getElementById("barcodeScannerModal");
  const scannerVideo = document.getElementById("scanner-video");
  const scannerPlaceholder = document.getElementById("scanner-placeholder");

  // Stop Quagga
  if (barcodeScanning) {
    try {
      Quagga.stop();
      Quagga.offDetected();
      Quagga.offProcessed();
    } catch (e) {
      console.log(
        "[Barcode Scanner] Quagga already stopped or not initialized",
      );
    }
  }

  // Stop video stream
  if (scannerVideo.srcObject) {
    const tracks = scannerVideo.srcObject.getTracks();
    tracks.forEach((track) => track.stop());
    scannerVideo.srcObject = null;
  }

  // Hide modal
  scannerModal.classList.add("hidden");

  // Reset video
  scannerVideo.style.display = "none";
  scannerPlaceholder.style.display = "flex";

  barcodeScanning = false;
  detectedBarcodeValue = null;
}

function useDetectedBarcode() {
  if (detectedBarcodeValue) {
    console.log(
      "[Barcode Scanner] Using detected barcode:",
      detectedBarcodeValue,
      "Source:",
      barcodeScannerSource,
    );

    // Populate barcode field based on which modal is open
    if (barcodeScannerSource === "edit") {
      document.getElementById("editProductBarcode").value =
        detectedBarcodeValue;
    } else {
      document.getElementById("productBarcode").value = detectedBarcodeValue;
    }

    // Close scanner modal
    closeBarcodeScanner();

    console.log(
      "[Barcode Scanner] Barcode populated in",
      barcodeScannerSource,
      "form",
    );
  }
}

// ========================
// Barcode Image Upload Handler
// ========================
function handleBarcodeImageUpload(event) {
  const file = event.target.files[0];
  if (!file) return;

  console.log("[Barcode Upload] Processing image:", file.name);

  const reader = new FileReader();
  const errorDiv = document.getElementById("scanner-error");
  const errorText = document.getElementById("scanner-error-text");
  const resultDiv = document.getElementById("scanner-result");
  const detectedBarcodeElement = document.getElementById("detected-barcode");
  const useBarcodeBtn = document.getElementById("use-barcode-btn");

  reader.onload = function (e) {
    const img = new Image();
    img.onload = function () {
      console.log("[Barcode Upload] Image loaded, scanning...");

      // Show image preview
      const uploadPreview = document.getElementById("upload-preview");
      uploadPreview.src = e.target.result;
      uploadPreview.style.display = "block";
      document.getElementById("scanner-placeholder").style.display = "none";

      // Create canvas from image
      const canvas = document.getElementById("scanner-canvas");
      const ctx = canvas.getContext("2d");
      canvas.width = img.width;
      canvas.height = img.height;
      ctx.drawImage(img, 0, 0);

      // Use Quagga to decode barcode from image
      try {
        Quagga.decodeSingle(
          {
            src: e.target.result,
            numOfWorkers: 0,
            inputStream: {
              size: 800,
            },
            decoder: {
              readers: [
                "code_128_reader",
                "ean_reader",
                "ean_8_reader",
                "upc_reader",
                "upc_e_reader",
                "code_39_reader",
                "code_93_reader",
                "codabar_reader",
              ],
            },
          },
          function (result) {
            if (result && result.codeResult) {
              const barcode = result.codeResult.code;
              console.log("[Barcode Upload] Barcode detected:", barcode);

              // Clear errors
              errorDiv.classList.add("hidden");

              // Show result
              detectedBarcodeValue = barcode;
              detectedBarcodeElement.textContent = barcode;
              resultDiv.classList.remove("hidden");
              useBarcodeBtn.classList.remove("hidden");
            } else {
              console.warn("[Barcode Upload] No barcode found in image");
              errorText.textContent =
                "No barcode detected in the image. Try a clearer image.";
              errorDiv.classList.remove("hidden");
            }
          },
        );
      } catch (error) {
        console.error("[Barcode Upload] Decoding error:", error);
        errorText.textContent = "Error decoding image: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    };

    img.onerror = function () {
      console.error("[Barcode Upload] Failed to load image");
      errorText.textContent = "Failed to load image. Please try another file.";
      errorDiv.classList.remove("hidden");
    };

    img.src = e.target.result;
  };

  reader.onerror = function () {
    console.error("[Barcode Upload] File read error");
    errorText.textContent = "Error reading file.";
    errorDiv.classList.remove("hidden");
  };

  reader.readAsDataURL(file);

  // Reset file input
  event.target.value = "";
}

// ========================
// Product Display Functions
// ========================

function loadProducts() {
  console.log("[Products] Loading all products...");

  fetch("../../php/handlers/getAllProductsHandler.php")
    .then((response) => response.json())
    .then((data) => {
      console.log("[Products] Data received:", data);

      if (data.status === "success") {
        allProducts = data.data || [];
        console.log("[Products] Total products:", allProducts.length);
        displayProducts(allProducts);
      } else {
        console.error("[Products] Error:", data.message);
        showProductError("Failed to load products: " + data.message);
      }
    })
    .catch((error) => {
      console.error("[Products] Fetch error:", error);
      showProductError("Error loading products: " + error.message);
    });
}

function displayProducts(products) {
  const productTable = document.getElementById("productTable");

  if (!productTable) {
    console.warn("[Products] productTable element not found");
    return;
  }

  // Clear existing rows
  productTable.innerHTML = "";

  if (!products || products.length === 0) {
    productTable.innerHTML = `
      <tr>
        <td colspan="8" class="py-8 px-4 text-center text-gray-500">
          No products found. <a href="#" onclick="openProductModal(); return false;" class="text-blue-500 hover:underline">Add one now</a>
        </td>
      </tr>
    `;
    return;
  }

  products.forEach((product) => {
    const row = document.createElement("tr");
    row.className = "border-b hover:bg-cream";
    row.innerHTML = `
      <td class="py-3 px-4 font-medium">P-${String(product.product_id).padStart(3, "0")}</td>
      <td class="py-3 px-4 font-mono text-sm">${product.barcode}</td>
      <td class="py-3 px-4 font-mono text-xs text-gray-600">${product.sku || "N/A"}</td>
      <td class="py-3 px-4">${product.product_name}</td>
      <td class="py-3 px-4">${product.store_name || "N/A"}</td>
      <td class="py-3 px-4 text-center">${product.quantity}</td>
      <td class="py-3 px-4 font-semibold">₱${Number(product.price).toFixed(2)}</td>
      <td class="py-3 px-4">
        <div class="flex gap-2 justify-center">
          <button onclick="openEditProductModal(${product.product_id})" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition" title="Edit">
            <span class="material-icons text-base">edit</span>
          </button>
          <button onclick="openDeleteProductModal(${product.product_id})" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition" title="Delete">
            <span class="material-icons text-base">delete_outline</span>
          </button>
        </div>
      </td>
    `;
    productTable.appendChild(row);
  });

  console.log("[Products] Displayed", products.length, "products");
}

function showProductError(message) {
  console.error("[Products Error]", message);
  const productTable = document.getElementById("productTable");
  if (productTable) {
    productTable.innerHTML = `
      <tr>
        <td colspan="6" class="py-8 px-4 text-center text-red-500">
          ⚠️ ${message}
        </td>
      </tr>
    `;
  }
}

function filterProducts() {
  const searchInput = document.getElementById("productSearch");
  const storeFilter = document.getElementById("storeFilter");

  if (!searchInput || !storeFilter) {
    console.warn("[Products] Filter inputs not found");
    return;
  }

  const searchTerm = searchInput.value.toLowerCase().trim();
  const selectedStore = storeFilter.value.trim();

  console.log(
    "[Products] Filtering - Search:",
    searchTerm,
    "Store:",
    selectedStore,
  );

  const filtered = allProducts.filter((product) => {
    const matchesSearch =
      !searchTerm ||
      product.product_name.toLowerCase().includes(searchTerm) ||
      product.barcode.includes(searchTerm) ||
      String(product.product_id).includes(searchTerm);

    const matchesStore = !selectedStore || product.store_name === selectedStore;

    return matchesSearch && matchesStore;
  });

  console.log("[Products] Filtered to", filtered.length, "products");
  displayProducts(filtered);
}

// ========================
// Edit Product Functions
// ========================
function openEditProductModal(productId) {
  console.log("[Edit Product] Opening edit modal for product:", productId);

  const product = allProducts.find((p) => p.product_id == productId);
  if (!product) {
    console.error("[Edit Product] Product not found");
    return;
  }

  // Populate form with current product data
  document.getElementById("editProductId").value = productId;
  document.getElementById("editProductBarcode").value = product.barcode;
  document.getElementById("editProductName").value = product.product_name;
  document.getElementById("editProductPrice").value = product.price;
  document.getElementById("editProductQuantity").value = product.quantity;
  document.getElementById("editProductError").classList.add("hidden");

  // Fetch and populate stores
  fetch("../../php/handlers/getAllStoresHandler.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        const storeSelect = document.getElementById("editProductStore");
        storeSelect.innerHTML = '<option value="">-- Select Store --</option>';

        data.data.forEach((store) => {
          const option = document.createElement("option");
          option.value = store.store_id;
          option.textContent = store.store_name;
          option.selected = store.store_id == product.store_id;
          storeSelect.appendChild(option);
        });
      }
    })
    .catch((error) =>
      console.error("[Edit Product] Error fetching stores:", error),
    );

  toggleModal("editProductModal");
}

function submitEditProduct() {
  const productId = document.getElementById("editProductId").value;
  const barcode = document.getElementById("editProductBarcode").value;
  const productName = document.getElementById("editProductName").value;
  const price = document.getElementById("editProductPrice").value;
  const quantity = document.getElementById("editProductQuantity").value;
  const storeId = document.getElementById("editProductStore").value;
  const errorDiv = document.getElementById("editProductError");
  const errorText = document.getElementById("editProductErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  // Validate
  if (!barcode || !productName || !price || !quantity || !storeId) {
    if (errorDiv && errorText) {
      errorText.textContent = "Please fill in all required fields";
      errorDiv.classList.remove("hidden");
    }
    return;
  }

  console.log("[Edit Product] Updating product:", {
    productId,
    barcode,
    productName,
    price,
    quantity,
    storeId,
  });

  const formData = new FormData();
  formData.append("product_id", productId);
  formData.append("barcode", barcode);
  formData.append("product_name", productName);
  formData.append("price", price);
  formData.append("quantity", quantity);
  formData.append("store_id", storeId);

  fetch("../../php/handlers/updateProductHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[Edit Product] Update response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Product Updated";
        document.getElementById("successMessage").textContent =
          `Product "${productName}" updated successfully`;
        document.getElementById("successEmail").textContent = data.data.barcode;
        document.getElementById("successRole").textContent =
          "Qty: " + quantity + " - ₱" + parseFloat(price).toLocaleString();
        document.getElementById("successUserId").textContent =
          "SKU: " + data.data.sku;
        document
          .getElementById("successStoreContainer")
          .classList.add("hidden");

        window.isEditOperation = true;

        toggleModal("editProductModal");
        toggleModal("successModal");

        // Reload page after modal closes
        setTimeout(() => {
          location.reload();
        }, 2000);
      } else {
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[Edit Product] Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

// ========================
// Delete Product Functions
// ========================

function openDeleteProductModal(productId) {
  console.log("[Delete Product] Opening delete modal for product:", productId);

  let product = allProducts.find((p) => p.product_id == productId);

  // If product not found in allProducts, fetch it from database
  if (!product) {
    console.warn(
      "[Delete Product] Product not found in allProducts, fetching from database",
    );
    fetch(
      `../../php/handlers/getProductByIdHandler.php?product_id=${productId}`,
    )
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          product = data.data;
          populateDeleteModal(productId, product.product_name);
        } else {
          console.error("[Delete Product] Failed to fetch product");
          document
            .getElementById("deleteProductError")
            .classList.remove("hidden");
          document.getElementById("deleteProductErrorText").textContent =
            "Product not found";
        }
      })
      .catch((error) => {
        console.error("[Delete Product] Fetch error:", error);
        document
          .getElementById("deleteProductError")
          .classList.remove("hidden");
        document.getElementById("deleteProductErrorText").textContent =
          "Error fetching product";
      });
  } else {
    populateDeleteModal(productId, product.product_name);
  }
}

function populateDeleteModal(productId, productName) {
  document.getElementById("deleteProductId").value = productId;
  document.getElementById("deleteProductName").textContent = productName;
  document.getElementById("deleteProductError").classList.add("hidden");

  toggleModal("deleteProductModal");
}

function confirmDeleteProduct() {
  const productId = document.getElementById("deleteProductId").value;
  const errorDiv = document.getElementById("deleteProductError");
  const errorText = document.getElementById("deleteProductErrorText");

  // Clear previous errors
  if (errorDiv) {
    errorDiv.classList.add("hidden");
  }

  console.log("[Delete Product] Deleting product:", productId);

  const formData = new FormData();
  formData.append("product_id", productId);

  fetch("../../php/handlers/deleteProductHandler.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      console.log("[Delete Product] Delete response:", data);
      if (data.status === "success") {
        // Show success modal
        document.getElementById("successHeader").textContent =
          "Product Deleted";
        document.getElementById("successMessage").textContent =
          "Product has been deleted successfully";
        document
          .getElementById("successDetails")
          .classList.add("hidden");

        // Set flag to reload page when modal is closed
        shouldReloadPage = true;

        toggleModal("deleteProductModal");
        toggleModal("successModal");
      } else {
        if (errorDiv && errorText) {
          errorText.textContent = "Error: " + data.message;
          errorDiv.classList.remove("hidden");
        }
      }
    })
    .catch((error) => {
      console.error("[Delete Product] Error:", error);
      if (errorDiv && errorText) {
        errorText.textContent = "An error occurred: " + error.message;
        errorDiv.classList.remove("hidden");
      }
    });
}

// Load products when page is ready
document.addEventListener("DOMContentLoaded", function () {
  console.log("[Products] DOM loaded, initializing products...");

  // Check if we're on the products page
  if (document.getElementById("productTable")) {
    loadProducts();
  }
});
