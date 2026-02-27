/*
 * From: https://courses.cs.washington.edu/courses/cse154/25au/calendar/index.html
   Whack a Bug
 * Handles whacking bugs.
 */

"use strict";
(function() {

  window.addEventListener("load", init);

  const MAX_BUGS = 20;
  let MIN_BUGS = 5;
  const SIZES =['small', 'medium', 'large'];
  
  // Keep track of total bugs spawned to cap at MAX_BUGS
  let totalSpawned = MIN_BUGS; // Starts with 5 bugs spawned
  let bugsWhacked = 0;
  let score = 0;

  // Timer and Game stuffs
  let timerId = null;
  let timeLeft = 30;
  let gameOver = false;

  /**
   * Sets up event listeners for the start button and the bugs.
   */
  function init() {
    let container = id('bug-container');
    clear(container);

    // Initialize timer text, but don't start the countdown yet
    id("timer").textContent = timeLeft;

    populate(container);
  }

  /**
   * Handles the 1-second countdown for the game timer.
   */
  function countdown() {
    timeLeft--;
    id("timer").textContent = timeLeft;
    
    // Loss condition: Time runs out before all bugs are whacked
    if (timeLeft <= 0) {
      endGame(false);
    }
  }

  /**
   * Ends the game, stops the timer, and displays the appropriate status message.
   * @param {boolean} isWin - true if the player won, false if timer ran out.
   */
  function endGame(isWin) {
    clearInterval(timerId);
    gameOver = true;
    
    if (isWin) {
      qs("#game p").textContent = "You Win!";
    } else {
      qs("#game p").textContent = "Game Over.";
    }
  }

  function clear(container) {
    container.innerHTML = '';
  }

  function populate(container) {
    for(let i = 0; i < MAX_BUGS; i++){
      let bug = document.createElement('img');
      bug.classList.add('empty'); // used to query empty cells later
      container.appendChild(bug);
      bug.src='empty.png'; // empty white image, hack to remove outline of img when src is undefined
      bug.id=`bug-${i}`; // used to access each cell later
    }

    // simpler event listener
    id("bug-container").addEventListener("click", whackBug);

    const bugs =[...qsa("#bug-container img.empty")];
    shuffle(bugs);
    
    // after shuffling, get the first 5 bugs and make them visible.
    bugs.slice(0, MIN_BUGS).forEach(bug => {
      bug.src = 'bug.png';
      bug.classList.replace('empty', SIZES[getRandomInt(0, 3)]);
    });

  }

  /**
   * Whacks the clicked bug and increments the score. The bug cannot be whacked again afterwards.
   * After 250ms, resets it to an empty placeholder in the DOM to preserve layout.
   */
  function whackBug(event) {
    // ignorei nteraction if game over
    if (gameOver) return;

    // get exact element that was clicked inside the container
    const bug = event.target;
    
    // only run logic if an image was clicked
    if (bug.tagName !== "IMG") return;

    // ensure we are clicking an active bug, not an empty space or already whacked bug
    if(!bug.classList.contains("whacked") && !bug.classList.contains("empty")) {
      
      // start on first whack
      if (!timerId) {
        timerId = setInterval(countdown, 1000);
      }

      bug.classList.add("whacked");
      bug.src = "bug-whacked.png";
      
      bugsWhacked++;
      
      let points = 1;
      if (bug.classList.contains("small")) {
        points = 3;
      } else if (bug.classList.contains("medium")) {
        points = 2;
      } else if (bug.classList.contains("large")) {
        points = 1;
      }

      score += points;
      id("score").textContent = score;
      id("totalbugs").textContent = bugsWhacked;
      
      // Win condition: Player successfully whacks the 20th bug
      if (bugsWhacked === MAX_BUGS) {
        endGame(true);
      }

      // 250ms delay then replace bug img source with empty again
      setTimeout(() => {
        bug.classList.remove("whacked", "small", "medium", "large");
        bug.classList.add("empty");
        bug.src = "empty.png";
      }, 250);

      // spawn if not max bug
      if (totalSpawned < MAX_BUGS) {
        spawnNewBug();
      }
    }
  }

  /**
   * Spawns a new bug by transforming an existing empty cell, assigning it 
   * a randomized size and ensuring it has a random position in the layout.
   */
  function spawnNewBug() {
    const emptyBugs = qsa("#bug-container img.empty");
    if (emptyBugs.length > 0) {
      const randomEmpty = emptyBugs[getRandomInt(0, emptyBugs.length)];
      randomEmpty.src = 'bug.png';
      randomEmpty.classList.replace('empty', SIZES[getRandomInt(0, 3)]);
      totalSpawned++;
    }
  }


/* --- HELPER FUNCTIONS --- */

  /**
   * Returns the element that has the ID attribute with the specified value.
   * @param {string} name - element ID.
   * @returns {object} - DOM object associated with id.
   */
  function id(name) {
    return document.getElementById(name);
  }

  /**
   * Returns first element matching selector.
   * @param {string} selector - CSS query selector.
   * @returns {object} - DOM object associated selector.
   */
  function qs(selector) {
    return document.querySelector(selector);
  }

  /**
   * Returns an array of elements matching the given query.
   * @param {string} query - CSS query selector.
   * @returns {array} - Array of DOM objects matching the given query.
   */
  function qsa(query) {
    return document.querySelectorAll(query);
  }

  /**
   * From: https://www.geeksforgeeks.org/javascript/how-to-shuffle-the-elements-of-an-array-in-javascript/
   * 
   * Based on the Fisher-Yates (Knuth) Shuffle, iterates through the array in reverse order and
   * swaps each element with a randomly selected element before it.
   * 
   * @param {Array} arr - Array to be shuffled.
   * @returns {Array} Shuffled array
   */
  function shuffle(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
    	const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
  	}
  	return arr;
  }

  /**
   * Taken from an example in: 
   * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Math/random
   * 
   * Randomly generates an integer within the range of
   * min and max. Inclusive of min, exclusive of max.
   * @param {Number} min - Minimum number range (gets the ceiling if not integer)
   * @param {Number} max - Maximum number range (gets the floor if not integer)
   * @returns {Number} Random int between min and max.
   */
  function getRandomInt(min, max) {
    const minCeiled = Math.ceil(min);
    const maxFloored = Math.floor(max);
    return Math.floor(Math.random() * (maxFloored - minCeiled) + minCeiled);
    // The maximum is exclusive and the minimum is inclusive
  }

})();