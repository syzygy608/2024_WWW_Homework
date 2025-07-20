<div class = "hidden z-30 top-0 left-0 w-screen h-screen overflow-auto fixed items-center backdrop-blur-sm" id = "add_poll">
    <div class = "h-6/12 mx-auto bg-white px-3 py-2 rounded-lg drop-shadow-xl">
        <form class = "mx-auto w-[30rem] h-full flex flex-col py-3" method = "POST" action = "./function/add_poll.php">
            <p class = "font-bold text-2xl border-b pb-3">
                Add New Topics
            </p> 
            <div class = "flex flex-col items-center">
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Topics</p>
                    </div>
                    <div class = "basis-3/4">
                        <select class = "w-full text-center mb-2 p-2 border-2 border-gray-300 rounded-lg" name = "topic">
                            <?php
                                $topics = get_topics();
                                echo "<option selected value = '0'>Select Topic</option>";
                                for($i = 0; $i < count($topics); $i++)
                                {
                                    echo "<option value = '".$topics[$i]["topic_name"]."'>".$topics[$i]["topic_name"]."</option>";
                                }
                                // add new topic with text input
                            ?>
                        </select>
                        <?php
                            echo "<input type = 'text' class = 'w-full p-2 border-2 border-gray-300 rounded-lg' name = 'new_topic' placeholder = 'Add New Topic'>";
                        ?>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Title</p>
                    </div>
                    <div class = "basis-3/4">
                        <input type = "text" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "title" required/>
                    </div>
                </div>
                <div class = "w-full flex flex-row items-center m-2">
                    <div class = "basis-1/4">
                        <p class = "text-lg mx-3">Options</p>
                    </div>
                    <div class = "basis-3/4" id = "option_list">
                        <input type = "text" class = "w-full p-2 border-2 border-gray-300 rounded-lg" name = "option[]" required/>
                        <button type = "button" class = "mt-3 w-32 bg-gradient-to-tr from-purple-300 to-green-300 p-2 rounded-lg text-white font-bold hover:bg-gradient-to-tl"
                            onclick = "add_option()"
                        >
                            Add Option
                        </button>
                    </div>
                </div>
            </div>
            <div class = "flex w-full place-content-between">
                <button type = "reset" class = "px-6 py-2 bg-purple-400 hover:bg-purple-600 hover:text-white text-base duration-200 rounded-2xl" onclick = "show_add_poll()">
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
    function show_add_poll()
    {
        var add_poll = document.getElementById("add_poll");
        if(add_poll.classList.contains("hidden"))
        {
            add_poll.classList.remove("hidden");
            add_poll.classList.add("flex");
        }
        else
        {
            add_poll.classList.remove("flex");
            add_poll.classList.add("hidden");
            // clear option list
            var option_list = document.getElementById("option_list");
            while(option_list.children.length > 2)
                option_list.removeChild(option_list.children[option_list.children.length - 2]);
        }
    }
    function add_option()
    {
        var option = document.createElement("input");
        option.type = "text";
        option.className = "w-full p-2 border-2 border-gray-300 rounded-lg";
        option.name = "option[]";
        option.required = true;
        var add_poll = document.getElementById("option_list");
        add_poll.insertBefore(option, add_poll.children[add_poll.children.length - 1]);
    }
</script>