<nav class = "bg-purple-400/80 py-3 px-3 h-[4rem] flex justify-between items-center">
    <div class = "flex justify-between items-center w-2/12">
        <a href = "./home.php" class = "text-white font-bold text-xl">Voting System</a>
        <button onclick = "collapse_menu()">
            <i class = "fa fa-bars text-white"></i>
        </button>
    </div>
    
    <div class = "flex justify-between items-center relative z-0">
        <?php
            require_once "./lib/base.php";
            $result = get_user_info($_SESSION["email"]);

            echo "<div class = 'text-white flex items-center justify-center cursor-pointer' onclick='toggleInfo()'>";
            if($result["photo"] != null)
                echo "<img src = './upload/".$result["photo"]."' class = 'w-8 h-8 rounded-full'>";
            else
                echo "<img src = './upload/default.jpg' class = 'w-8 h-8 rounded-full'>";
            echo "<div class = 'ms-2 text-white'>".$result["username"]."</div>";
            echo "</div>";
        ?>
        <div id = "info" class = "w-[20rem] hidden absolute top-full right-0 mt-6 shadow-lg z-10" style = "display: none;">
            <div class = "text-center py-2 bg-purple-500 w-[20rem]">
                <?php
                    if($result["photo"] != null)
                        echo "<img src = './upload/".$result["photo"]."' class = 'w-24 h-24 rounded-full mx-auto'>";
                    else
                        echo "<img src = './upload/default.jpg' class = 'w-24 h-24 rounded-full mx-auto'>";
                    echo "<div class = 'text-white font-bold text-medium'>".$result["username"]."</div>";
                ?>
            </div>
            <div class = "text-center bg-white px-6 py-2 flex justify-between">
                <button class = "px-3 py-1 bg-gray-300 text-black hover:bg-purple-400" onclick = "update()">Update</button>
                <a class = "px-3 py-1 text-black bg-gray-300 hover:bg-purple-400" href = "./function/logout.php" >Sign out</a>
            </div>
        </div>
    </div>
</nav>

<div class = "hidden z-30 top-0 left-0 w-screen h-screen fixed items-center backdrop-blur-sm" id = "update">
    <div class = "h-6/12 mx-auto bg-white px-3 py-2 rounded-lg drop-shadow-xl">
        <form class = "mx-auto w-[30rem] h-full flex flex-col py-3" method = "POST" action = "./function/update.php" enctype = "multipart/form-data">
            <p class = "font-bold text-2xl border-b pb-3">
                User Profile
            </p> 
            <div class = "flex flex-col items-center text-center">
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Username</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "text" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "username"/>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Password</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "password" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "password"/>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Photo</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "file" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "photo" accept = "image/*"/>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Current Password</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "password" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "current_password"/>
                    </div>

                </div>

            </div>
            <div class = "flex w-full place-content-between">
                <button type = "reset" class = "px-6 py-2 bg-purple-400 hover:bg-purple-600 hover:text-white text-base duration-200 rounded-2xl" onclick = "update()">
                    取消
                </button>
                <button type = "submit" class = "px-6 py-2 bg-green-400 hover:bg-green-600 hover:text-white text-base duration-200 rounded-2xl">
                    確認
                </button>
            </div>
        </form>
    </div>
</div>      

<script>
    function toggleInfo()
    {
        var info = document.getElementById("info");
        if(info.style.display === "none")
            info.style.display = "block";
        else
            info.style.display = "none";
    }
    function update()
    {
        var update = document.getElementById("update");
        if(update.classList.contains("hidden"))
        {
            update.classList.remove("hidden");
            update.classList.add("flex");
        }
        else
        {
            update.classList.remove("flex");
            update.classList.add("hidden");
        }
    }
    function collapse_menu()
    {
        var menu = document.getElementById("menu");
        var content = document.getElementById("content");
        
        if(menu.style.display === "none")
        {
            menu.style.display = "block";
            content.classList.remove("w-full");
            content.classList.add("w-10/12");

        }
        else
        {
            menu.style.display = "none";
            content.classList.remove("w-10/12");
            content.classList.add("w-full");
        }
    }
</script>