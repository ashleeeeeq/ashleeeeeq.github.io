// Colors for each day
var dayColors = {
    "Monday": { bg: "#FFFFFF", border: "#F8F8FF" }, 
    "Tuesday": { bg: "#FF5733", border: "#C70039" },
    "Wednesday": { bg: "#FFD700", border: "#B8860B" }, 
    "Thursday": { bg: "#4CAF50", border: "#388E3C" }, 
    "Friday": { bg: "#FF69B4", border: "#C71585" }, 
    "Saturday": { bg: "#6A5ACD", border: "#483D8B" }, 
    "Sunday": { bg: "#FFA500", border: "#FF8C00" }
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