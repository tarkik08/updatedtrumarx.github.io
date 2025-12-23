const fs = require('fs');
const path = require('path');

// Get all files in the current directory
const files = fs.readdirSync(__dirname);

// Filter for HTML files
const htmlFiles = files.filter(file => path.extname(file) === '.html');

// Process each HTML file
htmlFiles.forEach(file => {
    const filePath = path.join(__dirname, file);
    let htmlContent = fs.readFileSync(filePath, 'utf8');

    // Validation: Check if Env Vars exist
    if (!process.env.EMAILJS_PUBLIC_KEY) console.warn(`WARNING: EMAILJS_PUBLIC_KEY is not set for ${file}`);
    if (!process.env.EMAILJS_SERVICE_ID) console.warn(`WARNING: EMAILJS_SERVICE_ID is not set for ${file}`);
    if (!process.env.EMAILJS_TEMPLATE_ID) console.warn(`WARNING: EMAILJS_TEMPLATE_ID is not set for ${file}`);

    // Replace placeholders with environment variables
    // Using default values like 'MISSING_...' helps debug if they appear in the final HTML
    htmlContent = htmlContent.replace(/\{\{EMAILJS_PUBLIC_KEY\}\}/g, process.env.EMAILJS_PUBLIC_KEY || 'MISSING_PUBLIC_KEY');
    htmlContent = htmlContent.replace(/\{\{EMAILJS_SERVICE_ID\}\}/g, process.env.EMAILJS_SERVICE_ID || 'MISSING_SERVICE_ID');
    htmlContent = htmlContent.replace(/\{\{EMAILJS_TEMPLATE_ID\}\}/g, process.env.EMAILJS_TEMPLATE_ID || 'MISSING_TEMPLATE_ID');

    fs.writeFileSync(filePath, htmlContent);
    console.log(`Processed: ${file} (Environment variables injected)`);
});

console.log('Build complete: EmailJS credentials processed for all HTML files.');