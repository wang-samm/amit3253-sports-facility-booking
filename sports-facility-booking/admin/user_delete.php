<?php
require '../config.php';require '../auth.php';require_admin();if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)($_POST['id']??0);if($id===(int)current_user_id())$_SESSION['flash_error']='You cannot delete your own account.';else{$stmt=$conn->prepare('DELETE FROM users WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();}}header('Location: users.php');exit;

