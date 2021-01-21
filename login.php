<?php
require_once('pdo.php');
    #IF USER IS PRE LOGGED IN
    session_start();
    if(isset($_SESSION['user_id'])){
        header('Location:index.php');
        return;
    }



         
    if(isset($_POST['signup'])){
        ###For sign up



        if(strlen($_POST['name']) > 3 && strlen($_POST['passwd']) > 4){
            if(strlen($_POST['email']) >3 && strpos($_POST['email'],'@')){
                if($_POST['passwd'] == $_POST['cpasswd']){
                    #die("fsf");
                    #Finallllly alll tests are over
                    #now it is is ti,e to check for duplicate email or not????
                    $smt = $pdo->prepare("SELECT email FROM `accounts-rj` WHERE email = :e");
                    #### I fetch only email just to avoid smart user's data hacking
                    $smt->execute(array( 
                        ':e' => $_POST['email']
                    ));
                    $userDetails = $smt -> fetch(PDO::FETCH_ASSOC);
                    if($userDetails != null){
                        $_SESSION['error'] = 'Email already existes';
                        header('location:login.php');
                        return;
                    }
                    #### All check is complete i.e. user is genuine

                    ###Time to insert into db
                    $smt = $pdo->prepare("INSERT INTO `accounts-rj` (name,email,password) VALUES(:n,:e,:p)");
                    $smt->execute(array( 
                        ':n' => $_POST['name'],
                        ':e' => $_POST['email'],
                        ':p' => $_POST['passwd']
                    ));


                    #Time to check our insertion is successfull or not

                    $smt = $pdo->prepare("SELECT id FROM `accounts-rj` WHERE email = :e");
                    #### I fetch only email just to avoid smart user's data hacking
                    $smt->execute(array( 
                        ':e' => $_POST['email']
                    ));
                    $userDetails = null;
                    $userDetails = $smt -> fetch(PDO::FETCH_ASSOC);
                    if($userDetails != null){
                        ###i.e our insertion is successfull......
                        $_SESSION['user_id'] = $userDetails['id'];
                        $_SESSION['success'] = "Account Created Successfully";                                ###i saved user/_id to sessions to avoid every time log in
                        header('location:index.php');
                        return;
                    }else{
                        $_SESSION['error'] = "Some unexpected error occurs";
                    }
                    ##########   sign up is done successfully


                }else{
                    $_SESSION['error'] = "Password and Confirm Password doesn't match together";
                }
            }else{
                $_SESSION['error'] = 'Wrong Email Format';
            }
        }else{
            $_SESSION['error'] = 'Name must be more tham 3 charecters and password must be > 5 characters';
        }
    }

    




    ###for log in
    if(isset($_POST['login'])){
        if(strlen($_POST['uname']) >3 && strpos($_POST['uname'],'@')){
            $smt = $pdo->prepare("SELECT * FROM `accounts-rj` WHERE email = :e and password = :p");
            $smt->execute(array( 
                ':e' => $_POST['uname'],
                ':p' => $_POST['password']
            ));
            $userDetails = null;
            $userDetails = $smt -> fetch(PDO::FETCH_ASSOC);
            if($userDetails != null){
                $_SESSION['user_id'] = $userDetails['id'];
                $_SESSION['success'] = "Successfully logged in";
                header("location:index.php");
                return;
            }else{
                $_SESSION['error'] = "Incorrect Email or Password";
            }
        }else{
            $_SESSION['error'] = "Incorrect Email format";
        }
    }
    

    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="./css/login.css">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
</head>
<body>


        <li class="nav-item active">
			<a class="nav-link" href="index.html">Home <span class="sr-only">(current)</span></a>
		</li>



        <div class="container">    
        <div id="loginbox" style="margin-top:50px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            <div class="panel panel-info" >
                    <div class="panel-heading">
                        <div class="panel-title">Sign In</div>
                        <div style="float:right; font-size: 80%; position: relative; top:-10px"><a href="#">Forgot password?</a></div>
                    </div>     

                    <div style="padding-top:30px" class="panel-body" >

                        <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12"></div>

                        <!------- Changed By Sayak Das   ------>
                        <?php
                            if( isset($_SESSION['error']) ){
                            echo("<p style=\"color: red;\">".$_SESSION['error']."</p>");
                            unset($_SESSION['error']);
                        }
                        ?>
                        <!--Changed by sayak Das
                        
                        
                        action = 'login.php'
                            --->    

                        <form id="loginform" class="form-horizontal" role="form"  method="POST" action = 'login.php'>
                                    
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                        <input id="login-username" type="text" class="form-control" name="uname" value="" placeholder="user email">                                        
                                    </div>
                                
                            <div style="margin-bottom: 25px" class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="login-password" type="password" class="form-control" name="password" placeholder="password">
                                    </div>
                                    

                                
                            <div class="input-group">
                                      <div class="checkbox">
                                        <label>
                                          <input id="login-remember" type="checkbox" name="remember" value="1"> Remember me
                                        </label>
                                      </div>
                                    </div>


                                <div style="margin-top:10px" class="form-group">
                                    <!-- Button -->

                                    <div class="col-sm-12 controls">

                                    <!--- Changed by Sayak Das      
                                
                                
                                  value  = login
                                  and replace <a by <button
                                -->
                                      <button id="btn-login" type="submit" name="login" value='login' class="btn btn-success">Login  </button>
                                      <a id="btn-fblogin" href="#" class="btn btn-primary">Login with Facebook</a>

                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="border-top: 1px solid#888; padding-top:15px; font-size:85%" >
                                            Don't have an account! 
                                        <a href="#" onClick="$('#loginbox').hide(); $('#signupbox').show()">
                                            Sign Up Here
                                        </a>
                                        </div>
                                    </div>
                                </div>    
                            </form>     



                        </div>                     
                    </div>  
        </div>
        <div id="signupbox" style="display:none; margin-top:50px" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Sign Up</div>
                            <div style="float:right; font-size: 85%; position: relative; top:-10px"><a id="signinlink" href="#" onclick="$('#signupbox').hide(); $('#loginbox').show()">Sign In</a></div>
                        </div>  
                        <div class="panel-body" >

          <!----- Changed by Sayak Das ---->

                        <?php
                            if( isset($_SESSION['error']) ){
                            echo("<p style=\"color: red;\">".$_SESSION['error']."</p>");
                            unset($_SESSION['error']);
                        }
                        ?>
                            <!-- Changed by Sayak Das
                        
                                action = login.php
                        -->
                            <form id="signupform" class="form-horizontal" role="form"  method="POST" action = 'login.php'>
                                
                                <div id="signupalert" style="display:none" class="alert alert-danger">
                                    <p>Error:</p>
                                    <span></span>
                                </div>
                                    
                                
                                  
                                <div class="form-group">
                                    <label for="email" class="col-md-3 control-label">Email</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="email" placeholder="Email Address">
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="name" class="col-md-3 control-label"> Name</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" placeholder="Name">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password" class="col-md-3 control-label">Password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" name="passwd" placeholder="Password">
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="password" class="col-md-3 control-label">Confirm password</label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" name="cpasswd" placeholder="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <!-- Button -->                                        
                                    <div class="col-md-offset-3 col-md-9">
                                        <!------Changed By Sayak-------  name = 'signup' [for seperating 2 rrquest]  
                                        
                                        
                                        
                                        
                                        
                                        
                                        ---->
                                        <button id="btn-signup" type="submit" name="signup" value = 'signUp' class="btn btn-info"><i class="icon-hand-right"></i> &nbsp Sign Up</button>
                                        <span style="margin-left:8px;">or</span>  
                                    </div>
                                </div>
                                
                                <div style="border-top: 1px solid #999; padding-top:20px"  class="form-group">
                                    
                                    <div class="col-md-offset-3 col-md-9">
                                        <button id="btn-fbsignup" type="submit" name="submit" class="btn btn-primary"><i class="icon-facebook"></i>   Sign Up with Facebook</button>
                                    </div>                                           
                                        
                                </div>
                                
                                
                                
                            </form>
                         </div>
                    </div>
         </div> 
    </div>
</body>
</html>