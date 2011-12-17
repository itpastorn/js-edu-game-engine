<?php
/*
 * Game engine that uses requestAnimationFrame
 *
 * move() is updated very often
 * draw() only when needed
 * 
 * PHP only used for these comments (doc generation)
 * @author Lars Gunther <gunther@keryx.se>
 * @license MIT
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Game engine idea</title>
  <style>
    #domball {
        width: 10px;
        height:10px;
        background-color: blue;
        border-radius: 50%;
        position: absolute;
        top: 1px;
        left:1px;
    }
    #wall {
        position: absolute;
        top: 0;
        left: 0;
        width: 150px;
        height: 200px;
        border: 1px solid;
    }
    #domballstart {
        position: absolute;
        top: 210px;
    }
    #cv {
        width: 150px;
        height: 200px;
        position: absolute;
        top: 0;
        left: 200px;
        border: 1px solid;
    }
    #cvstart {
        position: absolute;
        top: 210px;
        left: 225px;
    }
    #svg {
        width: 150px;
        height: 200px;
        position: absolute;
        top: 0;
        left: 400px;
        border: 1px solid;
    }
    #svgballstart {
        position: absolute;
        top: 210px;
        left: 425px;
    }
    #webgl {
        
    }
    #webglstart {
        
    }
    #log {
        position: absolute;
        top: 240px;
        font-family: sans-serif;
    }
  </style>
</head>
<body>
<div id="domball"></div>
<div id="wall"></div>
<button id="domballstart">HTML DOM ball</button>
<canvas id="cv" width="150" height="200">Canvas ball demo</canvas>
<button id="cvstart">Canvas ball</button>
<svg id="svg" width="150" height="200" viewport="0 0 150 200">
  <circle cx="5" cy="5" r="5" id="svgball" fill="red" />
</svg>
<button id="svgballstart">SVG ball</button>
<div id="log"></div>
<script src="kxengine.js"></script>
<script>
window.onerror = function (msg, url, line) {
    var log = document.getElementById("log");
    log.innerHTML = log.innerHTML += "<dl><dt>Error msg</dt><dd> " + msg + "</dd>\n";
    log.innerHTML = log.innerHTML += "<dl><dt>Error url</dt><dd> " + url + "</dd>\n";
    log.innerHTML = log.innerHTML += "<dl><dt>Error line</dt><dd> " + line + "</dd></dl>\n";
}

function log(msg) {
    var log = document.getElementById("log");
    log.innerHTML = log.innerHTML += "<dl><dt>Log</dt><dd> " + msg + "</dd>\n";
}

var domball = {
    left: 1
};
domball.dom = document.getElementById("domball");

// All math to collision detect, etc goes here
// Move in memory and collision detect
domball.move = function () {
    if (domball.isMoving() === false) {
    	return false;
    }
    // Stop moving when ball has returned to the top
    if (domball.top > 0) {
        // simulate a wall at the bottom
        if (domball.top >= 190) {
            domball.ySpeed = domball.ySpeed * -1;
        }
        domball.top += domball.ySpeed;
        // Simulate a wall at 1px left and 151px left
        if (domball.left < 1) {
            domball.xSpeed = Math.abs(domball.xSpeed);
        }
        if (domball.left > 141) {
            domball.xSpeed = -Math.abs(domball.xSpeed);
        }
        domball.left += domball.xSpeed;
    } else {
        domball.isMoving(false);
    }
}
// Just update the screen, no math
domball.draw = function () {
    domball.dom.style.left = domball.left + "px";
    domball.dom.style.top  = domball.top + "px";
}
domball.isMoving = function (toggle) {
    if ( typeof toggle === "boolean" ) {
        domball.moving = toggle;
    }
    return domball.moving;
}

// @todo Expand this and make canvsobjects a library of its own
// Canvasobjects will inherit methods from this one
var canvasobjects = {
    setContext: function (id) {
        canvasobjects.cvs = document.getElementById(id);
        canvasobjects.ctx = canvasobjects.cvs.getContext('2d');
        return canvasobjects.ctx;
    },
    // @todo Next one is not working, method should be set at this level (proto)
    // but the moving property is specific to all objects 
    // In order to fix this we must bind "this"...
    isMoving : function (toggle) {
        if ( typeof toggle === "boolean" ) {
            canvasobjects.moving = toggle;
        }
        return canvasobjects.moving;
    }
    
}
canvasobjects.setContext('cv');

canvasgame =  Object.create(canvasobjects);
canvasball =  Object.create(canvasobjects);

canvasgame.draw = function () {
    canvasgame.ctx.clearRect(0, 0, 150, 200);
}
canvasball.draw = function () {
    canvasball.ctx.fillStyle = "maroon";
    canvasball.ctx.beginPath();
    canvasball.ctx.arc(canvasball.x, canvasball.y, 5, 0, Math.PI * 2, true);
    canvasball.ctx.closePath();
    canvasball.ctx.fill();
}
// Start values
canvasball.x = 6;
canvasball.xSpeed = 4;

canvasball.move = function () {
    if (canvasball.isMoving() === false) {
        return false;
    }
    // Stop moving when ball has returned to the top
    if (canvasball.y > 4) {
        // simulate a wall at the bottom
        if (canvasball.y >= 195) {
            canvasball.ySpeed = canvasball.ySpeed * -1;
        }
        canvasball.y += canvasball.ySpeed;
        // Simulate a wall at 0 left and 150 left
        if (canvasball.x < 6 || canvasball.x > 145) {
            canvasball.xSpeed = canvasball.xSpeed * -1;
        }
        canvasball.x += canvasball.xSpeed;
    } else {
        canvasball.isMoving(false);
    }
}

// Idea: Ball factory...

// Handling SVG is almost identical to HTML DOM
var svgball = {
    cx: 1
};
svgball.dom = document.getElementById("svgball");

// All math to collision detect, etc goes here
// Move in memory and collision detect
svgball.move = function () {
    if (svgball.isMoving() === false) {
        return false;
    }
    // Stop moving when ball has returned to the top
    if (svgball.cy > 0) {
        // simulate a wall at the bottom
        if (svgball.cy >= 190) {
            svgball.ySpeed = svgball.ySpeed * -1;
        }
        svgball.cy += svgball.ySpeed;
        // Simulate a wall at 1px left and 151px left
        if (svgball.cx < 1 || svgball.cx > 141) {
            svgball.xSpeed = svgball.xSpeed * -1;
        }
        svgball.cx += svgball.xSpeed;
    } else {
        svgball.isMoving(false);
    }
}
// Just update the screen, no math
svgball.draw = function () {
    svgball.dom.setAttribute("cx", svgball.cx);
    svgball.dom.setAttribute("cy", svgball.cy);
}
svgball.isMoving = function (toggle) {
    if ( typeof toggle === "boolean" ) {
        svgball.moving = toggle;
    }
    return svgball.moving;
}

// @todo Slider to change speed
KXENGINE.setSpeed(200);
KXENGINE.register(domball);
KXENGINE.registerBackground(canvasgame);
KXENGINE.register(canvasball);
KXENGINE.register(svgball);

document.getElementById("domballstart").onclick = function () {
    canvasball.isMoving(false);
    svgball.isMoving(false);
    domball.isMoving(true);
    domball.top    = 1;
    domball.xSpeed = 4;
    domball.ySpeed = 0.5;
    KXENGINE.start();
}

document.getElementById("cvstart").onclick = function () {
    domball.isMoving(false);
    svgball.isMoving(false);
    canvasgame.draw();
    canvasball.y = 5;
    canvasball.ySpeed = 0.5;
    canvasball.move();
    canvasball.draw();
    canvasball.isMoving(true);
    KXENGINE.start();
}

document.getElementById("svgballstart").onclick = function () {
    canvasball.isMoving(false);
    domball.isMoving(false);
    svgball.isMoving(true);
    svgball.cy     = 1;
    svgball.xSpeed = 4;
    svgball.ySpeed = 0.5;
    KXENGINE.start();
}
</script>
</body>
</html>
