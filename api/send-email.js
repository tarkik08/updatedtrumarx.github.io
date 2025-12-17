// api/send-email.js
const fetch = require('node-fetch');

module.exports = async (req, res) => {
  // Set CORS headers
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
  res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

  // Handle preflight request
  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ success: false, error: 'Method not allowed' });
  }

  try {
    const { name, email, phone, subject, message } = req.body;

    // Validate required fields
    if (!name || !email || !message) {
      return res.status(400).json({ 
        success: false, 
        error: 'Name, email, and message are required' 
      });
    }

    // Send email using EmailJS
    const response = await fetch('https://api.emailjs.com/api/v1.0/email/send', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        service_id: process.env.EMAILJS_SERVICE_ID,
        template_id: process.env.EMAILJS_TEMPLATE_ID,
        user_id: process.env.EMAILJS_PUBLIC_KEY,
        template_params: {
          name,
          email,
          phone: phone || 'Not provided',
          subject: subject || 'New Contact Form Submission',
          message
        }
      })
    });

    if (!response.ok) {
      const errorData = await response.text();
      console.error('EmailJS API error:', response.status, errorData);
      throw new Error(`Failed to send email: ${response.status} ${response.statusText}`);
    }

    const result = await response.json();
    console.log('Email sent successfully:', result);
    
    return res.status(200).json({ 
      success: true, 
      message: 'Email sent successfully',
      data: result
    });

  } catch (error) {
    console.error('Error in send-email API:', error);
    return res.status(500).json({ 
      success: false, 
      error: 'Failed to send email. Please try again later.',
      details: process.env.NODE_ENV === 'development' ? error.message : undefined
    });
  }
};
