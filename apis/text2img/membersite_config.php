<?PHP
require_once("fg_membersite.php");

$fgmembersite = new FGMembersite();

//Provide your site name here
$fgmembersite->SetWebsiteName('rushabhgosar.com');

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail('me@rushabhgosar.com');

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time
$fgmembersite->InitDB(/*hostname*/'highscoreapi.db.10917081.hostedresource.com',
                      /*username*/'highscoreapi',
                      /*password*/'Rushabh%1',
                      /*database name*/'highscoreapi',
                      /*table name*/'fgusers3');

//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it here
$fgmembersite->SetRandomKey('RuShaBH1RIT@1');

?>