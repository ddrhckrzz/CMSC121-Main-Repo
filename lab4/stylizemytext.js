"use strict";

(function () {
    // js code here
    window.addEventListener('load', init);

    function init() {
        // executes only after the entire DOM is loaded by the browser
        const textArea = id("text-area");
        const btnBigger = id("btn-bigger");
        const checkboxBling = id("checkbox-bling");
        const btnSnoopify = id("btn-snoopify");

        // call bling if checkbox is checked somehow
        bling(textArea, checkboxBling)

        // add listeners
        btnBigger.addEventListener('click', () => {bigger(textArea)});
        checkboxBling.addEventListener('click', () => {bling(textArea, checkboxBling)});
        btnSnoopify.addEventListener('click', () => {snoopify(textArea)})
    }

    /// other functions here idk
    function id(id) {
        return document.getElementById(id);
    }

    function bigger(text) {
        if (text.style.fontSize != "24pt") { //ensures it only does it once
            text.style.fontSize = "24pt";
        }
    }

    function snoopify(text) {
        const sfix = "-izzle";
        var str = text.value.toUpperCase();
        // some code i found on stackoverflow to split a string by line
        // from my understanding it's just regex that checks if there's
        // a newline or a carriage return based on different file systems
        // then splits it by that...
        const chunks = str.split(/\r?\n|\r|\n/g);
        var final = ''; // this should be the final text to put into the thing...
        // just take every word and then insert it into the parts array
        // adding a newline at the end just to keep the structure the same
        chunks.forEach((line) => {
            var parts = line.split(" "); // splits each line by word
            parts.forEach((element, index) => {
                if (!element.endsWith("-izzle")) { // ensures it only does it once
                    // ensure it only replaces the last dot on the string
                    // regex ensures it will only replace those with a .
                    // at the end and ignores the whitespace afterward
                    parts[index] = element.replace(/\.$/, sfix);
                }
            });
            final += parts.join(" ") + '\n';
        }, sfix);
        text.value = final.slice(0, -1) + "!";
    }

    function bling(textArea, checkboxBling){
        textArea.classList[checkboxBling.checked ? 'add' : 'remove']('bling');
    }

}());