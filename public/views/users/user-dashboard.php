<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../../../index.php');
    exit;
}

// Ensure only non-admin users can access
if (isset($_SESSION['role']) && $_SESSION['role'] == 0) {
    header('Location: ../admin/admin-dashboard.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Smart POS – User Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="../../css/admin-dashboard.css">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              cream: "#FBF8F3",
              gold: "#DD9F52",
              green: "#4B774E",
              redsoft: "#E97171",
            },
          },
        },
      };
    </script>
  </head>

  <body class="bg-cream min-h-screen flex">
    <script src="../../js/admin-dashboard.js"></script>
    
    <!-- SIDEBAR -->
    <aside class="w-64 bg-white shadow-lg p-6">
      <h2 class="text-2xl font-bold text-green mb-8">Smart POS</h2>
      <nav class="space-y-2 text-sm font-medium">
        <button onclick="showPage('dashboard', this)" class="nav-item active">
          Dashboard
        </button>
        <button onclick="showPage('products', this)" class="nav-item">
          Products
        </button>
        <button onclick="showPage('sales', this)" class="nav-item">
          POS / Sales
        </button>
      </nav>
    </aside>

    <!-- MOBILE NAVIGATION -->
    <div id="mobileNav" class="mobile-nav">
      <div class="mobile-nav-menu">
        <h2 class="text-2xl font-bold text-green mb-8">Smart POS</h2>
        <nav class="space-y-2 text-sm font-medium">
          <button onclick="showPage('dashboard', this); toggleMobileNav()" class="nav-item active w-full">
            Dashboard
          </button>
          <button onclick="showPage('products', this); toggleMobileNav()" class="nav-item w-full">
            Products
          </button>
          <button onclick="showPage('sales', this); toggleMobileNav()" class="nav-item w-full">
            POS / Sales
          </button>
        </nav>
      </div>
    </div>

    <!-- MAIN -->
    <main class="flex-1 p-8">
      <!-- HEADER -->
      <header
        class="bg-white rounded-xl shadow-sm px-6 py-4 mb-8 flex justify-between items-center"
      >
        <div class="flex items-center gap-4">
          <button onclick="toggleMobileNav()" class="hamburger" id="hamburger">
            <span></span>
            <span></span>
            <span></span>
          </button>
          <h1 id="pageTitle" class="text-2xl font-bold text-green">Dashboard</h1>
        </div>

        <div class="relative">
          <button onclick="toggleUserMenu()" class="flex items-center gap-3">
            <div
              class="w-10 h-10 rounded-full bg-gold flex items-center justify-center text-white font-bold"
            >
              <?php echo strtoupper(substr($_SESSION['email'], 0, 1)); ?>
            </div>
          </button>

          <div
            id="userMenu"
            class="hidden absolute right-0 mt-3 w-40 bg-white rounded-xl shadow-lg border z-50"
          >
            <div class="px-4 py-3 text-sm text-gray-600 border-b">
              <?php echo htmlspecialchars($_SESSION['email']); ?>
            </div>
            <a
              href="../../php/handlers/logoutHandler.php"
              class="block px-4 py-3 text-sm text-redsoft hover:bg-redsoft/10"
              >Logout</a
            >
          </div>
        </div>
      </header>

      <!-- DASHBOARD -->
      <div id="dashboard" class="page">
        <?php include './layouts/dashboard.php'; ?>
      </div>

      <!-- PRODUCTS -->
      <div id="products" class="page hidden">
        <?php include './layouts/products.php'; ?>
      </div>

      <!-- SALES / POS -->
      <div id="sales" class="page hidden">
        <?php include './layouts/sales.php'; ?>
      </div>
    </main>

    <!-- MODALS -->
    <?php include './modals/success-modal.php'; ?>
    <?php include './modals/product-modal.php'; ?>
    <?php include './modals/edit-product-modal.php'; ?>
    <?php include './modals/delete-product-modal.php'; ?>
    <?php include './modals/barcode-scanner-modal.php'; ?>

    <script>
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

      function toggleMobileNav() {
        const mobileNav = document.getElementById("mobileNav");
        const hamburger = document.getElementById("hamburger");
        mobileNav.classList.toggle("active");
        hamburger.classList.toggle("active");
      }
    </script>
  </body>
</html>
