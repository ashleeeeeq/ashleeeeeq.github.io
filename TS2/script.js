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

    document.querySelectorAll(".box").forEach(box => {
        box.style.top = "-100px"; 
        setTimeout(() => box.remove(), 1500);
    });

    var newBox = document.createElement("div");
    newBox.className = "box";
    newBox.style.borderColor = dayColors[day].border;
    newBox.style.backgroundColor = "transparent"; 
    newBox.style.top = "-100px";
    newBox.dataset.bgColor = dayColors[day].bg;

    animationArea.appendChild(newBox);

    setTimeout(() => {
        newBox.style.top = "60%";
    }, 300); 

    setTimeout(checkOverlaps, 100);
}

function checkOverlaps() {
    var boxes = document.querySelectorAll(".box");

    boxes.forEach(box1 => {
        var isOverlapping = false;

        boxes.forEach(box2 => {
            if (box1 !== box2 && isOverlappingElements(box1, box2)) {
                isOverlapping = true;
            }
        });

        if (isOverlapping) {
            box1.style.backgroundColor = box1.dataset.bgColor;
            box1.style.opacity = "0.7";
        } else {
            box1.style.backgroundColor = "transparent";
            box1.style.opacity = "1";
        }
    });

    requestAnimationFrame(checkOverlaps);
}

function isOverlappingElements(el1, el2) {
    var rect1 = el1.getBoundingClientRect();
    var rect2 = el2.getBoundingClientRect();

    return !(
        rect1.right < rect2.left ||
        rect1.left > rect2.right ||
        rect1.bottom < rect2.top ||
        rect1.top > rect2.bottom
    );
}