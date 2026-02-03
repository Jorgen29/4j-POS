<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Smart POS – Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

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

      <!-- Form (Design Only) -->
      <form class="space-y-5">
        <!-- Email -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
          </label>
          <input
            type="email"
            placeholder="admin@store.com"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold"
          />
        </div>

        <!-- Password -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <input
            type="password"
            placeholder="••••••••"
            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gold focus:border-gold"
          />
        </div>

        <!-- Error State (UI Only) -->
        <!--
            <div class="bg-redsoft/10 text-redsoft text-sm px-4 py-2 rounded-lg">
                Invalid email or password.
            </div>
            -->

        <!-- Login Button -->
        <button
          type="button"
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
  </body>
</html>
