const { EMAILJS_PUBLIC_KEY, EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID } = process.env;
const fetch = require('node-fetch');

// Debug log environment variables
console.log('Environment variables:', {
    hasPublicKey: !!EMAILJS_PUBLIC_KEY,
    publicKeyStartsWith: EMAILJS_PUBLIC_KEY ? `${EMAILJS_PUBLIC_KEY.substring(0, 3)}...` : 'undefined',
    hasServiceId: !!EMAILJS_SERVICE_ID,
    hasTemplateId: !!EMAILJS_TEMPLATE_ID
});

exports.handler = async (event) => {
    if (event.httpMethod !== 'POST') {
        return {
            statusCode: 405,
            body: JSON.stringify({ error: 'Method Not Allowed' })
        };
    }

    try {
        const data = JSON.parse(event.body);
        console.log('Sending email with data:', { 
            serviceId: EMAILJS_SERVICE_ID,
            templateId: EMAILJS_TEMPLATE_ID,
            data: { ...data, message: data.message ? '[MESSAGE_CONTENT]' : 'empty' }
        });

        // Use EmailJS REST API directly
        const response = await fetch('https://api.emailjs.com/api/v1.0/email/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                service_id: EMAILJS_SERVICE_ID,
                template_id: EMAILJS_TEMPLATE_ID,
                user_id: EMAILJS_PUBLIC_KEY,  // Using public key as user_id
                template_params: data
            })
        });

        if (!response.ok) {
            const errorData = await response.text();
            throw new Error(`EmailJS API error: ${response.status} - ${errorData}`);
        }

        const result = await response.json();
        console.log('Email sent successfully:', result);
        
        return {
            statusCode: 200,
            body: JSON.stringify({ 
                success: true, 
                message: 'Email sent successfully',
                data: result
            })
        };
    } catch (error) {
        console.error('Error sending email:', {
            message: error.message,
            stack: error.stack,
            errorObject: JSON.stringify(error, Object.getOwnPropertyNames(error))
        });
        
        return {
            statusCode: 500,
            body: JSON.stringify({ 
                success: false, 
                error: 'Failed to send email. Please try again later.',
                details: process.env.NODE_ENV === 'development' ? error.message : undefined
            })
        };
    }
};