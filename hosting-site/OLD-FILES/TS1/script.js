var colors = [
    { background: "#633901", font: "#fffcd7" },
    { background: "#090013", font: "#fef7ff" },
    { background: "#d2f9fe", font: "#09001b" },
    { background: "#fef7ff", font: "#310330" }
]; // Define colors (background and font)
var index = 0;

function changeColor() {
    var resumeElement = document.querySelector(".resume");
    resumeElement.style.backgroundColor = colors[index].background;
    resumeElement.style.color = colors[index].font; // Change font color
    index = (index + 1) % colors.length; // Cycle through colors
}