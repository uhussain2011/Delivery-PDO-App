<?php
include_once 'DB/info.php';
include_once 'DB/db.php';
include_once 'Classes/files.php';



class Create extends Dcon  {
    use Fileupload;

    function GetData($table) {
        $query = "SELECT id FROM ".$table." ORDER bY id desc limit 1";
        $stm = $this->connect()->query($query);
        $rows = $stm->fetch(PDO::FETCH_ASSOC);
        return $rows['id']??0;
    }

    function GetDetails($table,$id) {
        $query = "SELECT * FROM ".$table." where id = ".$id."  ORDER By id desc limit 1";
        $stm = $this->connect()->query($query);
        $rows = $stm->fetch(PDO::FETCH_ASSOC);
     return (object)$rows;
    }

    function ConsignmentDetails($id) {
        $query = "SELECT orders.id AS id,orders.name AS name FROM consignment_details JOIN orders ON orders.id = consignment_details.order_id WHERE consignment_id = ".$id." ORDER BY orders.id";
        $stm = $this->connect()->query($query);
        $rows = $stm->fetchAll(PDO::FETCH_ASSOC);
        $oldselect = '';

            foreach ($rows as $details)
            {
                $details = (object)$details;
                $oldselect.= '<option value="'.$details->id.'" selected>'.$details->name.'</option>';
            }
            return $oldselect;

    }

    function UpdateConsignmentes($data,$id)
    {
        $keys=  implode("=?,",array_keys($data)).'=?';
        $sql = "UPDATE consignmentes SET ".$keys." WHERE id=".$id;
        $is_true =  $this->connect()->prepare($sql)->execute(array_values($data));
       if($is_true)
       {
           $count=$this->connect()->prepare("DELETE FROM consignment_details WHERE consignment_id=:id");
           $count->bindParam(":id",$id,PDO::PARAM_INT);
           $count->execute();

           foreach ($_POST['order_id'] as $order_id)
           {
               $query = "INSERT INTO `consignment_details`(`order_id`, `consignment_id`, `created_at`, `updated_at`) VALUES (?,?,?,?)";
                $this->connect()->prepare($query)->execute([$order_id,$id, date("Y-m-d h:i:s"), date("Y-m-d h:i:s")]);
           }
       }

       return true;

    }
}



$create = new Create();

if(isset($_GET['id']))
{
    $oldvalues =  $create->GetDetails('consignmentes',$_GET['id']);
    $oldselect =  $create->ConsignmentDetails($_GET['id']);

}
else
{
    header("LOCATION: ".$base_path);
}

if (isset($_POST['status'])) {



    $reqest = [];
    if (isset($_POST['status']) && $_POST['status'] == 'Start')
        $reqest['start_date'] = date("Y-m-d h:i:s");
    if (isset($_POST['status']) && $_POST['status'] == 'End')
        $reqest['end_date'] = date("Y-m-d h:i:s");

    $reqest['courier_name'] = $_POST['couriers'];
    $reqest['status'] = $_POST['status'];
    if(file_exists($_FILES['file']['tmp_name']))
    $reqest['file'] = file_exists($_FILES['file']['tmp_name'])?$create->Savefile($_FILES) : null ;
    $reqest['created_at'] = date("Y-m-d h:i:s");
    $reqest['updated_at'] = date("Y-m-d h:i:s");

    $is_save = $create->UpdateConsignmentes($reqest,$_GET['id']);

   if($is_save)
   {
       $_SESSION["type"] = "alert-success";
       $_SESSION["message"] = "Record Edit successfully";

       header("LOCATION: ".$base_path);
   }
}






?>
<?php
include_once './layout/header.php';
?>

<main role="main" class="col-md-12 ml-sm-auto col-lg-12 px-4">
  <a href="/courier">Courier</a>
            <div class="my-3 p-3 mt-5 rounded shadow-sm album py-5 bg-light">
                <h6 class="border-bottom border-gray pb-2 mb-0">Consignment</h6>
                        <form action="#" class="mt-5" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-6">

                            <div class="form-group">
                                <label for="exampleInputEmail1">Start/End Consignment</label>
                                <select name="status" class="form-control" id="exampleFormControlSelect1" required>
                                    <option value="">Select</option>
                                    <option <?php if($oldvalues->status == "Start") echo 'selected' ;?>  value="Start">Start</option>
                                    <option <?php if($oldvalues->status == "End") echo 'selected' ;?> value="End">End</option>
                                </select>
                                <small id="emailHelp" class="form-text text-muted"></small>
                            </div>
                                </div>
                                <div class="col-6">

                            <div class="form-group">
                                <label for="exampleInputEmail1">Couriers</label>
                                <select name="couriers" class="form-control" id="exampleFormControlSelect1" required >
                                    <option  value="">Select</option>
                                    <option  <?php if($oldvalues->courier_name == "Royal Mail") echo 'selected' ;?> value="Royal Mail">Royal Mail</option>
                                    <option  <?php if($oldvalues->courier_name == "DHL") echo 'selected' ;?> value="DHL">DHL</option>
                                    <option  <?php if($oldvalues->courier_name == "ParcelForce") echo 'selected' ;?> value="ParcelForce">ParcelForce</option>
                                    <option  <?php if($oldvalues->courier_name == "DPD") echo 'selected' ;?> value="DPD">DPD</option>
                                </select>
                                <small id="emailHelp" class="form-text text-muted"></small>
                            </div>
                                </div>
                                <div class="col-6">

                            <div class="form-group">
                                <label for="exampleInputEmail1">Order </label>
                                <select multiple name="order_id[]" id="ee" class="form-control" required  >
                                    <?php echo $oldselect?>
                                </select>
                                <small id="emailHelp" class="form-text text-muted"></small>
                            </div>
                                </div>
                                <div class="col-6">

                            <div class="form-group">
                                <label for="exampleInputEmail1">File </label>
                                <input class="form-control-file"  id = "exampleInputEmail1" type="file" name="file" id="">
                                <small id="emailHelp" class="form-text text-muted"></small>
                            </div>
                                </div>
                                <div class="col-6">
                                     <button type="submit" class="btn btn-primary">Submit</button>
                                     <button type="reset" class="btn btn-secondary">reset</button>
                                </div>
                            </div>
                        </form>
            </div>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.full.js"></script>

<script>

    var SITEURL = '<?php echo $base_path  ?>'+'Classes/main.php';
$("#ee").select2({
        multiple: true,
        ajax: {
            url: SITEURL,
            dataType: 'json',
            delay: 250,
            data: function (params) {
        return {
                q: params.term, // search term
                page: params.page,
                ajax: "true"
        };
            },
            processResults: function (data, page) {
        return {
            results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 1,
        templateResult: formatRepo,
        templateSelection: formatRepoSelection

    });

    function formatRepo (repo) {
        if (repo.loading) return repo.text;

        var markup =
            '<div class="avatar">' +
            '<div class="col-sm-6">' + repo.id +' | '+ repo.name + '</div>'
        ;


        markup += '</div></div>';

        return markup;
    }

    function formatRepoSelection (repo) {
        if(repo.text)
            return repo.id +' | '+ repo.text;
        return repo.id +' | '+ repo.name;
    }

</script>
</body>
</html>