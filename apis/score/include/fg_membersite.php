<?PHP
require_once("class.phpmailer.php");
require_once("formvalidator.php");

//require_once './Array2XML.php';
class FGMembersite
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;
    
    var $scoreTableName = "scores";
    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'rushabhgosar.com';
        $this->rand_key = '0iQx5oBk66oVZep';
    }
    
    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        
    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    
    
    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        
        return true;
    }
    
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }
    
    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);
        
        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }
        
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }
        
        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }
        
        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }
    
    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
        
        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }
        
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        
        $pwd = trim($_POST['oldpwd']);
        
        if($user_rec['password'] != md5($pwd))
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);
        
        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    public $row = array();
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y'";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysql_fetch_assoc($result);        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
    public $user_rec = array();
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysql_query("Select name, email from $this->tablename where confirmcode='$confirmcode'",$this->connection);   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);
        
        $qry = "Update $this->tablename Set password='".md5($newpwd)."' Where  id_user=".$user_rec['id_user']."";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $email = $this->SanitizeForSQL($email);
        
        $result = mysql_query("Select * from $this->tablename where email='$email'",$this->connection);  

        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysql_fetch_assoc($result);
        return true;
    }
    
    function getApiKey()
    {
     if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        //echo $this->UserEmail();
        $query = "Select apikey from " . $this->tablename ." where email='" . $this->UserEmail() . "'";
        $result = mysql_query($query, $this->connection);  
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            //$this->HandleError("There is no user with email: $email");
            mysql_error ();
            return false;
        }                
         $row = mysql_fetch_assoc($result);  
         
         return $row['apikey'];
    }
    
    function APIexists($apikey)
    {
       if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $query = "Select * from " . $this->tablename ." where apikey='" . $apikey . "'";        
        

        $result = mysql_query($query, $this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
           
           echo mysql_error();
           return false;
        }     
        
        if(mysql_fetch_array($result))
        return true;
    }
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "This is you API Key" . $this->getApiKey() . "Please store it and keep it safely" .
        "You can also access it later on the website" .
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "There was a request to reset your password at ".$this->sitename."\r\n".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['username']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }    
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in UserName");
        $validator->addValidation("password","req","Please fill in Password");
        $validator->addValidation("country", "req", "Please enter you country");
        $validator->addValidation("reason", "req", "Plase give us a reason why you would like to use the application");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
        $formvars['country'] = $this->Sanitize($_POST['country']);
        $formvars['reason'] = $this->Sanitize($_POST['reason']);
    }
    
    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($formvars['email'],$formvars['name']);
        
        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;
        
        $mailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
        "Thanks for your registration with ".$this->sitename."\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "$confirm_url\r\n".
        "\r\n".
        "Regards,\r\n".
        "Webmaster\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This UserName is already used. Please try another username");
            return false;
        }        
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
    
    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }    
    
    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
    
    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "phone_number VARCHAR( 16 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "apikey VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($qry, $this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode =(md5($formvars['email']) + md5($formvars['password']));
        
        $formvars['confirmcode'] = $confirmcode;
        
        $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                username,
                password,
                country,
                reason,
                apikey,
                confirmcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
                "' . md5($formvars['password']) . '",
                "' . $this->SanitizeForSQL($formvars['country']) . '",
                "' . $this->SanitizeForSQL($formvars['reason']) . '",
                "' . md5($this->SanitizeForSQL($formvars['username'])) . '",
                "' . $confirmcode . '"
                )';
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }
    
    
    function generateAPIKey($email)
    {
    
        
    }
    
    
    
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    } 
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }   
    
    
    
    
    
  function CreateTableScores()
    {      
     $sql = 'CREATE TABLE `highscoreapi`.`' . $this->scoreTableName . '` (`id_user` INT NOT NULL auto_increment, `name` VARCHAR(128) NOT NULL, `score` VARCHAR(64) NOT NULL, `age` INT NOT NULL, `additional` VARCHAR(32) NOT NULL, `country` VARCHAR(16) NOT NULL, PRIMARY KEY (`id_user`)) ENGINE = MyISAM';
       // $qry = "Create Table $this->scoretablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "score VARCHAR( 64 ) NOT NULL ,".
                "age INT NOT NULL ,".
                "country VARCHAR( 16 ) NOT NULL ,".
                "additional VARCHAR( 32 ) NOT NULL ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($sql, $this->connection))
        {
           echo 'Creation failed\n\t';
           return false;
        }
        echo 'Create succeed';
        return true;
    }
    
    
      
    
   function EnsureScoretable()
    {       
        $result = mysql_query("SHOW COLUMNS FROM $this->scoreTableName");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTableScores();
        }
        return true;
    }   
    
function EnterScores($name, $score, $age, $country, $additional, $apikey, $overwrite)
{       
    
    $this->scoreTableName .= $apikey;
    if(!$this->DBLogin())
    {
        return false;
    }
    
    
   
    if(!$this->EnsureScoretable())
    {
        
        return false;
    }
   
    
    if(((int)$_GET['overwrite']) != 1)
    {
      //  echo 'overwrite';
         if(!isset($_GET['age']))
                $age = 0;
            
            if(!isset($_GET['additional']))                
                $additional ='';
            
            if(!isset($_GET['country']))
                $country = '';
        
        
        $insert_query = 'insert into '. $this->scoreTableName.'(
                name,
                score,
                age,
                country,
                additional
                )
                values
                (
                "' . $name . '",
                ' . $score . ',
                ' . $age . ',
                "' . $country . '",
                "' . $additional . '"
                )';
        
        if(!mysql_query( $insert_query , $this->connection))
        {
            
           
           return false;
        }       
        
        return true;
    }
    else
    {
         //echo 'No overwrite';
         if(!isset($_GET['age']))
                $age = 0;
            
            if(!isset($_GET['additional']))                
                $additional ='';
            
            if(!isset($_GET['country']))
                $country = '';
            
        $check_query = "Select * from " . $this->scoreTableName . " Where name = '" . $name . "'";
        
        $result = mysql_query($check_query ,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {              
           
            if(!isset($_GET['age']))
                $age = 0;
            
            if(!isset($_GET['additional']))                
                $additional ='';
            
            if(!isset($_GET['country']))
                $country = '';
            
          $updateQuery = "UPDATE  `highscoreapi`. ". $this->scoreTableName . " SET  `score` =  ". $score . ",
`age` = " . $age . ", `country` = '". $country . "', `additional` = ' " . $additional . "' WHERE  `name` = '". $name ."'";
     
          
            if(!mysql_query( $updateQuery , $this->connection))
            {
                
               return false;
            } 
            
        }
 else {
     
        $insert_query = 'insert into '. $this->scoreTableName.'(
                name,
                score,
                age,
                country,
                additional
                )
                values
                (
                "' . $name . '",
                ' . $score . ',
                ' . $age . ',
                "' . $country . '",
                "' . $additional . '"
                )';        
 
 if(!mysql_query( $insert_query , $this->connection))
        {
           
           return false;
        }       
 }
        return true;
    }
}


function RetrieveScores($apikey, $offset, $take)
{
    
    $this->scoreTableName .= $apikey;   
    $this->DBLogin();
    
    if(!$this->APIexists($apikey))
    {
        echo $this->beautify(json_encode($invalidAPI = array("Response" => "Invalid API Key!")));
        return;
    }
    
    if(!$this->EnsureScoretable())
    {
        return false;
    }
    
    $sql = '';
    if(isset($_GET['offset']) && isset($_GET['take']))
    {
        $sql = 'Select name, score, age, additional, country from ' . $this->scoreTableName  . " Limit " . $offset . "," . $take;
    }

 else {        
    $sql = 'Select name, score, age, additional, country from ' . $this->scoreTableName  ;   
 }
    
      $userinfo = array();
    $res = mysql_query($sql);
    if(!$res)
    {
        echo $this->beautify(json_encode(array("Response" => "An internal error occured")));
        echo mysql_error();
        return;        
    }
    while ($row_user = mysql_fetch_assoc($res))
        $userinfo[] = $row_user;

    $json = json_encode(array("Response" => $userinfo));
    if(!$json)   
        echo $this->beautify(json_encode(array("Response" => "Empty!")));
    
        $beauty_json = $this->beautify($json);
        echo $beauty_json;
    
}

function beautify($json)
{
    $beauty_json = '';
    $quote_state = FALSE;
    $level = 0; 

    $json_length = strlen($json);
    $ret= "\n"; 
    $ind= "\t";

    for ($i = 0; $i < $json_length; $i++)
    {
        $pre = '';
        $suf = '';

        switch ($json[$i])
        {
            case '"':                               
                $quote_state = !$quote_state;                                                           
                break;

            case '[':                                                           
                $level++;               
                break;

            case ']':
                $level--;                   
                $pre = $ret;
                $pre .= str_repeat($ind, $level);       
                break;

            case '{':

                if ($i - 1 >= 0 && $json[$i - 1] != ',')
                {
                    $pre = $ret;
                    $pre .= str_repeat($ind, $level);                       
                }   

                $level++;   
                $suf = $ret;                                                                                                                        
                $suf .= str_repeat($ind, $level);                                                                                                   
                break;

            case ':':
                $suf = ' ';
                break;

            case ',':

                if (!$quote_state)
                {  
                    $suf = $ret;                                                                                                
                    $suf .= str_repeat($ind, $level);
                }
                break;

            case '}':
                $level--;   

            case ']':
                $pre = $ret;
                $pre .= str_repeat($ind, $level);
                break;

        }
        
        $beauty_json .= $pre.$json[$i].$suf;
    }    
    //echo $this->indent($json);
    return $beauty_json;
}


function json_to_xml($json) {
    $serializer = new XML_Serializer();
    $obj = json_decode($json);

    if ($serializer->serialize($obj)) {
        $serializer->_createXMLTag("name");
        $serializer->_createXMLTag("age");
        $serializer->_createXMLTag("country");
        $serializer->_createXMLTag("score");
        $serializer->_createXMLTag("additional");
        return $serializer->_serializeArray($obj);
       // return $serializer->getSerializedData();
    }
    else {
        return null;
    }
}


function array_to_xml($student_info, &$xml_student_info) {
    foreach($student_info as $key => $value) {
        if(is_array($value)) {
            if(!is_numeric($key)){
                $subnode = $xml_student_info->addChild("$key");
                $this->array_to_xml($value, $subnode);
            }
            else{
                $this->array_to_xml($value, $xml_student_info);
            }
        }
        else {
            $xml_student_info->addChild("$key","$value");
        }
        
    }    
    return $xml_student_info;
  }
}

class ArrayToXML {

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     * Based on: http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
	 * 
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public static function toXml($data, $rootNodeName = 'data', &$xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
    if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
    if ( is_null( $xml ) ) {
    	 $xml = simplexml_load_string(stripslashes("<?xml version='1.0' encoding='utf-8'?><root xmlns:example='http://example.namespace.com' version='1.0'></root>"));
	}
 
    // loop through the data passed in.
    foreach( $data as $key => $value ) {
 
        // no numeric keys in our xml please!
        $numeric = false;
        if ( is_numeric( $key ) ) {
            $numeric = 1;
            $key = $rootNodeName;
        }
 
        // delete any char not allowed in XML element names
        $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
		
		//check to see if there should be an attribute added (expecting to see _id_)
		$attrs = false;
 
		//if there are attributes in the array (denoted by attr_**) then add as XML attributes
		if ( is_array( $value ) ) {
			foreach($value as $i => $v ) {
				$attr_start = false;
				$attr_start = stripos($i, 'attr_');
				if ($attr_start === 0) {
					$attrs[substr($i, 5)] = $v; unset($value[$i]);
				}
			}
		}
		  
		
        // if there is another array found recursively call this function
        if ( is_array( $value ) ) {
 
            if ( ArrayToXML::is_assoc( $value ) || $numeric ) {
 
                // older SimpleXMLElement Libraries do not have the addChild Method
                if (method_exists('SimpleXMLElement','addChild'))
                {
                    $node = $xml->addChild( $key, null, 'http://www.lcc.arts.ac.uk/' );
					if ($attrs) {
						foreach($attrs as $key => $attribute) {
							$node->addAttribute($key, $attribute);
						}
					}
                }
 
            }else{
                $node =$xml;
            }
 
            // recrusive call.
            if ( $numeric ) $key = 'anon';
            ArrayToXML::toXml( $value, $key, $node );
        } else {
 
                // older SimplXMLElement Libraries do not have the addChild Method
                if (method_exists('SimpleXMLElement','addChild'))
                {
                    $childnode = $xml->addChild( $key, $value, 'http://www.lcc.arts.ac.uk/' );
					if ($attrs) {
						foreach($attrs as $key => $attribute) {
							$childnode->addAttribute($key, $attribute);
						}
					}
                }
        }
    }
 
	// pass back as unformatted XML
	//return $xml->asXML('data.xml');
 
	// if you want the XML to be formatted, use the below instead to return the XML
           // echo $xml->asXML();
	    $doc = new DOMDocument('1.0');
	    $doc->preserveWhiteSpace = false;
	    @$doc->loadXML( ArrayToXML::fixCDATA($xml->asXML()) );
	    $doc->formatOutput = true;
	    //return $doc->saveXML();
	    return $doc->save('data.xml');
	}
 
	public static function fixCDATA($string) {
		//fix CDATA tags
		$find[]     = '&lt;![CDATA[';
		$replace[] = '<![CDATA[';
		$find[]     = ']]&gt;';
		$replace[] = ']]>';	
		
		$string = str_ireplace($find, $replace, $string);	
		return $string;
	}
 
/**
 * Convert an XML document to a multi dimensional array
 * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
 *
 * @param string $xml - XML document - can optionally be a SimpleXMLElement object
 * @return array ARRAY
 */
	public static function toArray( $xml ) {
	    if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
	    $children = $xml->children();
	    if ( !$children ) return (string) $xml;
	    $arr = array();
	    foreach ( $children as $key => $node ) {
	        $node = ArrayToXML::toArray( $node );
	
	        // support for 'anon' non-associative arrays
	        if ( $key == 'anon' ) $key = count( $arr );
	
	        // if the node is already set, put it into an array
	        if ( isset( $arr[$key] ) ) {
	            if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
	            $arr[$key][] = $node;
	        } else {
	            $arr[$key] = $node;
	        }
	    }
	    return $arr;
	}
	
	// determine if a variable is an associative array
	public static function is_assoc( $array ) {
	    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
	}
}
?>