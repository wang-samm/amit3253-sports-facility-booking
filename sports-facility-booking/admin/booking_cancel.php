<?php
require '../config.php';require '../auth.php';require_admin();if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)($_POST['id']??0);$stmt=$conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND status='confirmed'");$stmt->bind_param('i',$id);$stmt->execute();}header('Location: bookings.php');exit;

