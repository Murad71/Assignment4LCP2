<?php
session_start();

$storage_file = 'users.json';


if (file_exists($storage_file)) {
    $json_data = file_get_contents($storage_file);
    $users = json_decode($json_data, true);
} else {
    $users = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
      
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

     
        if (empty($name) || empty($email) || empty($password)) {
            $error = "Please fill all fields.";
        } else {
            // Check if email already exists
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    $error = "Email already registered.";
                    break;
                }
            }

            if (!isset($error)) {
                
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $users[] = [
                    'name' => $name,
                    'email' => $email,
                    'password_hash' => $password_hash
                ];

                file_put_contents($storage_file, json_encode($users));

                header("Location: login.php");
                exit();
            }
        }
    } elseif (isset($_POST['login'])) {
   
        $email = $_POST['email'];
        $password = $_POST['password'];

   
        if (empty($email) || empty($password)) {
            $error = "Please fill all fields.";
        } else {
           
            $login_successful = false;
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    if (password_verify($password, $user['password_hash'])) {
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_name']= $user['name'];
                        header("Location: customer/dashboard.php"); // Redirect to a dashboard or another page
                        exit();
                    } else {
                        $error = "Invalid email or password.";
                    }
                    $login_successful = true;
                    break;
                }
            }

            if (!$login_successful) {
                $error = "Invalid email or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html class="h-full bg-white" lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
      * {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
          'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
          'Helvetica Neue', sans-serif;
      }
    </style>
    <title>Sign In or Register</title>
</head>
<body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900">
                Sign In or Register
            </h2>
        </div>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
                <form class="space-y-6" method="POST">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                        <div class="mt-2">
                            <input id="name" name="name" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6 p-2" />
                        </div>
                    </div>

                    <?php if (isset($error)): ?>
                        <p class="text-red-500"><?php echo htmlspecialchars($error); ?></p>
                    <?php endif; ?>

                    <div>
                        <button type="submit" name="register" class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            Register
                        </button>
                        <button type="submit" name="login" class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 mt-2">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
            <p class="mt-10 text-sm text-center text-gray-500">
                <a href="./register.html" class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500">Register</a> or
                <a href="./login.html" class="font-semibold leading-6 text-emerald-600 hover:text-emerald-500">Sign-in</a>
            </p>
        </div>
    </div>
</body>
</html>
