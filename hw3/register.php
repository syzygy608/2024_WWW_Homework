<?php
    session_start();
    if(isset($_SESSION["email"]))
    {
        header("Location: ./home.php");
    }
?>

<!DOCTYPE html>
<html>
    <?php   
        require_once  "./template/head.php";
    ?>
    <body>
        <div class = "bg-gradient-to-tr from-purple-300 to-green-300 h-[100vh] flex flex-col justify-center items-center align-middle">
            <div class = "title text-3xl mb-6 font-extrabold">
                Voting System
            </div>
            <div class = "w-[30rem] mx-auto bg-white rounded-lg">
                <div class = "mx-auto text-gray-400 py-3 text-center text-medium">
                    Create a new account
                </div>
                <div class = "mx-3 my-3">
                    <form method = "POST" action = "./function/register.php">
                        <input type = "text" name = "username" placeholder = "Userame" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <input type = "text" name = "email" placeholder = "Email" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <input type = "password" name = "password" placeholder = "Password" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <input type = "password" name = "confirm_password" placeholder = "Confirm Password" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <button type = "submit" class = "w-full bg-purple-500 hover:bg-purple-700 text-white py-2 px-2 my-2">Register</button>
                        <a href = "./index.php" class = "text-purple-500">已經有帳號了嗎?點此登入</a>
                    </form>
                </div>
                <?php
                    if(isset($_SESSION["error"]))
                    {
                        echo "<div class = 'w-full bg-red-500/60 py-6'><div class = 'text-center'>".$_SESSION["error"]."</div></div>";
                        unset($_SESSION["error"]);
                    }
                ?>
            </div>
        </div> 
    </body>
</html>
