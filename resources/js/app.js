import './bootstrap';


/**
 * Connections Table has checkboxes
 * I want to show `csf -td` commands for each selected IP
 */
document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('table.connections');
    const codeElement = document.querySelector('pre#commands');

    table.addEventListener('change', (event) => {
        if (event.target.type === 'checkbox') {

            const csfDuration = document.querySelector('#csf_ttl').value;

            const selectedValues = [];

            // Get all checked checkboxes within the table
            const checkboxes = table.querySelectorAll('input[type="checkbox"]:checked');

            // Collect the values of all checked checkboxes
            checkboxes.forEach((checkbox) => {
                selectedValues.push(checkbox.value);
            });

            // Generate the output with each value on a new line
            if (selectedValues.length) {
                codeElement.textContent = selectedValues.map(value => `csf -td ${value} ${csfDuration}`).join('\n') + '\n';
            } else {
                codeElement.textContent = 'Tick some boxes...';
            }
        }
    });

});

/**
 * Commands box has Duration select, needs to update when changed
 */
document.addEventListener('DOMContentLoaded', () => {
    const csfDuration = document.querySelector('#csf_ttl');
    csfDuration.addEventListener('change', (event) => {
        // Select all the table.connection checkboxes, and trigger a 'change' event.
        const checkbox = document.querySelector('table.connections input[type="checkbox"]');
        if (checkbox) {
            // Create a new 'change' event
            const changeEvent = new Event('change', {
                'bubbles': true,  // Allow the event to bubble up to the parent (optional)
                'cancelable': true // Allows the event to be canceled (optional)
            });
            // Dispatch (trigger) the 'change' event
            checkbox.dispatchEvent(changeEvent);
        }
    });
});

/**
 * Commands box needs a 'copy' button
 */
document.addEventListener('DOMContentLoaded', () => {
    const copyButton = document.querySelector('.copy button'); // Select the button with class "copy"
    const preElement = document.getElementById('commands'); // Select the <pre> element by ID

    copyButton.addEventListener('click', () => {
        // Copy the text inside the <pre> element to the clipboard
        const textToCopy = preElement.textContent;

        navigator.clipboard.writeText(textToCopy).then(() => {
            // Notify the user that the text was copied
            //alert('Copied to clipboard!');
            copyButton.textContent = 'Copied!';
            setTimeout(() => {
                copyButton.textContent = 'Copy to Clipboard';
            }, 5000);
        }).catch(err => {
            console.error('Failed to copy text: ', err);
        });
    });
});
