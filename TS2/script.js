// Colors for each day
var dayColors = {
    "Monday": { bg: "white", border: "wheat" },
    "Tuesday": { bg: "red", border: "darkred" },
    "Wednesday": { bg: "yellow", border: "gold" },
    "Thursday": { bg: "green", border: "darkgreen" },
    "Friday": { bg: "pink", border: "deeppink" },
    "Saturday": { bg: "purple", border: "indigo" },
    "Sunday": { bg: "orange", border: "darkorange" }
};

var buttons = document.querySelectorAll(".day-btn");
for (var i = 0; i < buttons.length; i++) {
    (function(button) {
        button.addEventListener("click", function () {
            createAnimatedBox(button.getAttribute("data-day"));
        });
    })(buttons[i]);
}

function createAnimatedBox(day) {
    var animationArea = document.querySelector(".animation-area");
    
    var existingBoxes = animationArea.querySelectorAll(".box");
    for (var j = 0; j < existingBoxes.length; j++) {
        existingBoxes[j].style.animation = "moveUp 1s ease-in-out forwards";
    }

    var newBox = document.createElement("div");
    newBox.className = "box";
    newBox.style.backgroundColor = dayColors[day].bg;
    newBox.style.border = "10px solid " + dayColors[day].border;
    newBox.style.top = "-100px";

    newBox.style.animation = "moveDown 1s ease-in-out forwards, changeColor 1s ease-in-out forwards 0.5s";

    animationArea.appendChild(newBox);

    setTimeout(function() {
        for (var k = 0; k < existingBoxes.length; k++) {
            if (parseInt(existingBoxes[k].style.top) <= -100) {
                existingBoxes[k].remove();
            }
        }
    }, 1000);
}