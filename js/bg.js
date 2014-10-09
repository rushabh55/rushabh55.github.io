window.onload = function () {
    var screenWidth = screen.width - 0.065 * screen.width;
    var screenHeight = screen.height - 0.15 * screen.height;
    var totalObjs = 200;
    var array = new Array();
    function load(a) {
        if (a == null) {
            for (i = 0; i < totalObjs; i++) {
                var leftPad = Math.random() * screenWidth;
                var topPad = Math.random() * screenHeight;
                var img = document.createElement("img");
                img.src = "/cloud.png";
                img.style = "position:absolute; left:" + leftPad + "px; top + " + topPad + "px; width : 100; height: 50; />";
                img.id = i;
                img.blur();
                document.appendChild(img);
            }
        }
        else {
            var leftPad = 0;
            var obj = document.getElementById(a);
            obj.style.left = leftPad + "px";
            obj.blur();
        }
    }


    load(null);
    //document.body.style.backgroundColor = "#758297";
    var k = 0;
    var velocity = parseInt(Math.random() * 4);

    function Physics(a) {
        if (parseFloat(document.getElementById(a).style.left) > screenWidth) {
            load(a);
        }
    }


    function Update() {
        velocity = Math.random() * 2;
        for (i = 0; i < totalObjs; i++) {
            var obj = document.getElementById(i);
            obj.style.left = parseFloat(obj.style.left) + velocity + Math.random() * 2 + 'px';
            Physics(i);
        }

    }
    setInterval(function () { Update() }, 1);
};