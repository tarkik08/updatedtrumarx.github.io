<?php
session_start();

// Check if already logged in
if(isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true){
    header("location: index.php");
    exit;
}

require_once "../db_config.php";

$username = $password = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Sanitize
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    if(empty($username)){
        $username_err = "Please enter username.";
    }
    
    if(empty($password)){
        $password_err = "Please enter your password.";
    }
    
    if(empty($username_err) && empty($password_err)){
        // Prepare select statement
        $sql = "SELECT id, username, password FROM admin_users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, start session
                            session_start();
                            $_SESSION["admin_loggedin"] = true;
                            $_SESSION["admin_id"] = $id;
                            $_SESSION["admin_username"] = $username;                            
                            
                            header("location: index.php");
                        } else{
                            $password_err = "Invalid password.";
                        }
                    }
                } else{
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Trumarx</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }</style>
</head>
<body class="flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md overflow-hidden">
        <div class="bg-black py-4 text-center">
            <h1 class="text-white text-xl font-bold">Trumarx Admin</h1>
        </div>
        
        <div class="p-8">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Login to Dashboard</h2>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($username_err)) ? 'border-red-500' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $username_err; ?></span>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>">
                    <span class="text-red-500 text-xs italic"><?php echo $password_err; ?></span>
                </div>
                
                <div class="flex items-center justify-center">
                    <button class="bg-black hover:bg-gray-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit">
                        Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
