<?php

/**
 * Back end routines to add/delete courses, invoked by faculty.php
 * @author Avin E.M
 */

require_once('functions.php');
if(!sessionCheck('logged_in'))
  postResponse("error","Your session has expired, please login again");
require_once('connect_db.php');




if(!isset($_SESSION['faculty']))
  $_SESSION['faculty'] = $_SESSION['uName'];
if(!sessionCheck('level','faculty') && !empty($_GET['faculty']))
  $_SESSION['faculty'] = $_GET['faculty'];
if(valueCheck('action','add'))
{ $course = $_POST['cName'];
   $course = explode(" : ",$course);
  if(empty($_POST["allowConflict"]))
    $_POST["allowConflict"] = 0;
  try
  {
	  
    $query = $db->prepare('INSERT INTO courses(course_id,course_name,fac_id,allow_conflict) values (?,?,?,?)');
    $query->execute([$course[1],$course[0],$_SESSION['faculty'],$_POST["allowConflict"]]);
    $query = $db->prepare('INSERT INTO allowed(course_id,batch_name,batch_dept) values (?,?,?)');
    foreach ($_POST['batch'] as $batch) 
    {
      $batch = explode(" : ",$batch);
      $query->execute([$course[1],$batch[0],$batch[1]]);      
    }
    postResponse("addOpt","Course Added",[$course[0],$course[1]]);  
  }
	  
	  
  catch(PDOException $e)
  {
    if($e->errorInfo[0]==23000)
      postResponse("error","Course ID already exists");
    else
      postResponse("error",$e->errorInfo[2]);
  }
}
elseif(valueCheck('action','delete'))
{
  $query = $db->prepare('DELETE FROM courses where course_id =? and fac_id =?');
  $query->execute([$course[1],$_SESSION['faculty']]);
  postResponse("removeOpt","Course deleted");
}

?>
