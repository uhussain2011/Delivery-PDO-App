<?php
include_once 'DB/info.php';
include_once 'DB/db.php';

class DeleteClass extends Dcon  {
    function delete($id)
    {
        $count=$this->connect()->prepare("DELETE FROM consignment_details WHERE consignment_id=:id");
        $count->bindParam(":id",$id,PDO::PARAM_INT);
        $count->execute();

        $count=$this->connect()->prepare("DELETE FROM consignmentsh WHERE id=:id");
        $count->bindParam(":id",$id,PDO::PARAM_INT);
        $count->execute();

       return true;

    }
}



$create = new DeleteClass();
if(isset($_GET['id']))
{
    $create->delete($_GET['id']);

    $_SESSION["type"] = "alert-danger";
    $_SESSION["message"] = "Record deleted successfully";

    header("LOCATION: ".$base_path);
}
else
{
    header("LOCATION: ".$base_path);
}
