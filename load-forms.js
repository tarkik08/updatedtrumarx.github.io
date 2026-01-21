// Load all form components dynamically
(function () {
    // Function to load HTML content and execute scripts
    function loadFormComponent(url, callback) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Extract script tags before appending to body
                const scripts = Array.from(tempDiv.querySelectorAll('script'));
                const scriptContents = scripts.map(script => ({
                    src: script.src,
                    content: script.textContent,
                    attributes: Array.from(script.attributes)
                }));

                // Remove script tags from the HTML
                scripts.forEach(script => script.remove());

                // Append the HTML (without scripts) to body
                document.body.appendChild(tempDiv);

                // Now execute the scripts by creating new script elements
                scriptContents.forEach(scriptData => {
                    const newScript = document.createElement('script');

                    if (scriptData.src) {
                        newScript.src = scriptData.src;
                    } else {
                        newScript.textContent = scriptData.content;
                    }

                    // Copy attributes
                    scriptData.attributes.forEach(attr => {
                        if (attr.name !== 'src') { // src is already set above
                            newScript.setAttribute(attr.name, attr.value);
                        }
                    });

                    // Append to body to execute
                    document.body.appendChild(newScript);
                });

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
