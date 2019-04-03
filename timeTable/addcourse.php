<?php

/**
 * Back end routines to add/delete batches, invoked by manage.php
 * @author Avin E.M
 */

require_once('functions.php');
require_once('connect_db.php');
if(!sessionCheck('logged_in'))
  postResponse("error","Your session has expired, please login again");
if(!sessionCheck('level','dean'))
    die('You are not authorized to perform this action');
if(valueCheck('action','add'))
{
    rangeCheck('ncourse_name',6,100);
    rangeCheck('ncourse_id',1,4);

    try{
        $query = $db->prepare('INSERT INTO ncourses(ncourse_id,ncourse_name,ncourse_dept) values (?,?,?)');
        $query->execute([$_POST['ncourse_id'],$_POST['ncourse_name'],$_POST['ncourse_dept']]);
        postResponse("addOpt","Course Added",[$_POST['ncourse_id'].' : '.$_POST['ncourse_name'],$_POST['ncourse_dept']]);    
    }
    catch(PDOException $e)
    {
        if($e->errorInfo[0]==23000)
            postResponse("error","Course already exists");
        else
            postResponse("error",$e->errorInfo[2]);
    }
    
}
elseif(valueCheck('action','delete'))
{
    $query = $db->prepare('DELETE FROM ncourses where ncourse_name = ? AND ncourse_id=?');
    $ncourse = explode(" : ",$_POST['ncourse']);
    $query->execute([$ncourse[0],$ncourse[1]]);
    postResponse("removeOpt","course deleted");
}

?>