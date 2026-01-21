// Load all form components dynamically
(function () {
    // Function to load HTML content
    function loadFormComponent(url, callback) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                document.body.appendChild(tempDiv);
                if (callback) callback();
            })
            .catch(error => console.error('Error loading form component:', error));
    }

    // Load all forms when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            loadFormComponent('components/consultation-form.html');
            loadFormComponent('components/internship-form.html');
            loadFormComponent('components/job-application-form.html');
        });
    } else {
        loadFormComponent('components/consultation-form.html');
        loadFormComponent('components/internship-form.html');
        loadFormComponent('components/job-application-form.html');
    }
})();
