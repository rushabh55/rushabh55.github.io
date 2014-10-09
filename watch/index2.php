<?php

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title></title>        
      <script>
          function GetTubeSource() {
              var uri = document.getElementById("txtUrl").value;
              alert('Acquiring!');
              var xmlhttp;
              if (window.XMLHttpRequest) {
                  xmlhttp = new XMLHttpRequest();
                  alert('Acquiring XMLHttp!');
              }
              else {// code for IE6, IE5
                  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                  alert('AX!');
              }
              alert(xmlhttp.responseText);
              xmlhttp.onreadystatechange = function () {
                  if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                      alert('off for parsing!');
                      ParseResponse(xmlhttp.responseText);
                      
                  }
              }
              alert('Finishing!');
              xmlhttp.open("GET", uri, true);
              xmlhttp.send();
          }


          function ParseResponse(res) {
              try {
                  alert('Parsing!');
                  response = res;
                  var mp = "id=\"movie_player\"";
                  var s1 = response.indexOf(mp);
                  var s2;
                  if (navigator.appVersion.match("MSIE") != null) {
                      var paramflash = "name=\"flashvars\" value=\"";
                      s2 = response.indexOf(paramflash, s1) + paramflash.length;
                  }
                  else {
                      var flashvar = "flashvars=";
                      s2 = response.indexOf(flashvar, s1) + flashvar.length;
                  }

                  var end = response.indexOf("\"", s2);
                  var str = response.substring(s2, end);

                  w = str.split("&amp;");

                  for (i = 0; i <= w.length - 1; i++)
                      if (w[i].split("=")[0] == "url_encoded_fmt_stream_map") {
                          links = unescape(w[i].split("=")[1]);
                          break;
                      }
                  abc = links.split(",url="); for (i = 0; i <= abc.length - 1; i++) {
                      fmt = abc[i].split("|")[0];
                      if ((fmt.indexOf("flv") > 0) && (fmt.indexOf("large") <= 0)
                               && (fmt.indexOf("medium") <= 0)) {
                          if (fmt.indexOf("rl=") > 0) {
                              url = fmt.substring(4, fmt.indexOf("fallback_host") - 1);
                              url = unescape(unescape(url));
                              break;
                          }
                          else {
                              url = fmt.substring(0, fmt.indexOf("fallback_host") - 1);
                              url = unescape(unescape(url)); break;
                          }
                      }
                  }
                  combineurl = url + '&title=' + GetTubeTitle();
                  window.clipboardData.setData('Text', combineurl);
                  window.location.href = combineurl;
              }
              catch (ex) {
                  alert(ex.Message);
              }
              finally {
                  EnableForm();
              }
          }
          function GetTubeTitle() {
              try {
                  alert('fetching title!');
                  var sT = "<meta name=\"title\" content=\"";
                  var s = response.indexOf(sT) + sT.length;
                  var e = response.indexOf("\"", s);

                  var title = response.substring(s, e);
                  if (title.match(".$"))
                      title = title.substring(0, title.length - 1);

                  return title;
              }
              catch (ex) {
                  return "";
              }
          }
      </script>
    </head>
    <body>
           
              <!--<div id="vid" style="display: none;"></div>-->
              <h4>YouTube Video URL</h4>
              <input type="text" name="txtUrl" id="txtUrl" placeholder="YouTube Video URL like: www.youtube.com/watch?v=5KlnlCq2M5Q" style="width: 550px">
              <div>
              <button id="download-btn" onclick="GetTubeSource()" ><i class="icon-download-alt icon-white"></i> Download Video</button>
              <img id="loading" alt="Loading..." style="border-width:0; display: none;" src="img/loading.gif" />
                  <br /><label id="status" >Status</label>
              </div>
            
    </body>
</html>
