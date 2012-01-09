/**
 * Simple game flow engine for new programmers
 *
 * Purpose: To be used for simple games in teaching
 * Agnostic for HTML DOM, SVG, Canvas and WebGL
 *
 * Collision detection must be done in the moving objects
 * This script is only the main controler
 * 
 * Usage:
 * 1. Define background and movable objects according to interface
 * 2. Register those objects
 * 3. Set game speed (non mandatory step)
 * 4. Call the start method
 *
 * @todo Make documentation and demos!
 * @todo Rewrite using some smart pattern to make properties non public using ES5
 * @todo "use strict";
 *
 * @license MIT (probably...)
 */

// @todo Capability check and polyfill requestAnimationFrame
if ( typeof mozRequestAnimationFrame !== "function") {
    throw new Error("Sorry, Firefox only during initial development");
}

// Shim by Paul Irish
window.requestAnimFrame = (function(w){
  return  w.requestAnimationFrame ||
          w.webkitRequestAnimationFrame ||
          w.mozRequestAnimationFrame ||
          w.oRequestAnimationFrame ||
          w.msRequestAnimationFrame ||
          function(/* function FrameRequestCallback */ callback, /* DOMElement Element */ element){
            w.setTimeout(callback, 1000 / 60);
          };
})(window);

var KXENGINE = {

    // All moving objects that are handled by the engine goes here
    // Protected
    movables : [],

    // The game "boards" - non-moving but may need updates
    // Use with non retained graphics (Canvas, WebGL)
    // Protected
    backgrounds : [],
    
    // How often should in-memory updates be done
    // Protected
    movetime : 10,
    
    // Moving objects should be added to the main functions using this method
    // @todo check that movable is an object that has all necessary methods (interface...?)
    // Must have: obj.move(), obj.draw(), obj.isMoving()
    register : function(object) {
        if ( typeof object.movid === "number" ) {
            throw new Error("Object " + object.movid + " already registered");
        }
        // More checks here...
        var place = KXENGINE.movables.length;
        KXENGINE.movables[place] = object;
        object.movid = place;
        return place;
    },

    // Non moving objects that may need to be refreshed for non retained graphics
    registerBackground : function(object) {
        // Perform checks here...
        var place = KXENGINE.backgrounds.length;
        KXENGINE.backgrounds[KXENGINE.movables.length] = object;
        object.backgroundid = place;
    },

    // If an object is removed (as in space invaders ship destroyed) it should be
    // un-registered
    unRegister : function(object) {
        // @todo investigate if we can keep this array dense
        delete KXENGINE.movables[object.movid];
        delete object.movid;
        // @todo de-register backgrounds e.g. when reaching a new level
    },
    
    // Start over
    // @todo Investigate why it does not work
    unRegisterAll : function() {
        KXENGINE.movables = [];
        KXENGINE.backgrounds = [];
    },
    
    // Check if an object is registered
    isRegistered : function(id) {
        return typeof KXENGINE.movables[id] !== "undefined";
        // @todo repeat for backgrounds
    },

    // This function updates movement in memory
    move : function () {
        var something_is_moving = false;
        for (var i=0, len = KXENGINE.movables.length; i < len; i += 1) {
            if ( typeof KXENGINE.movables[i] !== "undefined" && KXENGINE.movables[i].isMoving()) {
                KXENGINE.movables[i].move();
                something_is_moving = true;
            }
        }
        if ( something_is_moving ) {
            setTimeout(KXENGINE.move, KXENGINE.movetime);
        } else {
            // @todo remove this log
            log("ALL movement stopped (reload to start again)");
            // @todo Movements must be resumable
        }
        return something_is_moving;
    },

    // This function updates the screen (paints)
    // To fix problems mentioned below we probably need 2 functions: One for retained mode and one for immediate mode graphics
    draw : function () {
        // @todo Fix logic problem. This loop seems to run once to clear the screen when when game is over.
        for (var i=0, len = KXENGINE.backgrounds.length; i < len; i += 1) {
            if ( typeof KXENGINE.backgrounds[i] !== "undefined" ) {
                KXENGINE.backgrounds[i].draw();
            }
        }
        // No need to repaint when nothing is moving
        // Please note for canvas and WebGL that standing still = moving if you want it painted!
        // @todo Must fix this discrepancy between retained mode and immediate mode graphics
        var something_is_moving = false;
        for (var i = 0, len = KXENGINE.movables.length; i < len; i += 1) {
            if ( typeof KXENGINE.movables[i] !== "undefined" && KXENGINE.movables[i].isMoving()) {
                KXENGINE.movables[i].draw();
                something_is_moving = true;
            }
        }
        if ( something_is_moving ) {
            mozRequestAnimationFrame(KXENGINE.draw);
            return true; // Something has moved
        }
        return false; // Nothing has moved
    },
    
    // Sets movetime to change game speed
    // De-coupled from screen updates!
    setSpeed : function (fps) {
        // Check that fps has an acceptable value
        // @todo Capability detect max FPS
        if ( typeof fps !== "number" ) {
            throw new Error("Argument fps must be a number for KXENGINE.setSpeed()");
        }
        if ( fps < 10 || fps > 250 ) {
            throw new Error("Argument fps must be between 10 and 250 for KXENGINE.setSpeed()");
        }
        if ( fps ) {
            KXENGINE.movetime = 1000/fps;
        } else {
            KXENGINE.movetime = 10; // Reset to default
        }
        return KXENGINE.getSpeed();
    },
    
    // Get frames per second for in memory movements
    // Not for screen updates
    getSpeed : function () {
        return 1000 / KXENGINE.movetime;
    },
    
    // Get frames per seconds for actual updates on screen
    // Since these are decoupled from game engine math
    // they do not measure game code quality 
    getFps : function () {
        throw new Error("Method getFps not implemented yet!");
    },

    // Pausing the game
    // Returns an array with the movid of all paused objects
    pause : function () {
        throw new Error("Method pause() not implemented yet!");

        var paused = [];
        var pausnum = 0;
        for (var i=0, len = KXENGINE.movables.length; i < len; i += 1) {
            if ( typeof KXENGINE.movables[i] !== "undefined" && KXENGINE.movables[i].isMoving()) {
                KXENGINE.movables[i].isMoving(false);
                paused[pausnum] = KXENGINE.movables[i].movid;
                pausnum += 1;
            }
        }
        return paused;
    },

    // Unpausing the game
    // Takes an array with all paused objects
    // Returns true if at least one object has resumed moving
    unpause : function (paused) {
        throw new Error("Method unpause() not implemented yet!");

        var something_is_moving = false;
        for (var i=0, len = KXENGINE.paused.length; i < len; i += 1) {
            KXENGINE.movables[i].isMoving(false);
            something_is_moving = true;
        }
        return something_is_moving;
    },
    
    // No start if already started...
    running : false,
    
    // Begin the game!
    start : function() {
        if ( KXENGINE.movables.length < 1 ) {
            throw new Error("No moving objects registered");
        }
        if ( !KXENGINE.running ) {
            KXENGINE.running = true;
            KXENGINE.move();
            KXENGINE.draw();
            return true;
        }
        return false;
    }
};


/*
Interfaces

Necessary properties on a movable object
o.move()             - Move in memory and do collision detection
o.draw()             - Move on screen (paint)
o.isMoving([toggle]) - when true o.move, and o.draw should be called
                       when called with true movements starts or resumes
                       when called with false movements stops
o.movid              - to enable deRegister, e.g. when the object is destroyed

If o.isMoving() returns false then neither obj.move(), nor obj.draw() will be called

Necessary properties on a background objects
o.draw()       - Should clear (part of) the screen and draw background items
o.backgroundid - to enable deRegister
*/

// Check out
// http://hacks.mozilla.org/2011/08/animating-with-javascript-from-setinterval-to-requestanimationframe/
// http://jsfiddle.net/THEtheChad/RUsnb/ (no animation que - a good idea????)
// http://billmill.org/static/canvastutorial/
