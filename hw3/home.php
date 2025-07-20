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
            <div id = "content" class = "w-10/12 bg-slate-300 flex flex-col  align-middle">
                <div class = "text-2xl font-bold p-3">
                    Welcome to Voting System
                </div>
                <div class = "w-full flex items-center justify-center">
                        <?php 
                            $user = user_count();
                            $poll = poll_count();
                        ?>
                    <div class = "w-[30rem] bg-gradient-to-tr from-purple-300 to-green-300 m-2 p-2 rounded-lg shadow-lg">
                        <div class = "text-center text-2xl font-bold">
                            User Number
                        </div>
                        <div class = "text-center text-xl"><?php echo $user[0]; ?></div>
                    </div>
                    <div class = "w-[30rem] bg-gradient-to-tr from-purple-300 to-green-300 m-2 p-2 rounded-lg shadow-lg">
                        <div class = "text-center text-2xl font-bold">
                            Poll Number
                        </div>
                        <div class = "text-center text-xl"><?php echo $poll[0]; ?></div>
                    </div>
                </div>
                <div class = "flex flex-wrap items-start justify-center">
                    <?php
                        $poll_list = list_polls();
                        foreach($poll_list as $poll)
                        {
                            echo "<div class = 'basis-1/2 w-full p-3 flex justify-center h-[20rem]'>";
                            echo "<div class = 'bg-white rounded-lg shadow-lg p-3 w-9/12 overflow-auto'>";
                            echo "<div class = 'flex items-start justify-between'>";
                            echo "<div class = 'flex flex-col'>";
                            echo "<div class = 'text-xl font-bold'>".$poll["topic_name"]."</div>";
                            echo "<div class = 'text-lg'>".$poll["title"]."</div>";
                            echo "</div>";
                            echo "<div class = 'flex flex-col'>";
                            // owner photo and name
                            echo "<div class = 'flex items-center'>";
                            echo "<div class = 'w-8 h-8 rounded-full'>";
                            $owner = get_user_info($poll["owner"]);
                            if($owner["photo"] != null)
                                echo "<img src = './upload/".$owner["photo"]."' class = 'w-full h-full rounded-full'>";
                            else
                                echo "<img src = './upload/default.jpg' class = 'w-full h-full rounded-full'>";
                            echo "</div>";
                            echo "<div class = 'text-medium px-2'>".$owner["username"]."</div>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                            echo "<div class = 'flex flex-col'>";
                            
                            $options = get_options($poll["id"]);
                            $total = 0;
                            foreach($options as $option)
                            {
                                $total += $option["vote_count"];
                            }
                            if($total == 0)
                            $total = 1;
                            $the_user = get_user_info($_SESSION["email"]);
                            // 檢查是否已經投過票
                            $conn = getDB();
                            $stmt = $conn->prepare("SELECT * FROM vote_record WHERE username = ? AND topic_id = ?");
                            $stmt->bindParam(1, $the_user["username"], PDO::PARAM_STR);
                            $stmt->bindParam(2, $poll["id"], PDO::PARAM_INT);
                            $stmt->execute();
                            $result = $stmt->fetch();
                            $the_option_that_user_voted = null;
                            if($result != null)
                            {
                                $the_option_that_user_voted = $result["option_id"];
                            }

                            foreach($options as $option)
                            {
                                if($the_option_that_user_voted == $option["id"])
                                {
                                    echo "<a href = './function/vote.php?topic_id=".$poll["id"]."&option_id=".$option["id"]."' class = 'bg-green-200 m-2 rounded-lg hover:bg-green-300'>";
                                    echo "<div class = 'text-medium p-2'>".$option["item_name"]." - 票數: " . $option["vote_count"]. " (得票率: ".round($option["vote_count"]/$total*100, 2)."% )</div>";
                                    echo "</a>";
                                }
                                else
                                {
                                    echo "<a href = './function/vote.php?topic_id=".$poll["id"]."&option_id=".$option["id"]."' class = 'bg-gray-200 m-2 rounded-lg hover:bg-gray-300'>";
                                    echo "<div class = 'text-medium p-2'>".$option["item_name"]." - 票數: " . $option["vote_count"]. " (得票率: ".round($option["vote_count"]/$total*100, 2)."% )</div>";
                                    echo "</a>";
                                }
                            }

                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>