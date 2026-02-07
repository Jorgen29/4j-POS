<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Smart POS – Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />

    <!-- Tailwind Color Palette -->
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

  <body class="bg-cream min-h-screen flex items-center justify-center">
    <!-- Login Card -->
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-green">Smart POS</h1>
        <p class="text-sm text-gray-500 mt-2">
          Camera-Based Point of Sale System
        </p>
      </div>

      <!-- Error Message -->
      <div id="errorMessage" class="hidden bg-redsoft/10 text-redsoft text-sm px-4 py-2 rounded-lg mb-4">
        <p id="errorText"></p>
      </div>

      <!-- Form -->
      <form class="space-y-5" method="POST" action="public/php/handlers/loginHandler.php">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
          </label>
          <input
            id="email"
            type="email"
            name="email"
            placeholder="admin@store.com"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold"
            required
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <div class="relative">
            <input
              id="password"
              type="password"
              name="password"
              placeholder="••••••••"
              class="w-full px-4 py-3 pr-10 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold"
              required
            />
            <button
              type="button"
              onclick="togglePasswordVisibility()"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <span class="material-icons text-sm" id="passwordIcon">visibility</span>
            </button>
          </div>
        </div>

        <!-- Login Button -->
        <button
          type="submit"
          class="w-full bg-gold text-white py-3 rounded-lg font-semibold hover:bg-green transition duration-200 shadow-md"
        >
          Sign In
        </button>
      </form>

      <!-- Footer -->
      <div class="text-center mt-6 text-sm text-gray-500">
        © 2026 Smart POS System
      </div>
    </div>

    <script>
      function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        
        if (passwordInput.type === 'password') {
          passwordInput.type = 'text';
          icon.innerText = 'visibility_off';
        } else {
          passwordInput.type = 'password';
          icon.innerText = 'visibility';
        }
      }

      // Check if there's an error message from the server
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('error')) {
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const errorCode = urlParams.get('error');
        
        if (errorCode === 'invalid') {
          errorText.textContent = 'Invalid email or password.';
        } else if (errorCode === 'empty') {
          errorText.textContent = 'Email and password are required.';
        } else {
          errorText.textContent = 'An error occurred. Please try again.';
        }
        
        errorMessage.classList.remove('hidden');
      }
    </script>
  </body>
</html>
