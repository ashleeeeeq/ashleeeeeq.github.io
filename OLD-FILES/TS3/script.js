const display = document.querySelector(".display-input"); // Selects the input field where the user enters the numbers and operators.
var currentInput = ""; // Stores the current input value.

// Clears the display 
function clearDisplay() {
    currentInput = "";
    display.value = "0";
}

// Performs basic calculations: Addition (+), Subtraction (-), Multiplication (*), and Division (/).
function calculateResult() {
    try {
        let parts = currentInput.split(/([\+\-\*\/])/); // Split numbers and operators
        let result = parseFloat(parts[0]); // Start with the first number

        for (let i = 1; i < parts.length; i += 2) {
            let operator = parts[i];
            let nextNumber = parseFloat(parts[i + 1]); 

            if (isNaN(nextNumber)) throw "Error"; // Handle invalid input

            // Perform the corresponding arithmetic operation
            if (operator === "+") result += nextNumber;
            else if (operator === "-") result -= nextNumber;
            else if (operator === "*") result *= nextNumber;
            else if (operator === "/") {
                if (nextNumber === 0) throw "Error"; // Prevent division by zero
                result /= nextNumber;
            }
        }

        currentInput = result.toString();
        display.value = currentInput;
    } catch {
        display.value = "Error";
        currentInput = "";
    }
}

// Function to append values to the display
function appendValue(value) {
    if (value === "C") {
        clearDisplay(); 
        return;
    }

    if (value === "=") {
        calculateResult(); 
        return;
    }

    // Prevent operators from being entered first
    if (currentInput === "" && "+-*/".includes(value)) {
        alert("Invalid format. Please enter a number first.");
        return;
    }

    // Prevent multiple consecutive operators
    if ("+-*/".includes(value) && "+-*/".includes(currentInput.slice(-1))) {
        return;
    }

    let lastNumber = currentInput.split(/[\+\-\*\/]/).pop(); // Get the last entered number
    // Prevent multiple dots in a single number
    if (value === "." && lastNumber.includes(".")) {
        return; 
    }

    // If a dot is entered without a preceding number, prepend "0"
    if (value === "." && (lastNumber === "" || "+-*/".includes(currentInput.slice(-1)))) {
        value = "0.";
    }

    // If starting with "0" and entering a number, replace "0"
    if (currentInput === "0" && !"+-*/".includes(value) && value !== ".") {
        currentInput = value;
    } else {
        currentInput += value;
    }

    // Update the display
    display.value = currentInput;
}

// onClick from HTML file
function buttonClick(value) {
    appendValue(value);
}