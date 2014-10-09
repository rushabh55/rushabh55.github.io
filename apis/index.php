<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
<head>
    <script async>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r; i[r] = i[r] || function () {
                (i[r].q = i[r].q || []).push(arguments)
            }, i[r].l = 1 * new Date(); a = s.createElement(o),
        m = s.getElementsByTagName(o)[0]; a.async = 1; a.src = g; m.parentNode.insertBefore(a, m)
        })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');
        ga('create', 'UA-35391718-2', 'rushabhgosar.com');
        ga('send', 'pageview');

        function onRegNavigate() {
            document.location = "register.php";
        }

        function onCRNavigate() {
            document.location = "confirmreg.php";
        }

        function onLoginNavigate() {
            document.location = "login.php";
        }
</script>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
      <title>Rushabh's API</title>
      <link rel="STYLESHEET" type="text/css" href="style/fg_membersite.css">
</head>
<body>
<div id='fg_membersite_content'>
    <h1><img src="http://rushabhgosar.com/apis/text2img?msg=Rushabh's API Registration Website!" /></h1>
    <br />
    <button onclick="onRegNavigate()"><img src="http://rushabhgosar.com/apis/text2img?msg=Register" /></button>    
    <br />
    <button onclick="onCRNavigate()"><img src="http://rushabhgosar.com/apis/text2img?msg=Confirm Registration" /></button>
    <br />
    <button onclick="onLoginNavigate()"><img src="http://rushabhgosar.com/apis/text2img?msg=Login" /></button>
</div>
</body>
</html>
