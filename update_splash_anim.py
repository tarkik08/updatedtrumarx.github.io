import re

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add ID to stamp
content = content.replace('<!-- Floating Trumarx Stamp -->\n    <a href="index.html"', '<!-- Floating Trumarx Stamp -->\n    <a id="floating-stamp" href="index.html"')

# 2. Add CSS
css_additions = """
        #splash-screen.fade-out {
            background-color: transparent !important;
            pointer-events: none;
        }

        #splash-logo.retract {
            animation: none !important;
            transition: all 0.7s cubic-bezier(0.25, 1, 0.5, 1) !important;
            opacity: 0;
        }
        
        #floating-stamp {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease-in;
        }
        
        .splash-skip #floating-stamp, #floating-stamp.show {
            opacity: 1 !important;
            pointer-events: auto !important;
        }
"""
content = content.replace('#splash-screen.fade-out {\n            opacity: 0;\n            pointer-events: none;\n        }', css_additions)

# 3. Update JS
js_old = """        (function () {
            // Check if the splash screen needs to be skipped
            if (document.documentElement.classList.contains('splash-skip')) {
                const splash = document.getElementById('splash-screen');
                if (splash) splash.style.display = 'none';
                return;
            }

            const splash = document.getElementById('splash-screen');
            const logo = document.getElementById('splash-logo');

            if (splash && logo) {
                // Disable scrolling on body during animation
                document.body.style.overflow = 'hidden';

                // Trigger the logo animation slightly after loading
                setTimeout(function () {
                    logo.classList.add('animate-drop');
                }, 100);

                // Start fading out the splash screen after 1.6s
                setTimeout(function () {
                    splash.classList.add('fade-out');
                }, 1600);

                // Fully hide splash screen and enable scrolling after 2.3s
                setTimeout(function () {
                    splash.style.display = 'none';
                    document.body.style.overflow = '';

                    // Set visited cookie for 7 days
                    const d = new Date();
                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
                    document.cookie = "visited=true; expires=" + d.toUTCString() + "; path=/";
                }, 2300);"""

js_new = """        (function () {
            // Check if the splash screen needs to be skipped
            if (document.documentElement.classList.contains('splash-skip')) {
                const splash = document.getElementById('splash-screen');
                if (splash) splash.style.display = 'none';
                return;
            }

            const splash = document.getElementById('splash-screen');
            const logo = document.getElementById('splash-logo');
            const stamp = document.getElementById('floating-stamp');

            if (splash && logo) {
                // Disable scrolling on body during animation
                document.body.style.overflow = 'hidden';

                // Trigger the logo animation slightly after loading
                setTimeout(function () {
                    logo.classList.add('animate-drop');
                }, 100);

                // Start retracting the logo to the stamp after 1.6s
                setTimeout(function () {
                    splash.classList.add('fade-out');
                    
                    if (stamp) {
                        // Force layout
                        const logoRect = logo.getBoundingClientRect();
                        const stampRect = stamp.getBoundingClientRect();
                        
                        const windowWidth = window.innerWidth;
                        const windowHeight = window.innerHeight;
                        const logoCenterX = windowWidth / 2;
                        const logoCenterY = windowHeight / 2;
                        
                        const stampCenterX = stampRect.left + stampRect.width / 2;
                        const stampCenterY = stampRect.top + stampRect.height / 2;
                        
                        const translateX = stampCenterX - logoCenterX;
                        const translateY = stampCenterY - logoCenterY;
                        const scale = stampRect.width / logoRect.width;
                        
                        logo.classList.add('retract');
                        // Override CSS transform
                        logo.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
                        
                        setTimeout(() => {
                            stamp.classList.add('show');
                        }, 500);
                    }
                }, 1600);

                // Fully hide splash screen and enable scrolling after 2.3s
                setTimeout(function () {
                    splash.style.display = 'none';
                    document.body.style.overflow = '';

                    // Set visited cookie for 7 days
                    const d = new Date();
                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));
                    document.cookie = "visited=true; expires=" + d.toUTCString() + "; path=/";
                }, 2300);"""

content = content.replace(js_old, js_new)

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated index.html animation")
