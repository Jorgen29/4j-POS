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
