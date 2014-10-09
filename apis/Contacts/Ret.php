<?php
header('Content-type: image/jpg'); 
class Process
{
    var $db_host;
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
 function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;        
    }
    
     function DBLogin()
    {
        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            //$this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            //$this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            //$this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
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
    
    function EnterIntoDB()
    {
        $this->DBLogin();
        $query = 'INSERT INTO CONTACTSTORE (NAME, EMAIL, PHONE, ADDITIONAL, IMAGE) VALUES (
            "' . $this->SanitizeForSQL($_POST['NAME']) . '" ,
                "' . $this->SanitizeForSQL($_POST['EMAIL']) . '" ,
                    "' . $this->SanitizeForSQL($_POST['PHONE']) . '", 
                        "' . $this->SanitizeForSQL($_POST['ADDITIONAL']) . '",
                            "' . $_POST['IMAGE'] . '")';
        //echo $_POST['IMAGE'];
        //echo base64_decode($_POST['IMAGE']);
        if(!mysql_query($query, $this->connection))
        {
            echo 'Failed';
            return false;
        }
        
        echo json_encode(array( "Response" => "Success"));
    }
    
    
    function Ret()
    {
        $this->DBLogin();
        $query = "Select IMAGE from CONTACTSTORE WHERE ID = " . $_GET['id'];
        $data = mysql_fetch_array($query);        
        //echo $data;
        $img = $data['IMAGE'];
        echo $img;
    }
}



$process = new Process();
$process->InitDB(/*hostname*/'highscoreapi.db.10917081.hostedresource.com',
                      /*username*/'highscoreapi',
                      /*password*/'Rushabh%1',
                      /*database name*/'highscoreapi',
                      /*table name*/'fgusers3');
$process->Ret();
?>

<img src="http://rushabhgosar.com/apis/Contacts/Ret.php?id=9">