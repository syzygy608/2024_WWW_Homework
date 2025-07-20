<?php
    session_start();
    if(!isset($_SESSION["email"]))
    {
        header("Location: ./index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <?php  
        require_once  "./template/head.php";
    ?>
    <body>
        <?php
            require_once "./template/nav.php";
            require_once "./template/poll_add.php";
            require_once "./template/poll_edit.php";
        ?>
        <div class = "flex w-full flex-row" id = "contain">
            <div class = "w-2/12 bg-purple-800" id = "menu">
                <div class = "text-center py-2 bg-purple-500 w-full">
                    <?php
                        if($result["photo"] != null)
                            echo "<img src = './upload/".$result["photo"]."' class = 'w-24 h-24 rounded-full mx-auto'>";
                        else
                            echo "<img src = './upload/default.jpg' class = 'w-24 h-24 rounded-full mx-auto'>";
                        echo "<div class = 'text-white font-bold text-medium'>".$result["username"]."</div>";
                    ?>
                </div>
                <button class = "w-full py-2 text-white font-bold hover:bg-purple-700" onclick = "window.location.href = './home.php'">
                    Home
                </button>
                <button class = "w-full py-2 text-white font-bold hover:bg-purple-700" onclick = "window.location.href = './poll.php'">
                    Poll Page
                </button>
            </div>
            <div id = "content" class = "w-10/12 bg-slate-300 flex flex-col align-middle">
                <div class = "text-2xl font-bold p-3">
                    Poll Page
                </div>
                <div class = "w-full flex items-center justify-center">
                    <div class = "flex flex-col w-full px-6">
                        <!-- error message -->
                        <?php
                            if(isset($_SESSION["error"]))
                            {
                                echo "<div class = 'bg-red-400 m-3 text-white p-3 rounded-lg text-center'>".$_SESSION["error"]."</div>";
                                unset($_SESSION["error"]);
                            }
                        ?>
                        <button class = "w-32 bg-gradient-to-tr from-purple-300 to-green-300 p-3 rounded-lg text-white font-bold hover:bg-gradient-to-tl shadow-lg"
                            onclick="show_add_poll()"
                        >
                            + 新增投票
                        </button>
                        <div class = "flex items-center place-content-between align-middle pt-3">
                            <div class = "flex items-center align-middle">
                                <p>顯示最新的</p>
                                <select class = "bg-white border-2 border-gray-300 rounded-lg p-2 mx-3" name = "limit" onchange="window.location.href = './poll.php?limit=' + document.getElementsByName('limit')[0].value">
                                    <?php
                                        $limit = 10;
                                        if(isset($_GET["limit"]))
                                            $limit = intval($_GET["limit"]);
                                        echo "<option value = '10' ".($limit == 10 ? "selected" : "").">10</option>";
                                        echo "<option value = '20' ".($limit == 20 ? "selected" : "").">20</option>";
                                        echo "<option value = '30' ".($limit == 30 ? "selected" : "").">30</option>";
                                    ?>
                                </select>
                                <p>筆投票</p>
                            </div>
                            <div>
                                <input type = "search" placeholder = "Search" class = "bg-white border-2 border-gray-300 rounded-lg p-2" name = "keyword">
                                <button class = "bg-gradient-to-tr from-purple-300 to-green-300 p-2 rounded-lg text-white font-bold hover:bg-gradient-to-tl" onclick="window.location.href = './poll.php?keyword=' + document.getElementsByName('keyword')[0].value">Search</button>
                            </div>
                        </div>
                        <table class = "p-3 mt-3 table-fixed" id = "poll_list">
                            <thead class = "text-center rounded-lg">
                                <tr>
                                    <td class = "py-2">Topics</td>
                                    <td class = "py-2">Titles</td>
                                    <td class = "py-2">Create Time</td>
                                    <td class = "py-2 w-40">Tool</td>
                                </tr>
                            </thead>
                            <tbody class = "text-center bg-green-100/50 rounded-lg">
                                <!-- show the poll list belong to the user -->
                                <?php
                                $count = 10;
                                if(isset($_GET["limit"]))
                                    $count = intval($_GET["limit"]);
                                
                                if(isset($_GET["keyword"]))
                                    $result = get_polls_based_on_keyword($_SESSION["email"], "%".$_GET["keyword"]."%", $count);
                                else
                                    $result = get_polls($_SESSION["email"], $count);
                                for($i = 0; $i < count($result); $i++)
                                {
                                    echo "<tr class = 'text-center h-[4rem]'>";
                                    echo "<td class = 'py-2'>".$result[$i]["topic_name"]."</td>";
                                    echo "<td class = 'py-2'>".$result[$i]["title"]."</td>";
                                    echo "<td class = 'py-2'>".$result[$i]["created_time"]."</td>";
                                    echo "<td class = 'py-2 flex justify-between w-40 px-5'>";
                                    echo "<a href = './poll.php?id=" . $result[$i]["id"] . "' class = 'bg-purple-400 p-2 rounded-lg text-white font-bold hover:bg-purple-600'>Edit</a>";
                                    echo "<a href = './function/delete_poll.php?id=" . $result[$i]["id"] . "' class = 'bg-red-400 p-2 rounded-lg text-white font-bold hover:bg-red-600'>Delete</a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
