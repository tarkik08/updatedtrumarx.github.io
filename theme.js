tailwind.config = {
    darkMode: 'class', // Keeping this but we won't toggle it.
    theme: {
        extend: {
            colors: {
                brand: {
                    // Core Brand Colors
                    navy: '#1a3d7a',        /* Dark Royal Blue: Primary */
                    gold: '#c9a961',        /* Brushed Gold: Accent */
                    charcoal: '#2e3338',    /* Charcoal: Primary Text */
                    white: '#fafbfc',       /* Off White: Background */

                    // Supporting Colors
                    navyDeep: '#0f2847',    /* Deep Navy */
                    blue: '#2b5aa6',        /* Lighter Royal Blue */
                    goldWarm: '#d4b579',    /* Warm Gold */
                    goldAntique: '#b89654', /* Antique Gold */
                    slate: '#6b7280',       /* Slate */
                    grayLight: '#e1e4e8',   /* Light Gray */
                    grayMedium: '#a8aeb5',  /* Medium Gray */
                    charcoalDeep: '#1a1d21',/* Deep Charcoal */

                    // Functional Semantics (Single Theme Mappings)
                    // These map both 'light' and 'dark' variants to the same single-theme look
                    // or to complementary contrasts if maintaining standard light-theme logic.

                    bg: '#fafbfc',          /* Main Background */
                    card: '#ffffff',        /* Card Background */
                    text: '#2e3338',        /* Primary Text */
                    textSecondary: '#505761', /* Secondary Text */
                    border: '#e1e4e8',      /* Borders */

                    // Backward Compatibility / Semantic Overrides
                    // We map these to ensure existing classes look correct in the new Single Theme.

                    light: '#fafbfc',       /* Maps to BG */
                    dark: '#fafbfc',        /* Maps to BG (overriding dark mode bg to be light) */

                    darkCard: '#ffffff',    /* Maps to Card White */
                    darkCardHover: '#f3f4f6',

                    textDark: '#2e3338',    /* Primary Text */
                    textLight: '#2e3338',   /* Force Dark text even if 'text-brand-textLight' is used */

                    accent: '#c9a961',
                    accentHover: '#b89654',

                    logoGrey: '#505761',
                    logoBlack: '#1a3d7a',
                },
                functional: {
                    success: '#2d7a4f',
                    warning: '#d97706',
                    error: '#c53030',
                    info: '#3182ce',
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
