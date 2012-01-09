Game Engine in JavaScript for Education
=======================================

Purpose
-------

I want a simple game engine for HTML5 to use when teaching students how to program.

Clarity should be the number one goal for the project.
Fool proof code at no 2. **No cleverness!** I want to help students get *started*, not make a complete game engine.

Unique idea
-----------

The engine has one method (move) that should be updated very often. It should not do anything that causes a re-draw on the screen.
The idea is to keep *deltas* so small between each movement, that all collision detection math becomes very simple.

Another method, that does no movements and no collision detection is responsible for painting stuff.
That method uses - if available - requestAnimationFrame.

Target games and applications
-----------------------------

Examples of stuff I expect my students to recreate:

 * Pong
 * Arkanoid
 * Gorilla (Q-basic)
 * Snake
 * Whack-a-mole
 * (Clay) Pigeon Shooting (Duck Hunt)
 * Game and watch Donkey Kong
 * A simple paint program
 * Animated logos or abstract animations
 * Charts and diagrams
 
