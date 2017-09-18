<?php
session_start();
$_POST = json_decode(file_get_contents('php://input'), true);
try{
$db = new PDO("mysql:host=localhost;dbname=c9;",'adace1','');
} catch(Exception $e) {
    echo "Could not connect";
    exit(1);
}

if($_POST['newpost'] == true) {
    newPost();
}

if($_POST['changeField'] == "changeTitle") {
    changeField("title");
}

else if($_POST['changeField'] == "changeContent") {
    changeField("content");
}

if($_POST['fetchPosts'] == true) {
    getAllPosts();
}

if($_POST['editCurrentPost'] == true) {
    editCurrentPost();
}

if($_POST['deletePost'] == true) {
    deletePost();
}

function newPost() {
    global $db;
    $stmt = $db->prepare("insert into Diary(title,content,user_email) values(?,?,?)");
    $stmt->execute(array('','',$_POST['email']));
    echo $db->lastInsertId();
}

function changeField($field) {
    global $db;
    if($field == "title") {
        $stmt = $db->prepare("update Diary set title = ? where id = ? and user_email = ?");
        $stmt->execute(array($_POST['title'],$_POST['postId'],$_POST['email']));
    } else {
        $stmt = $db->prepare("update Diary set content = ? where id = ? and user_email = ?");
        $stmt->execute(array($_POST['content'],$_POST['postId'],$_POST['email']));
    }
}

function getAllPosts() {
    global $db;
    $stmt = $db->prepare("select * from Diary where user_email = ? order by created desc");
    $stmt->execute(array($_POST['email']));
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

function editCurrentPost() {
    global $db;
    $stmt = $db->prepare("select title,content from Diary where id = ? and user_email = ?");
    $stmt->execute(array($_POST['postId'],$_POST['email']));
    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
}

function deletePost() {
    global $db;
    $stmt = $db->prepare("delete from Diary where id = ? and user_email = ?");
    $stmt->execute(array($_POST['postId'],$_POST['email']));
}


?>