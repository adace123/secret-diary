<?php 
$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"], 1);
$_POST = json_decode(file_get_contents('php://input'), true);
try{
$db = new PDO("mysql:host=$server;dbname=$db;",$username,$password);
} catch(Exception $e) {
    echo "Could not connect";
    exit(1);
}

if($_POST['registerMode'] == true) {
addUser();
}

else if(checkAuthenticated()) {
echo json_encode(array("status" => "success", "remember" => $_POST['remember']));
}

function checkRegistered() {
    global $db;
    $stmt = $db->prepare("select * from User where email = ?");
    $stmt->execute(array($_POST['email']));
    return $stmt->rowCount() == 0 ? true : false;
}

function addUser() {
    if(checkRegistered()){
    global $db;
    $stmt = $db->prepare("insert into User(email,password) values(?,?)");
    $stmt->execute(array($_POST['email'],password_hash($_POST['password'], PASSWORD_DEFAULT)));
    echo json_encode(array("status" => "success"));
    } else echo "User already registered";
}

function checkAuthenticated() {
    global $db;
    $stmt = $db->prepare("select password from User where email = ?");
    $stmt->execute(array($_POST['email']));
    $pass = $stmt->fetch(PDO::FETCH_ASSOC)['password'];
    return password_verify($_POST['password'],$pass);

}


?>
