// functions/send-email.js
export async function onRequestPost(context) {
    try {
        const { request } = context;
        const data = await request.json();
        
        // Log the incoming request data (without sensitive info)
        console.log('Sending email with data:', {
            ...data,
            message: data.message ? '[MESSAGE_CONTENT]' : 'empty'
        });

        // Send email using EmailJS
        const response = await fetch('https://api.emailjs.com/api/v1.0/email/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                service_id: context.env.EMAILJS_SERVICE_ID,
                template_id: context.env.EMAILJS_TEMPLATE_ID,
                user_id: context.env.EMAILJS_PUBLIC_KEY,
                template_params: data
            })
        });

        if (!response.ok) {
            const errorData = await response.text();
            console.error('EmailJS API error:', response.status, errorData);
            throw new Error(`Failed to send email: ${response.status} ${response.statusText}`);
        }

        const result = await response.json();
        console.log('Email sent successfully:', result);
        
        return new Response(JSON.stringify({ 
            success: true, 
            message: 'Email sent successfully',
            data: result
        }), {
            headers: { 'Content-Type': 'application/json' }
        });

    } catch (error) {
        console.error('Error in send-email function:', error);
        return new Response(JSON.stringify({ 
            success: false, 
            error: 'Failed to send email. Please try again later.',
            details: context.env.NODE_ENV === 'development' ? error.message : undefined
        }), { 
            status: 500,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}
