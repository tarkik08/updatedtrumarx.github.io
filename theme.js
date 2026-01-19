tailwind.config = {
    darkMode: 'class', // Keeping this but we won't toggle it.
    theme: {
        extend: {
            colors: {
                brand: {
                    // Core Brand Colors - Apple Monochrome (Premium Trust)
                    navy: '#1d1d1f',        /* Apple Signature Dark: Primary */
                    gold: '#6e6e6e',        /* Sophisticated Grey: Accent */
                    charcoal: '#1d1d1f',    /* Near Black: Primary Text */
                    white: '#f5f5f7',       /* Apple White: Background */

                    // Supporting Colors
                    navyDeep: '#000000',    /* Pure Black */
                    blue: '#9e9e9e',        /* Light Grey */
                    goldWarm: '#9e9e9e',    /* Light Grey */
                    goldAntique: '#4a4a4a', /* Dark Grey */
                    slate: '#6e6e6e',       /* Medium Grey */
                    grayLight: '#f5f5f7',   /* Apple Light Grey */
                    grayMedium: '#86868b',  /* Apple Medium Grey */
                    charcoalDeep: '#000000',/* Pure Black */

                    // Functional Semantics
                    bg: '#000000',          /* Main Background: Black */
                    card: '#1d1d1f',        /* Card Background: Apple Dark */
                    text: '#f5f5f7',        /* Primary Text: Apple White */
                    textSecondary: '#86868b', /* Secondary Text: Apple Grey */
                    border: '#424245',      /* Borders: Apple Grey Border */

                    // Backward Compatibility
                    light: '#f5f5f7',       /* Maps to Apple Light */
                    dark: '#000000',        /* Maps to Black */

                    darkCard: '#1d1d1f',    /* Maps to Apple Dark */
                    darkCardHover: '#2c2c2e',

                    textDark: '#f5f5f7',    /* Force text to Apple White */
                    textLight: '#f5f5f7',   /* Force text to Apple White */

                    accent: '#6e6e6e',      /* Grey Accent */
                    accentHover: '#4a4a4a', /* Dark Grey Hover */

                    logoGrey: '#6e6e6e',
                    logoBlack: '#1d1d1f',
                },
                functional: {
                    success: '#86868b',     /* Grey Success */
                    warning: '#6e6e6e',     /* Grey Warning */
                    error: '#4a4a4a',       /* Dark Grey Error */
                    info: '#6e6e6e',        /* Grey Info */
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
