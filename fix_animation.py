import re

with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the flawed setTimeout(1600) block
old_js = """                // Start retracting the logo to the stamp after 1.6s
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
                }, 1600);"""

new_js = """                // Start retracting the logo to the stamp after 1.6s
                setTimeout(function () {
                    splash.classList.add('fade-out');
                    
                    if (stamp) {
                        // Use getBoundingClientRect to find exact center coordinates
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
                        
                        // Use Web Animations API for smooth, non-teleporting transition
                        // This overrides the CSS animations cleanly
                        logo.animate([
                            { transform: 'translate(0px, 0px) scale(1)', opacity: 1 },
                            { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0 }
                        ], {
                            duration: 700,
                            easing: 'cubic-bezier(0.25, 1, 0.5, 1)',
                            fill: 'forwards'
                        });
                        
                        // Show the stamp halfway through the movement for a seamless morph
                        setTimeout(() => {
                            stamp.classList.add('show');
                        }, 300);
                    }
                }, 1600);"""

content = content.replace(old_js, new_js)

# We can also clean up the CSS that is no longer needed:
content = content.replace("""        #splash-logo.retract {
            animation: none !important;
            transition: all 0.7s cubic-bezier(0.25, 1, 0.5, 1) !important;
            opacity: 0;
        }""", "")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated JS animation")
