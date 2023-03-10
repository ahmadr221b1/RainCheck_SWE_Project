<!DOCTYPE html>
<html>
  <head>
    <title>Verification</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
    <script
      type="module"
      src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"
    ></script>
    <script
      nomodule
      src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"
    ></script>
  </head>
  <body>
    <?PHP
    include 'SendMail.php';
    if(isset($_POST['submitFromForgotPassword'])||isset($_POST['submit'])||isset($_POST['Verify'])){
    /* Connect to database */
    ob_start(); //This is optional. require by our specific website host only.
    $servername = "localhost";
    $username="u647272286_swe2023";
    $password= "Heoboy123$%^&*(";
    $db_name="u647272286_swe";
    $conn= new mysqli($servername, $username, $password, $db_name);
    if($conn->connect_error){
      die("connection failed".$conn->connect_error);
    }
    if(isset($_POST['submitFromForgotPassword'])){
      $code = str_pad(rand(800000, 999999), 6, '0', STR_PAD_LEFT);
      while (true){
        $sqlCheck = "SELECT Code FROM Users WHERE Code = $code";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows == 0) {
          break;
        }
        $code = str_pad(rand(800000, 999999), 6, '0', STR_PAD_LEFT);
      }
      $emailReset = $_POST['emailFromForgotPassword'];
      $sqlInputResetCode = "SELECT provider_value FROM google_oauth WHERE provider = 'google';";
      $resultInRow = $conn->query($sqlInputResetCode);
      $resultInValue =  $resultInRow->fetch_assoc();
      $Tran=$resultInValue['provider_value'];
      sendmail($emailReset, $code, $Tran);
      $sqlInputResetCode = "UPDATE Users SET Code = $code WHERE email = '$emailReset';";
      $conn->query($sqlInputResetCode);
    }

    /* User input verification code*/ 
    if(isset($_POST['Verify'])){
      $verify = $_POST['VerificationCode'];
      $sqlcompare="SELECT Code FROM Users WHERE Code = $verify";
      $resultVerify = $conn->query($sqlcompare);
      if ($resultVerify->num_rows === 1){
        if ($verify < 800000 && $verify > 0) {
          $sqlActive = "UPDATE Users SET EmailStatus = '1' WHERE Code = $verify;";
          $sqlDelete ="UPDATE Users SET Code= NULL WHERE Code = $verify;";
          $conn->query($sqlActive);
          $conn->query($sqlDelete);
          ob_end_flush();
          mysqli_close($conn);
          header("Location: https://melvin-projects.com/RainCheck_SWE_Project/index.html");
          exit();
        } else if ($verify >= 800000){
          $sql="SELECT email FROM Users WHERE Code = $verify";
          $resultInRow=$conn->query($sql);
          $resultInValue =  $resultInRow->fetch_assoc();
          $email=$resultInValue['email'];
          ob_end_flush();
          mysqli_close($conn);
          header("Location: https://melvin-projects.com/RainCheck_SWE_Project/ResetPassword.php?email=".$email."&code=".($verify*2));
          exit();
        }
      } else {
        $codeDelete = $_POST['CodeToDelete'];
        if ($codeDelete < 800000 && $codeDelete >0 ){
          echo '
          <div class="container">
          <div class="login-container Verification-container">
            <img src="./Brand-Logo.png" alt="Placeholder Image" />
            <h1>Failed !</h1>
            <p>
              You put the wrong code. Please hit the link below to Sign Up again.
            </p>
            <a href=https://melvin-projects.com/RainCheck_SWE_Project/SignUp.html>Sign Up</a>
          </div>
          <div class="image-container">
            <img src="./Logo.png" alt="Placeholder Image" />
          </div>
        </div> 
      </body>
    </html>
          ';
          $sqlDeletee ="DELETE FROM Users WHERE Code = $codeDelete;";
          $conn->query($sqlDeletee);
          ob_end_flush();
          mysqli_close($conn);
          exit();
        } else {
          echo '
          <div class="container">
          <div class="login-container Verification-container">
            <img src="./Brand-Logo.png" alt="Placeholder Image" />
            <h1>Failed !</h1>
            <p>
              You put the wrong code. Please hit the link below so you can start over.
            </p>
            <a href=https://melvin-projects.com/RainCheck_SWE_Project/ForgotPassword.html>Start Over</a>
          </div>
          <div class="image-container">
            <img src="./Logo.png" alt="Placeholder Image" />
          </div>
        </div> 
      </body>
    </html>';
          $sqlResetFailed ="UPDATE Users SET Code= NULL WHERE Code = $codeDelete;";
          $conn->query($sqlResetFailed);
          ob_end_flush();
          mysqli_close($conn);
          exit();
        }
      }
    }
    /* User come from sign up page. Store data into database*/
    if(isset($_POST['submit'])){
      $code = str_pad(rand(1, 799999), 6, '0', STR_PAD_LEFT);
      while (true){
        $sqlCheck = "SELECT Code FROM Users WHERE Code = $code";
        $result = $conn->query($sqlCheck);
        if ($result->num_rows == 0) {
          break;
        }
        $code = str_pad(rand(1, 799999), 6, '0', STR_PAD_LEFT);
      }
      $firstname_c =  $_POST['firstname'];
      $lastname_c =  $_POST['lastname'];
      $email_c =  $_POST['email'];
      $password_c = $_POST['pass'];
      $tel_c = $_POST['phonenumber'];
      $sql = "INSERT INTO Users (firstname,lastname,email,password,phonenumber,Code) VALUES ('$firstname_c', '$lastname_c', '$email_c', '$password_c', '$tel_c', '$code')";
      mysqli_query($conn, $sql);
      $sqlInputResetCode = "SELECT provider_value FROM google_oauth WHERE provider = 'google';";
      $resultInRow = $conn->query($sqlInputResetCode);
      $resultInValue =  $resultInRow->fetch_assoc();
      $Tran=$resultInValue['provider_value'];
      sendmail($_POST['email'], $code, $Tran);
    }
    ob_end_flush();
    mysqli_close($conn);
    } else {
      header("Location: https://melvin-projects.com/RainCheck_SWE_Project/index.html");
    }
    ?>
    <div class="container">
      <div class="login-container Verification-container">
        <img src="./Brand-Logo.png" alt="Placeholder Image" />
        <ion-icon name="checkmark-circle-outline"></ion-icon>
        <h1>Success !</h1>
        <p>
          An email has been sent to your email address. Please check your inbox
          for an email from our company and then put your verification code down below.
        </p>
        <form 
          class="Verification-form"
          name="Verification"
          action="Verification.php"
          method="POST"
        >
          <input style="width: 226px" type="text" name="VerificationCode" placeholder="Verification Code" maxlength="6"/>
          <input type="submit" id="btn-ForgotPassword" name="Verify" value="Verify">
          <input type="hidden" name="CodeToDelete" value="<?PHP echo $code; ?>">
        </form>
      </div>
      <div class="image-container">
        <img src="./Logo.png" alt="Placeholder Image" />
      </div>
    </div> 
  </body>
</html>