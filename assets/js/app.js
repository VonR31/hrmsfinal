// Ensure the document is fully loaded before executing scripts
$(document).ready(function () {
    // Example of a click event listener for opening the modal
    $('#openModalButton').click(function () {
        $('#addPayrollModal').modal('show');
    });

    // Form submission handler
    $('#formPayroll').submit(function (event) {
        event.preventDefault(); // Prevent the default form submission

        // Extract form data
        var payrollData = {
            name: $('#payrollName').val(),
            startDate: $('#payrollStartDate').val(),
            endDate: $('#payrollEndDate').val(),
            details: $('#payrollDetails').val()
        };

        // Validate form data (example: check if all fields are filled)
        if (payrollData.name && payrollData.startDate && payrollData.endDate && payrollData.details) {
            // Simulate a successful submission (you can replace this with an AJAX call)
            console.log('Form submitted successfully:', payrollData);
            // Close the modal
            $('#addPayrollModal').modal('hide');
            // Optionally, reset the form
            $('#formPayroll')[0].reset();
        } else {
            alert('Please fill in all fields');
        }
    });

   // Additional custom JavaScript can be added here
   
    // For example, date picker initialization, dynamic content loading, etc.
});




