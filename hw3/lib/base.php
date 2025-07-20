<?php
// some basic functions
function getDB()
{
    include_once "config.php";
    // config in lib/config.php
    $host = HOST;
    $user = USER;
    $port = PORT;
    $password = PASSWORD;
    $database = DATABASE;
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$database", $user, $password);

    if(!$conn)
    {
        echo "Failed to connect to database";
        exit();
    }
    return $conn;
}

function get_user_info($email)
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindParam(1, $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}

function user_count()
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}

function poll_count()
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM topics");
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}
function get_topics()
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT DISTINCT topic_name FROM topics");
    $stmt->execute();
    $result = $stmt->fetchAll();

    return $result;
}

function get_polls($user_email, $count)
{
    $conn = getDB();
    // check if ther user is admin
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindParam(1, $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();

    if($result["username"] == "admin")
    {
        $stmt = $conn->prepare("SELECT * FROM topics LIMIT ?");
        $stmt->bindParam(1, $count, PDO::PARAM_INT);
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM topics WHERE owner = ? LIMIT ?");
        $stmt->bindParam(1, $user_email, PDO::PARAM_STR);
        $stmt->bindParam(2, $count, PDO::PARAM_INT);
    }
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

function get_polls_based_on_keyword($user_email, $keyword, $count)
{
    $conn = getDB();
    // check if ther user is admin
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bindParam(1, $user_email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();
    if($result["username"] == "admin")
    {
        $stmt = $conn->prepare("SELECT * FROM topics WHERE title LIKE ? OR topic_name LIKE ? LIMIT ?");
        $stmt->bindParam(1, $keyword, PDO::PARAM_STR);
        $stmt->bindParam(2, $keyword, PDO::PARAM_STR);
        $stmt->bindParam(3, $count, PDO::PARAM_INT);
    }
    else
    {
        $stmt = $conn->prepare("SELECT * FROM topics WHERE owner = ? AND (title LIKE ? OR topic_name LIKE ?) LIMIT ?");
        $stmt->bindParam(1, $user_email, PDO::PARAM_STR);
        $stmt->bindParam(2, $keyword, PDO::PARAM_STR);
        $stmt->bindParam(3, $keyword, PDO::PARAM_STR);
        $stmt->bindParam(4, $count, PDO::PARAM_INT);
    }

    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}
function get_specific_poll($id)
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM topics WHERE id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    return $result;
}
function get_options($id)
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM poll_options WHERE topic_id = ?");
    $stmt->bindParam(1, $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}
function list_polls()
{
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM topics");
    $stmt->execute();
    $result = $stmt->fetchAll();
    return $result;
}

?>