<?php
require 'config.php';require 'auth.php';require_login();if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)($_POST['id']??0);$uid=current_user_id();$stmt=$conn->prepare("UPDATE bookings b JOIN time_slots ts ON ts.id=b.time_slot_id SET b.status='cancelled' WHERE b.id=? AND b.user_id=? AND b.status='confirmed' AND TIMESTAMP(b.booking_date,ts.start_time)>NOW()");$stmt->bind_param('ii',$id,$uid);$stmt->execute();}header('Location: index.php');exit;

