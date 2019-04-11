<?php

/*
***************************************************************************
--Web application homework--

Connecting php to the database and let the user decide on what
to do with the data: insert an item, delete an item or modify an item.
***************************************************************************
*/

function read_fromDB($db_handler){
    $html_read = "";
    $sql="SELECT * FROM Appetizers";
    if ($result = $db_handler->query($sql)){
        while($row=$result->fetch_assoc())
        {
            $html_read = $html_read.'<li>';
            //show the data from DB
            $html_read = $html_read.'<div><span class="item">'.$row['itemName'].'</span>';
            $html_read = $html_read.'<span class="item-price"> ['.$row['itemPrice'].'] </span>';
            $html_read = $html_read.'<span class="item-desc"> '.$row['itemDesc']."</span></div>";
            //show area to do updates
            $html_read = $html_read.'<div id="item-'.$row['itemID'].'" >';
            $html_read = $html_read.'<form action="" method="POST">';
            $html_read = $html_read.'<input name="itemId" type="number" value="'.$row['itemID'].'" hidden/>';
            $html_read = $html_read.'<input name="name" type="text" value="'.$row['itemName'].'" placeholder="item name" />';
            $html_read = $html_read.'<input name="price" type="text" value="'.$row['itemPrice'].'" placeholder="10.00" />';
            $html_read = $html_read.'<input name="desc" type="text" value="'.$row['itemDesc'].'" placeholder="item description" />';
            $html_read = $html_read.'<input type="submit" name="upd-submit" value="Update" class="btn btn-warning"/>';
            $html_read = $html_read.'</form>';
            $html_read = $html_read.'</div>';
            // show button to delete
            $html_read = $html_read.'<div><form action="" method="POST">';
            $html_read = $html_read.'<input name="itemId" type="number" value="'.$row['itemID'].'" hidden/>';
            $html_read = $html_read.'<input name="name" type="text" value="'.$row['itemName'].'" hidden/>';
            $html_read = $html_read.'<button type="submit" name="del-submit" class="btn btn-danger"><i class="far fa-trash-alt"></i></button></form>';
            $html_read = $html_read.'</li>';
        }
    }else{
            die('Invalid query: ' . $db_handler->error());
    }
    return $html_read;
}

function delete_fromDB($db_handler, $item){
    $sql="DELETE FROM Appetizers where itemID=$item";
    if ($result = $db_handler->query($sql)){
        $msg = "SUCCESS";
    }else{
        $msg = "FAILURE";
        die('Invalid query: ' . $db_handler->error());
    }
    return $msg;
}

// new_item will have three fields:  name, price and description
function insert_toDB($db_handler, $new_item){
    $i_name = $new_item['name'];
    $i_price = $new_item['price'];
    $i_desc = $new_item['desc'];
    $sql= "INSERT INTO Appetizers (itemName, itemPrice, itemDesc) VALUES ('$i_name',$i_price,'$i_desc');";
    
    if ($result = $db_handler->query($sql)){
        $msg = "SUCCESS";
    }else{
        $msg = "FAILURE";
        die('Invalid query: ' . $db_handler->error());
    }
    return $msg;
}
// update an existing item
function update_DB($db_handler, $new_item){
    $i_id = $new_item['id'];
    $i_name = $new_item['name'];
    $i_price = $new_item['price'];
    $i_desc = $new_item['desc'];

    // constructing the SQL statement
    $sql = "UPDATE Appetizers SET";
    $sql .= (empty($i_name))? '' : " itemName = '$i_name'";
    $sql .= (!empty($i_name) && (!empty($i_price) || !empty($i_desc)))? ",":'';
    $sql .= (empty($i_price))? '' : " itemPrice = $i_price";
    $sql .= (!empty($i_price) && (!empty($i_desc)))? ",":'';
    $sql .= (empty($i_desc))? '' : " itemDesc = '$i_desc' ";
    $sql .= " where itemID = $i_id;";

    // echo $sql;
        
    if ($result = $db_handler->query($sql)){
        $msg = "SUCCESS";
    }else{
        $msg = "FAILURE";
        die('Invalid query: ' . $db_handler->error());
    }
    return $msg;
}



require_once("includes/server.php");
$message = '';

if(isset($_POST['add-submit']))
{
    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';

    if(!empty($_POST['name']) && !empty($_POST['price']) && !empty($_POST['desc']))
        {
            $new_item['name'] = $mysqli->escape_string($_POST['name']);
            $new_item['price'] = floatval($_POST['price']);
            $new_item['desc'] = $mysqli->escape_string($_POST['desc']);
            $message = '<p style="color:green;" class="text-center"> <b>'.htmlentities($_POST['name']).'</b> added with ' . insert_toDB($mysqli,$new_item).'</p>';
        }
    else
        {
            $message = '<p style="color:red;" class="text-center">to add an item you need to fill up the information</p>';
        }
}else if(isset($_POST['del-submit'])){

    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';

    $message = '<p style="color:green;" class="text-center"> <b>'.htmlentities($_POST['name']).'</b> deleted with '.delete_fromDB($mysqli,intval($_POST['itemId'])).'.</p>';

}else if(isset($_POST['upd-submit'])){

    // echo '<pre>';
    // var_dump($_POST);
    // echo '</pre>';
    $new_item['id'] = intval($_POST['itemId']);
    $new_item['name'] = $mysqli->escape_string($_POST['name']);
    $new_item['price'] = empty($_POST['price'])? '' : floatval($_POST['price']);
    $new_item['desc'] = $mysqli->escape_string($_POST['desc']);

    // echo '<pre>';
    // var_dump($new_item);
    // echo '</pre>';

    $message = '<p style="color:green;" class="text-center"> <b>'.htmlentities($_POST['name']).'</b> updated with '.update_DB($mysqli,$new_item).'.</p>';
}

?>


<!DOCTYPE html>
    <head>
       <?php include_once("includes/headConfig.inc.php")?>
        <title>Marianela - DB Homework</title>
    </head>
    <body>
    <header class="bg-primary py-2">
        <h1 class="text-center">DATABASE HOMEWORK</h1>
        <?php include_once("includes/navigation.inc.php")?>
    </header>
<!-- ############################  START OF THE BODY CONTENT ############################ -->
    <main>
        <div class="container-fluid">
            <div class="row">
                <h2 class="text-center col-12">Restaurant - Appetizers</h2>
            </div>
        </div>
                <?php echo $message ?>

        <hr />

        <div class="container px-3">
            <div class="row">
                <div class="col-12">
                    <h3 class="pb-3">Insert Appetizers</h3>
                        <div class="row justify-content-center">
                            <form class="col-lg-10 " action="" method="POST">
                                <input name="name" type="text" placeholder="item name"/>
                                <input name="price" type="text" placeholder="price 10.00"/>
                                <input name="desc" type="text" placeholder="item description"/>
                                <button type="submit" name="add-submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr/>

        <div class="container px-3">
            <div class="row">
                <div class="col-12">
                    <h3 class="pb-3">Read the Appetizers</h3>
                        <div class="row">
                            <ol class="col-12 list">
                                <?php 
                                    $listOfItems = read_fromDB($mysqli);
                                    if(empty($listOfItems))
                                        echo "No results";
                                    else
                                        echo $listOfItems;
                                ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>                

        <hr/>

    </main>
    <?php include("includes/footer.inc.php"); ?>
</body>
<html>
<!-- ############################  END OF THE BODY CONTENT ############################ -->