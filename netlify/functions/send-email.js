const { EMAILJS_PUBLIC_KEY, EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID } = process.env;
const emailjs = require('@emailjs/nodejs');

// Debug log environment variables (won't be visible in production, only in logs)
console.log('Environment variables:', {
    hasPublicKey: !!EMAILJS_PUBLIC_KEY,
    hasServiceId: !!EMAILJS_SERVICE_ID,
    hasTemplateId: !!EMAILJS_TEMPLATE_ID,
    publicKeyStartsWith: EMAILJS_PUBLIC_KEY ? `${EMAILJS_PUBLIC_KEY.substring(0, 3)}...` : 'undefined'
});

exports.handler = async (event) => {
    // Only allow POST requests
    if (event.httpMethod !== 'POST') {
        return {
            statusCode: 405,
            body: JSON.stringify({ error: 'Method Not Allowed' })
        };
    }

    try {
        if (!EMAILJS_PUBLIC_KEY || !EMAILJS_SERVICE_ID || !EMAILJS_TEMPLATE_ID) {
            throw new Error(`Missing required environment variables. Check Netlify environment settings.`);
        }

        const data = JSON.parse(event.body);
        console.log('Sending email with data:', { 
            serviceId: EMAILJS_SERVICE_ID,
            templateId: EMAILJS_TEMPLATE_ID,
            data: { ...data, message: data.message ? '[MESSAGE_CONTENT]' : 'empty' }
        });
        
        // Initialize EmailJS with your public key
        emailjs.init(EMAILJS_PUBLIC_KEY);
        
        // Send email using EmailJS
        const response = await emailjs.send(
            EMAILJS_SERVICE_ID,
            EMAILJS_TEMPLATE_ID,
            data
        );

        console.log('Email sent successfully:', { response });
        return {
            statusCode: 200,
            body: JSON.stringify({ 
                success: true, 
                message: 'Email sent successfully',
                data: response 
            })
        };
    } catch (error) {
        console.error('Error sending email:', {
            message: error.message,
            stack: error.stack,
            envVars: {
                hasPublicKey: !!EMAILJS_PUBLIC_KEY,
                publicKeyStartsWith: EMAILJS_PUBLIC_KEY ? `${EMAILJS_PUBLIC_KEY.substring(0, 3)}...` : 'undefined',
                hasServiceId: !!EMAILJS_SERVICE_ID,
                hasTemplateId: !!EMAILJS_TEMPLATE_ID
            }
        });
        
        return {
            statusCode: 500,
            body: JSON.stringify({ 
                success: false, 
                error: error.message || 'Failed to send email',
                details: process.env.NODE_ENV === 'development' ? error.stack : undefined
            })
        };
    }
};