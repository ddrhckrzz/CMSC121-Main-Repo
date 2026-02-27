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
  const SIZES = ['small', 'medium', 'large']

  /**
   * Sets up event listeners for the start button and the bugs.
   */
  function init() {
    let container = id('bug-container');
    clear(container);

    populate(container);

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

    bindListeners(qsa("#bug-container img"));

    const bugs = [...qsa("#bug-container img.empty")];
    shuffle(bugs);
    
    // after shuffling, get the first 5 bugs and make them visible.
    bugs.slice(0, MIN_BUGS).forEach(bug => {
      bug.src = 'bug.png';
      bug.classList.replace('empty', SIZES[getRandomInt(0, 3)]);
    });

  }

  function bindListeners(bugs) {
    for(let i = 0; i < bugs.length; i++) {
      bugs[i].addEventListener("click", whackBug);
    }
  }

  /**
   * whacks the clicked bug and increments the score. The bug cannot be whacked again afterwards.
   */
  function whackBug(event) {
    if(!event.currentTarget.classList.contains("whacked")) {
      event.currentTarget.classList.add("whacked");
      event.currentTarget.src = "bug-whacked.png";
      let score = id("score");
      let total = parseInt(score.textContent) + 1;
      // Need to convert the string content into a number.
      score.textContent = total;
      if (total === qsa("#bug-container img").length) {
        qs("#game p").textContent = "All bugs have been whacked";
      }
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
