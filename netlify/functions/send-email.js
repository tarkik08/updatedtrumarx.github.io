const { EMAILJS_PUBLIC_KEY, EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID } = process.env;
const EmailJS = require('@emailjs/nodejs').default;  // Note the .default

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

        // Send email using EmailJS with the public key
        const response = await EmailJS.send(
            EMAILJS_SERVICE_ID,
            EMAILJS_TEMPLATE_ID,
            data,
            EMAILJS_PUBLIC_KEY  // Pass the public key directly to send
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