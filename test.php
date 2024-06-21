<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Example</title>
</head>
<body>

  <label for="mySelect">Select a value:</label>
  <select id="mySelect" onchange="printSelectedValue()">
    <option value="option1">Option 1</option>
    <option value="option2">Option 2</option>
    <option value="option3">Option 3</option>
  </select>

  <script>
    function printSelectedValue() {
      // Get the select element
      var selectElement = document.getElementById("mySelect");

      // Get the selected value
      var selectedValue = selectElement.value;

      // Print the selected value in the console
      console.log("Selected value: " + selectedValue);
    }
  </script>

</body>
</html>
