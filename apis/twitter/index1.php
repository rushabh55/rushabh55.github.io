        <?php
      
        require './OAuth.php';
        require './twitteroauth.php';
         
         $connection = new TwitterOAuth("zRkL1VQONTQduf9GdWYhkQ", "MTORu7zxaJxoSjZdt9wrEntsggB8EM9ifswWLgSA");
         
         $oauth_callback = 'http://rushabhgosar.com/apis/twitter/Callback.php';
         $request_token = $connection->getRequestToken($oauth_callback);
         $_SESSION['oauth_token'] = $request_token['oauth_token'];
         $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
           //session_start();
           $url = $connection->getAuthorizeURL($request_token);
         header('Location: ' . $url);
         
        ?>