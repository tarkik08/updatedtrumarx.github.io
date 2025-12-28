tailwind.config = {
    darkMode: 'class', // Keeping this but we won't toggle it.
    theme: {
        extend: {
            colors: {
                brand: {
                    // Core Brand Colors - Midnight & Azure (Premium Trust)
                    navy: '#0a1628',        /* Midnight Blue: Primary */
                    gold: '#3b82f6',        /* Azure Blue: Accent */
                    charcoal: '#1e3a5f',    /* Deep Navy: Primary Text */
                    white: '#f1f5f9',       /* Cool White: Background */

                    // Supporting Colors
                    navyDeep: '#050b14',    /* True Black Midnight */
                    blue: '#60a5fa',        /* Light Azure */
                    goldWarm: '#60a5fa',    /* Light Azure */
                    goldAntique: '#2563eb', /* Deep Azure */
                    slate: '#475569',       /* Slate Blue */
                    grayLight: '#e2e8f0',   /* Cool Gray */
                    grayMedium: '#94a3b8',  /* Medium Gray Blue */
                    charcoalDeep: '#020408',/* Deep Midnight */

                    // Functional Semantics
                    bg: '#0a1628',          /* Main Background: Midnight Blue */
                    card: '#1e3a5f',        /* Card Background: Deep Navy */
                    text: '#f1f5f9',        /* Primary Text: Cool White */
                    textSecondary: '#94a3b8', /* Secondary Text: Gray Blue */
                    border: '#2563eb',      /* Borders: Deep Azure */

                    // Backward Compatibility
                    light: '#0a1628',       /* Maps to Midnight BG */
                    dark: '#0a1628',        /* Maps to Midnight BG */

                    darkCard: '#1e3a5f',    /* Maps to Deep Navy */
                    darkCardHover: '#2d4f7c',

                    textDark: '#f1f5f9',    /* Force text to White */
                    textLight: '#f1f5f9',   /* Force text to White */

                    accent: '#3b82f6',      /* Azure Accent */
                    accentHover: '#2563eb', /* Deep Azure Hover */

                    logoGrey: '#475569',
                    logoBlack: '#0a1628',
                },
                functional: {
                    success: '#10b981',     /* Green Success */
                    warning: '#f59e0b',     /* Amber Warning */
                    error: '#ef4444',       /* Red Error */
                    info: '#3b82f6',        /* Azure Info */
                }
            },
            fontFamily: {
                sans: [
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"San Francisco"',
                    '"Helvetica Neue"',
                    'Helvetica',
                    'Arial',
                    'sans-serif'
                ],
                serif: ['"Merriweather"', 'serif'],
            },
            boxShadow: {
                'legal': '0 4px 20px -2px rgba(26, 61, 122, 0.1)', /* Dark Royal Blue Tint */
                'gold': '0 4px 20px -2px rgba(201, 169, 97, 0.2)',
            }
        }
    }
}
