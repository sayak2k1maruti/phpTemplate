<?php
    require_once("./pdo.php");
    session_start();
    $user = array( 
        'name' => NULL,
        'email' => NULL,
        'mobile' => NULL,
        'city' => NULL,
        'address' => NULL,
        'stream' => NULL,
        'cat' => NULL,
        'target_year' => NULL,
        'dp' => NULL
    );
    

    $editOrViewPersonal = 0;
    $editOrViewDp = 0;
    $editOrViewAcademic = 0;
    ### above variable will be used to determine if 
    #user is in edit mode or in view mode
    $firstTime = 0;
    ##if user is logged in for first time

    if(! isset($_SESSION['user_id'])){
        #i.e user is not logged in
        header("Location:login.php");
        return;
    }

    ## Now let's try to fetch user's details
    $smt = $pdo->prepare("SELECT * FROM `accounts-rj` WHERE id = :u");
    $smt->execute(
        array(
            ':u' => $_SESSION['user_id']
        )
    );
    $tempData = $smt -> fetch(PDO::FETCH_ASSOC);
    if($tempData == NULL){
        #i.e user is trying to do some bakchodi
        header("Location:login.php");
        return;
    }
    $user['email'] = $tempData["email"];
    $user['name'] = $tempData['name'];

    ###Try to fetch user basic Details
    $smt = $pdo->prepare("SELECT * FROM `personalDetails` WHERE user_id = :u");
    $smt->execute(
        array(
            ':u' => $_SESSION['user_id']
        )
    );
    $tempData = $smt -> fetch(PDO::FETCH_ASSOC);

    if($tempData == NULL){
        #i.e user did not enter their personal details ....
        $editOrViewPersonal = 1;
        $firstTime = 1;
    }else{
        ##
        $user['mobile'] = $tempData['mobile_number'];
        $user['city'] = $tempData['city'];
        $user['address'] = $tempData['address'];
        ####

        ###try to fetch Academic details
        $smt = $pdo->prepare("SELECT * FROM `academicDetails` WHERE user_id = :u");
        $smt->execute(
            array(
                ':u' => $_SESSION['user_id']
            )
        );
        $tempData = $smt -> fetch(PDO::FETCH_ASSOC);
        if($tempData != NULL){
            ###
            $user['stream'] = $tempData['stream'];
            $user['cat'] = $tempData['category'];
            $user['target_year'] = $tempData['targetYear'];
            ###
        }
    }


    if(isset($_POST['submit'])){
        if($_POST['submit'] == 'personalDetails'){
            #i.e. user want to edit/add his personal details
            if(strlen($_POST['mobile']) == 10){
                if($firstTime){
                    #i.e use logged in for first time
                    $smt = $pdo->prepare("INSERT INTO `personalDetails` (`entry_id`, `user_id`, `mobile_number`, `city`, `address`) VALUES (NULL, :u , :m, NULL, NULL) ");
                    $smt->execute(
                    array(
                        ':u' => $_SESSION['user_id'],
                        ':m' => $_POST['mobile']
                        )
                    );
                    $smt = $pdo->prepare("INSERT INTO `academicDetails` (`entry_id`, `user_id`, `category`, `stream`, `targetYear`) VALUES (NULL ,:u , NULL, NULL, NULL)");
                    $smt->execute(
                    array(
                        ':u' => $_SESSION['user_id']
                        )
                    );
                }

                ###
                $smt = $pdo->prepare("UPDATE `personalDetails` SET `city` = :c , `address` = :a  WHERE `personalDetails`.`user_id` = :u ");
                    $smt->execute(
                    array(
                        ':c' => $_POST['city'],
                        ':a' => $_POST['address'],
                        ':u' => $_SESSION['user_id']
                        )
                    );
                    header("Location:login.php");
                    return;
            }else{
                $_SESSION['error'] = "Correct Mobile Number is mandatory";
            }
        }


        ###
        if($_POST['submit'] == 'academicDetails'){
            ##i.e user want to update academic details
            $smt = $pdo->prepare("UPDATE `academicDetails` SET `category` = :c , `stream` = :s , `targetYear` = :t  WHERE `academicDetails`.`user_id` = :u ");
                    $smt->execute(
                    array(
                        ':c' => isset($_POST['cat']) ? $_POST['cat'] : NULL,
                        ':s' => $_POST['stream'],
                        ':t' => (int)$_POST['year'],
                        ':u' => $_SESSION['user_id']
                        )
                    );
                    header("Location:login.php");
                    return;
        }
    }


    ##try to fetch dp
    $smt = $pdo->prepare("SELECT * FROM `profilePic`  WHERE user_id = :u");
        $smt->execute(
            array(
                ':u' => $_SESSION['user_id']
            )
        );
        $tempData = $smt -> fetch(PDO::FETCH_ASSOC);
        if($tempData != NULL){
            $user['dp'] = $tempData['image_url'];
        }



    if(isset($_POST['edit'])){
        if($_POST['edit'] == 'personalDetails'){
            $editOrViewPersonal = 1;
        }
        if($_POST['edit'] == 'academicDetails'){
            $editOrViewAcademic = 1;
        }
        if($_POST['edit'] == 'dp'){
            $editOrViewDp = 1;
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>DashBoard</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" /><meta content="width=device-width, initial-scale=1" name="viewport" />
</head>
<body>
    <div class='dpDiv'>
        <div class='dp'>
            <img alt = 'avtar' src= <?= $user['dp']==NULL ? './avtar.png' : './uploads/'.$user['dp'] ?>></img>
        </div>
        <form  method='POST' action = 'index.php'><button class='editButton' type = 'submit' name='edit' value='dp'>Change Dp</button></form>
        <?php
            if($editOrViewDp){
                echo('    <form action="upload.php" method="post" enctype="multipart/form-data">'.
                '.Select image to upload:'.
                '<input type="file" name="fileToUpload" id="fileToUpload">'.
                '<input type="submit" value="Upload Image" name="submit">'.
              '</form>');
            }
        ?>
    </div>
<?php
    if( isset($_SESSION['error']) ){
        echo("<p style=\"color: red;\">".$_SESSION['error']."</p>");
        unset($_SESSION['error']);
    }
?>

    <div class = "details">
        
        <?php
        if($editOrViewPersonal){
            echo("<p>Name : " . htmlentities($user['name']) ."</p>");
            echo("<p>Email : " . htmlentities($user['email']) . "</p>");
            echo('<form method="POST" action="index.php">'.
                '<label for="mobileNumber">Mobile Number<span style="color: red;"> *</span></label>
                <input id="mobileNumber" type="tel"  placeholder="Mobile number ..." name="mobile" value='. htmlentities($user['mobile']). '></br>
                <label for="city">City : </label>
                <input id="city"placeholder="city...." name="city" value='.htmlentities($user['city']).'></br>
                <label for="address">Adddress:</label>
                <input id="address" placeholder="address..." name="address" value='.htmlentities($user['address']).'></br></br>
                <button type="submit" name="submit" value="personalDetails">Save Changes</button>
            </form>
            ');
        }else{
            echo("<form  method='POST' action = 'index.php'><button class='editButton' type = 'submit' name='edit' value='personalDetails'>Edit</button></form>");
            echo("<p>Name : " . htmlentities($user['name']) ."</p>");
            echo("<p>Email : " . htmlentities($user['email']) . "</p>");
            echo("<p>Mobile : " . htmlentities($user['mobile']) . "</p>");
            echo("<p>City : " . htmlentities($user['city']) . "</p>");
            echo("<p>Address : " . htmlentities($user['address']) . "</p>");  
        }    
        ?>
    </div>
    <div class = "details">
        
        <?php
        if(!$firstTime){
            if($editOrViewAcademic){
                echo('
                <form method="POST" action="index.php">
                <label for = "stream">Stream :</label>
                <input placeholder="stream.." name="stream" value='.$user['stream'].'>
                Catagory:</br>
                <input type="radio" name="cat" value="Gen" checked>Gen </br>
                <input type="radio" name="cat" value="OBC">OBC </br>
                <input type="radio" name="cat" value="SC">SC </br>
                <input type="radio" name="cat" value="ST">ST</br>
                Target Year : <input type="number" max="2025" min="2021" name="year" value= ' .$user['target_year'].'>
            </br></br>
            <button type="submit" name="submit" value="academicDetails">Save Changes</button>
            </form>
                ');
            }else{
                echo("<form method='POST' action = 'index.php'><button class='editButton' type = 'submit' name='edit' value='academicDetails'>Edit</button></form>");
                echo("<p>Stream : " . htmlentities($user['stream']) ."</p>");
                echo("<p>Cast : " . htmlentities($user['cat']) . "</p>");
                echo("<p>Target year : " . htmlentities($user['target_year']) . "</p>");
            }
        }
        ?>    

    </div>  
    <br/>

    </br>
    <a href = './logout.php'><button>LogOut</button></a>
</body>
<style>
    .details{
        width : 45vw;
        display: inline-block;
        box-shadow: #000 10px 10px 10px;
        margin : 2vw;
        background: rgba(255, 255, 255, 0.657);
    }
    .details:hover{
        transform : scale(1.1);
    }
    body{
        text-align : center;
        background: #0000005e;
    }
    .dp{
    width: 200px;
    height: 200px;
    border-radius: 100px;
    display: inline-block;

}
.dp img{
    width: inherit;
    height: inherit;
    border-radius: 100px;
    box-shadow: #000 10px 10px 10px;
    display: inline-block;

}
</style>
</html>