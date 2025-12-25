tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    // Primary Colors
                    navy: '#1a2b4a',        /* Deep Navy Blue: Authority, Trust (Primary) */
                    gold: '#d4a574',        /* Rich Gold: Prestige, Excellence (Secondary) */
                    white: '#ffffff',       /* Crisp White */
                    charcoal: '#1f1f1f',    /* Deep Charcoal: Text/Dark BG */

                    // Secondary/Accents
                    slate: '#4a5f7f',       /* Slate Blue: Softer professional tone */
                    bronze: '#b8956a',      /* Warm Bronze: Hover for Gold */
                    gray: '#6b7280',        /* Cool Gray: Neutral body text */

                    // Light Mode Specifics
                    light: '#f8f9fa',       /* Light Mode Background (Very light gray) */
                    textDark: '#1f1f1f',    /* Light Mode Primary Text (Charcoal) */
                    textDarkSecondary: '#2d3748', /* Light Mode Secondary Text */

                    // Dark Mode Specifics
                    dark: '#0f1419',        /* Dark Mode Background (Very dark blue-gray) */
                    darkCard: '#1a1d23',    /* Dark Mode Secondary BG (Cards) */
                    darkCardHover: '#252a31', /* Dark Mode Card Hover */
                    textLight: '#f8f9fa',   /* Dark Mode Text (Off-white) */
                    navyLight: '#5a7aa6',   /* Lighter Navy for Dark Mode visibility */
                    goldBright: '#e6c08a',  /* Brighter Gold for Dark Mode contrast */
                    borderDark: '#3a3f47',  /* Dark Mode Borders */

                    // Mappings to existing classes for backward compatibility/ease of transition
                    accent: '#d4a574',      /* Mapped to Gold */
                    accentHover: '#b8956a', /* Mapped to Bronze */
                    logoGrey: '#6b7280',    /* Mapped to Cool Gray */
                    logoBlack: '#1a2b4a',   /* Mapped to Navy for Logo */
                },
                functional: {
                    success: '#10b981',
                    warning: '#f59e0b',
                    error: '#ef4444',
                    info: '#3b82f6',
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
                'legal': '0 4px 20px -2px rgba(26, 43, 74, 0.1)', /* Shadow with Navy tint */
                'gold': '0 4px 20px -2px rgba(212, 165, 116, 0.2)', /* Shadow with Gold tint */
            }
        }
    }
}
