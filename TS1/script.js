var colors = ["#ffb3ba", "#ffdfba", "#ffffba", "#baffc9", "#bae1ff", "#f8f8ff"];
var index = 0;

function changeColor() {
    document.querySelector(".resume").style.backgroundColor = colors[index];
    index = (index + 1) % colors.length; // Cycle through colors
}