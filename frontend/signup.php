<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Department Monitoring System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex justify-center items-center">

    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Sign Up</h2>

        <?php if (isset($_GET['msg'])): ?>
            <div class="<?= (isset($_GET['type']) && $_GET['type'] === 'error') ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?> border px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <form action="../backend/auth_register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 border rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition duration-200">
                Sign Up
            </button>
        </form>

        <p class="mt-4 text-center text-gray-600 text-sm">
            Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>

</body>
</html>
