tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                brand: {
                    // Core Brand Colors
                    navy: '#2b5aa6',        /* Royal Blue: Primary Brand Color */
                    gold: '#c9a961',        /* Brushed Gold: Elegant Accent */
                    charcoal: '#2e3338',    /* Charcoal: Sophisticated Neutral */
                    white: '#fafbfc',       /* Soft White */

                    // Supporting Colors
                    blueLight: '#4a7bc8',   /* Light Royal: Accents/Link Hover (Dark Mode) */
                    blueDeep: '#1a3d6b',    /* Deep Navy: Hover States (Light Mode) */
                    goldWarm: '#d4b579',    /* Warm Gold: Highlights */
                    slate: '#505761',       /* Slate Gray: Secondary Text */

                    // Light Mode Semantics
                    light: '#fafbfc',       /* Background (Soft White) */
                    textDark: '#2e3338',    /* Primary Text (Charcoal) */
                    textDarkSecondary: '#505761', /* Secondary Text (Slate Gray) */

                    // Dark Mode Semantics
                    dark: '#1a1d21',        /* Background (Very Dark Charcoal) */
                    darkCard: '#2e3338',    /* Card/Section BG (Charcoal) */
                    darkCardHover: '#363b41', /* Slightly lighter Charcoal for hover */
                    textLight: '#e8eaed',   /* Primary Text (Soft White) */
                    textLightSecondary: '#a8aeb5', /* Secondary Text (Light Gray) */
                    borderDark: '#404449',  /* Borders (Medium Gray) */

                    // Mappings for Backward Compatibility
                    accent: '#c9a961',      /* Mapped to Brushed Gold */
                    accentHover: '#b8956a', /* Mapped to Bronze/Darker Gold */
                    logoGrey: '#505761',    /* Mapped to Slate Gray */
                    logoBlack: '#2b5aa6',   /* Mapped to Royal Blue for Logo */
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
                'legal': '0 4px 20px -2px rgba(43, 90, 166, 0.1)', /* Shadow with Royal Blue tint */
                'gold': '0 4px 20px -2px rgba(201, 169, 97, 0.2)', /* Shadow with Gold tint */
            }
        }
    }
}
