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
            console.info("made it bigger");
        }
    }

    function bling(text, checkbox) {
        if (checkbox.checked) {
            text.style.fontWeight = "bold";
            text.style.textDecoration = "underline";
            text.style.color = "green";
            text.animate([
                {
                    opacity: 1,
                },
                {
                    opacity: 0,
                },
                {
                    opacity: 1
                }
            ],{
                duration:2000,
                iterations: Infinity
            });
            text.style.fontFamily = "serif";
            text.style.fontStyle = "italic";
            text.style.backgroundImage = "url('hundred-dollar-bill.jpg')";
            text.style.backgroundRepeat = "repeat";
            console.info("Set to blinged...");
        } else {
            text.style.fontWeight = "normal";
            text.style.textDecoration = "none";
            text.style.color = "black";
            text.getAnimations()[0].cancel();
            text.style.fontFamily = "monospace";
            text.style.fontStyle = "normal";
            text.style.backgroundImage = "none";
            text.style.backgroundRepeat = "none";
            console.info("Set to normal...");
        }
    }

    function snoopify(text) {
        var str = text.value.toUpperCase();
        // some code i found on stackoverflow to split a string by line
        // from my understanding it's just regex that checks if there's
        // a newline or a carriage return
        // then splits it by that...
        const chunks = str.split(/\r?\n|\r|\n/g);
        var parts = [];
        // just take every word and then insert it into the parts array
        // adding a newline at the end just to keep the structure the same
        chunks.forEach((section) => {
            parts = parts.concat(section.split( ));
            parts.push('\n');
        }, parts);
        // pop the last element (it'll be a newline)
        // so that we don't have problems when adding the '!' later
        parts.pop();
        const sfix = "-izzle";
        parts.forEach((element, index) => {
            if (!element.endsWith("-izzle")) { // ensures it only does it once
                // ensure it only replaces the last dot on the string
                // regex ensures it will only replace those with a .
                // at the end and ignores the whitespace afterward
                parts[index] = element.replace(/\.$/, sfix);
            }
        });
        text.value = parts.join(" ") + "!";
    }

}());

/*
razzle me dazzle.
like i swing.
in me castle.
*/