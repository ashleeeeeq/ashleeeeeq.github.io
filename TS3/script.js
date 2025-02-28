const display = document.querySelector(".display-input"); // Selects the input field where the user enters the numbers and operators.
var currentInput = ""; // Stores the current input value.

// Clears the display 
function clearDisplay() {
    currentInput = "";
    display.value = "0";
}

// Function to append the value to the display
function appendValue(value) {
    if (value === "C") {
        clearDisplay(); // Call the clearDisplay function
    } else if (value === "=") {
        calculateResult(); // Call the calculateResult function
    } else {
        if (display.value === "0" && value !== ".") {
            currentInput = value;
        } else {
            currentInput += value;
        }
        display.value = currentInput;
    }
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

// onClick from HTML file
function buttonClick(value) {
    appendValue(value);
}