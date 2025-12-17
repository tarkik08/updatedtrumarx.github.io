const { EMAILJS_USER_ID, EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID } = process.env;
const emailjs = require('@emailjs/nodejs');

exports.handler = async (event) => {
    // Only allow POST requests
    if (event.httpMethod !== 'POST') {
        return {
            statusCode: 405,
            body: JSON.stringify({ error: 'Method Not Allowed' })
        };
    }

    try {
        const data = JSON.parse(event.body);
        
        // Initialize EmailJS with your user ID
        emailjs.init(EMAILJS_USER_ID);
        
        // Send email using EmailJS
        const response = await emailjs.send(
            EMAILJS_SERVICE_ID,
            EMAILJS_TEMPLATE_ID,
            data
        );

        return {
            statusCode: 200,
            body: JSON.stringify({ 
                success: true, 
                message: 'Email sent successfully',
                data: response 
            })
        };
    } catch (error) {
        console.error('Error sending email:', error);
        return {
            statusCode: 500,
            body: JSON.stringify({ 
                success: false, 
                error: error.message || 'Failed to send email' 
            })
        };
    }
};
