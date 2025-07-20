<div class = "hidden z-30 top-0 left-0 w-screen h-screen overflow-auto fixed items-center backdrop-blur-sm" id = "edit_poll">
    <div class = "h-6/12 mx-auto bg-white px-3 py-2 rounded-lg drop-shadow-xl">
        <form class = "mx-auto w-[30rem] h-full flex flex-col py-3" method = "POST" action = "./function/edit_poll.php">
            <p class = "font-bold text-2xl border-b pb-3">
                Edit
            </p> 
            <div class = "flex flex-col items-center">
                <div class = "w-full flex flex-row items-center m-2">
                    <input type = "hidden" name = "id" value="<?php echo $_GET["id"]; ?>"/>
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Topics</p>
                    </div>
                    <div class = "basis-3/4">
                        <select class = "w-full text-center mb-2 p-2 border-2 border-gray-300 rounded-lg" name = "topic">
                            <?php
                                $poll = get_specific_poll($_GET["id"]);
                                $topics = get_topics();
                                for($i = 0; $i < count($topics); $i++)
                                {
                                    if($topics[$i]["topic_name"] == $poll["topic_name"])
                                        echo "<option selected>".$topics[$i]["topic_name"]."</option>";
                                    else
                                        echo "<option>".$topics[$i]["topic_name"]."</option>";
                                }
                                // add new topic with text input
                            ?>
                        </select>
                        <?php
                            echo "<input type = 'text' class = 'w-full p-2 border-2 border-gray-300 rounded-lg' name = 'new_topic' placeholder = 'Use New Topic'>";
                        ?>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Title</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "text" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "title" value="<?php echo $poll["title"]; ?>" required/>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Options</p>
                    </div>
                    <div class = "basis-3/4" id = "option_list_edit">
                        <?php
                            $options = get_options($_GET["id"]);
                            for($i = 0; $i < count($options); $i++)
                            {
                                echo "<div class = 'flex flex-row items-center m-2'>";
                                echo "<input type = 'text' class = 'w-full p-2 border-2 border-gray-300 rounded-lg' name = 'option[]' value = '".$options[$i]["item_name"]."' disabled/>";
                                echo "</div>";
                            }
                        ?>
                        <button type = "button" class = "mt-3 w-32 bg-gradient-to-tr from-purple-300 to-green-300 p-2 rounded-lg text-white font-bold hover:bg-gradient-to-tl"
                            onclick = "add_new_option()"
                        >
                            Add Option
                        </button>
                    </div>
                </div>
            </div>
            <div class = "flex w-full place-content-between">
                <button type = "reset" class = "px-6 py-2 bg-purple-400 hover:bg-purple-600 hover:text-white text-base duration-200 rounded-2xl" onclick = "remove_id()">
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
    // check if the link contains id
    if(window.location.href.includes("id"))
    {
        var edit_poll = document.getElementById("edit_poll");
        edit_poll.classList.remove("hidden");
        edit_poll.classList.add("flex");
    }
    function add_new_option()
    {
        var option_list = document.getElementById("option_list_edit");
        var new_option = document.createElement("div");
        new_option.classList.add("flex");
        new_option.classList.add("flex-row");
        new_option.classList.add("items-center");
        new_option.classList.add("m-2");
        new_option.innerHTML = "<input type = 'text' class = 'w-full p-2 border-2 border-gray-300 rounded-lg' name = 'option[]' required/>";
        // on the button click, add new option above the button
        option_list.insertBefore(new_option, option_list.children[option_list.children.length - 1]);
    }
    function remove_id()
    {
        var edit_poll = document.getElementById("edit_poll");
        edit_poll.classList.remove("flex");
        edit_poll.classList.add("hidden");
        // clear option list
        var option_list = document.getElementById("option_list_edit");
        while(option_list.children.length > 2)
            option_list.removeChild(option_list.children[option_list.children.length - 2]);
        window.location.href = "./poll.php";
    }
</script>