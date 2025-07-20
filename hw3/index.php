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
                    Sign in to start your session
                </div>
                <div class = "mx-3 my-3">
                    <form method = "POST" action = "./function/login.php">
                        <input type = "email" name = "email" placeholder = "Email" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <input type = "password" name = "password" placeholder = "Password" class = "w-full border-b-2 border-gray-300 py-2 px-2 my-2" required>
                        <button type = "submit" class = "w-full bg-purple-500 hover:bg-purple-700 text-white py-2 px-2 my-2">Sign in</button>
                        <a href = "./register.php" class = "text-purple-500">尚未註冊?點此建立</a>
                    </form>
                </div>
                <?php
                    if(isset($_SESSION["error"]))
                    {
                        echo "<div class = 'w-full bg-red-500/60 py-2'><div class = 'text-center'>".$_SESSION["error"]."</div></div>";
                        unset($_SESSION["error"]);
                    }
                ?>
            </div>
        </div> 
    </body>
</html>
