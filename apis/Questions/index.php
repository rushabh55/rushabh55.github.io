<?php

/**
 * @author Rushabh
 * @copyright 2013
 */

class QSave {
    var $hostName = "highscoreapi.db.10917081.hostedresource.com";
    var $dbUser = "highscoreapi";
    var $dbPass = "Rushabh%1";
    var $siteName = "rushabhgosar.com";
    var $dbName = "highscoreapi";
    var $randKey = "rushabhGosar!2$1";
    var $connection;
    
    public function DBLogin() {
        $this->connection = mysql_connect($this->hostName, $this->dbUser, $this->dbPass);
        if(!$this->connection) {
            $this->HandleError("Db Login Failed! ");
            return false;
        }        
        mysql_select_db($this->dbName,$this->connection);        
        return true;
    }
    
    public function saveQuestion($question, $keywords, $difficulty) {        
            if(!$difficulty) {
                $difficulty = "easy";
                return false;
            }
            if(!question){                
                return false;
            }
            if(!$this->DBLogin()) {                
                return false;
            }
            
            $query = 'INSERT INTO QUESTIONS (QUESTION, KEYWORDS) VALUES (
            "' . $question . '", 
            "' . $keywords . '");';
            
            if(!mysql_query($query, $this->connection )) {
                echo '<br />' . $query . '<br />';
                $this->HandleError(mysql_error($this->connection));
                return false;
            }
            
            return true;
        }
        
        public function retrieve($id = 0){
                  if(!$this->DBLogin()) {                
                    return false;
                }
                $query = 'SELECT * FROM QUESTIONS';
                $res = mysql_query($query, $this->connection);
                if(!$res) {
                    echo '<br />' . $query . '<br />';
                    $this->HandleError(mysql_error($this->connection));
                    return false;
                }
               
                while($rs=mysql_fetch_assoc($res))
                {
                    $arr[] = $rs;  
                }
                if(!$arr)
                    echo json_encode(array("Response" => "Failed Error"));
                else{}
                    //echo $this->beautify(json_encode($arr));    
                return $arr;
        }
        
        function RandomQ(){
            //$this->HandleError("Randomizing!");
            $result = $this->retrieve();
            //$this->HandleError("List acquired!!");
            $size = sizeof($result);
            $elem = rand(0,$size);
            //$this->HandleError("size: " . $size . " elem:" . $elem);
            $tp[] = $result[$elem];
            //$tp[] = $result['KEYWORDS'][$elem];
            //var_dump($tp);
            echo json_encode($tp);
        }
        
        function callSOAP(){
            $wcfClient = new SoapClient('http://awa-v2.cloudapp.net/EvalService.svc?wsdl', array("trace" => 1, "connection_timeout"=>1000)); 
            $args = array('text' => @'Fortunately, when I worked with Joyce Carol Oates on The Best American Essays of the Century (that’s the last century, by the way), we weren’t restricted to ten selections. So to make my list of the top ten essays since 1950 less impossible, I decided to exclude all the great examples of New Journalism--Tom Wolfe, Gay Talese, Michael Herr, and many others can be reserved for another list. I also decided to include only American writers, so such outstanding English-language essayists as Chris Arthur and Tim Robinson are missing, though they have appeared in The Best American Essays series. And I selected essays, not essayists. A list of the top ten essayists since 1950 would feature some different writers.Fortunately, when I worked with Joyce Carol Oates on The Best American Essays of the Century (that’s the last century, by the way), we weren’t restricted to ten selections. So to make my list of the top ten essays since 1950 less impossible, I decided to exclude all the great examples of New Journalism--Tom Wolfe, Gay Talese, Michael Herr, and many others can be reserved for another list. I also decided to include only American writers, so such outstanding English-language essayists as Chris Arthur and Tim Robinson are missing, though they have appeared in The Best American Essays series. And I selected essays, not essayists. A list of the top ten essayists since 1950 would feature some different writers.Fortunately, when I worked with Joyce Carol Oates on The Best American Essays of the Century (that’s the last century, by the way), we weren’t restricted to ten selections. So to make my list of the top ten essays since 1950 less impossible, I decided to exclude all the great examples of New Journalism--Tom Wolfe, Gay Talese, Michael Herr, and many others can be reserved for another list. I also decided to include only American writers, so such outstanding English-language essayists as Chris Arthur and Tim Robinson are missing, though they have appeared in The Best American Essays series. And I selected essays, not essayists. ', 'maxMarks' => 6, 'minMarks' => 1, 'step' => 1, maxWords => 300, 'keywords' => array('Facebook', 'Donate', 'Like', 'Stay', 'in', 'touch', 'with', 'games', 'simulations', 'country', 'never', 'spam', 'name', 'email', 'address','Twitter','Hello', 'World'));
    
            $response = $wcfClient->Evaluate($args);
	    var_dump($response);
	    echo '<br/><br/>';
            echo $response->EvaluateResult;
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
    function StripSlashes($str) {    
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }   
        
    
    
    public function HandleError($error) {
        echo '<br />' . $error . '<br />';
    } 
    }
    
    
    
    $save = new QSave();
    if(isset($_POST['QUESTION']) && isset($_POST['KEYWORDS'])) {      
            if($save->saveQuestion($_POST['QUESTION'], $_POST['KEYWORDS'], "  a")) {
                echo json_encode(array("Response" => "Success"));
            }
            else
                echo json_encode(array("Response" => "Internal Error"));
        }
        else {          
            if($_GET['r']) {
                //echo 'ret';
                $save->retrieve();
                }
            if($_GET['g']){
                $save->RandomQ();
            }
            if($_GET['p']){
                 $save->callSOAP();
            }
        }
            

?>