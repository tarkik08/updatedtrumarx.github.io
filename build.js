const fs = require('fs');
const path = require('path');

const htmlFile = path.join(__dirname, 'index.html');
let htmlContent = fs.readFileSync(htmlFile, 'utf8');

htmlContent = htmlContent.replace(/\{\{EMAILJS_PUBLIC_KEY\}\}/g, process.env.EMAILJS_PUBLIC_KEY || '');
htmlContent = htmlContent.replace(/\{\{EMAILJS_SERVICE_ID\}\}/g, process.env.EMAILJS_SERVICE_ID || '');
htmlContent = htmlContent.replace(/\{\{EMAILJS_TEMPLATE_ID\}\}/g, process.env.EMAILJS_TEMPLATE_ID || '');

fs.writeFileSync(htmlFile, htmlContent);
console.log('Build complete: EmailJS credentials injected.');