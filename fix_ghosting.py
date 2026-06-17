with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

# Update Stamp CSS for better entry
old_css = """        #floating-stamp {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s ease-in;
        }
        
        .splash-skip #floating-stamp, #floating-stamp.show {
            opacity: 1 !important;
            pointer-events: auto !important;
        }"""

new_css = """        #floating-stamp {
            opacity: 0;
            pointer-events: none;
            transform: scale(0.6);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .splash-skip #floating-stamp, #floating-stamp.show {
            opacity: 1 !important;
            transform: scale(1) !important;
            pointer-events: auto !important;
        }"""
content = content.replace(old_css, new_css)

# Update JS for smoother animation and less ghosting
old_js = """                        logo.animate([
                            { transform: 'translate(0px, 0px) scale(1)', opacity: 1 },
                            { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0 }
                        ], {
                            duration: 1500,
                            easing: 'cubic-bezier(0.25, 0.1, 0.25, 1)',
                            fill: 'forwards'
                        });
                        
                        // Show the stamp halfway through the movement for a seamless morph
                        setTimeout(() => {
                            stamp.classList.add('show');
                        }, 750);"""

new_js = """                        logo.animate([
                            { transform: 'translate(0px, 0px) scale(1)', opacity: 1, filter: 'blur(0px)' },
                            { transform: `translate(${translateX}px, ${translateY}px) scale(${scale})`, opacity: 0, filter: 'blur(3px)' }
                        ], {
                            duration: 900,
                            easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
                            fill: 'forwards'
                        });
                        
                        // Wait until the logo is almost at its destination, then pop the stamp in
                        setTimeout(() => {
                            stamp.classList.add('show');
                        }, 700);"""
content = content.replace(old_js, new_js)

# Also update the final setTimeout to be closer
content = content.replace("setTimeout(function () {\n                    splash.style.display = 'none';\n                    document.body.style.overflow = '';\n\n                    // Set visited cookie for 7 days\n                    const d = new Date();\n                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));\n                    document.cookie = \"visited=true; expires=\" + d.toUTCString() + \"; path=/\";\n                }, 3300);", "setTimeout(function () {\n                    splash.style.display = 'none';\n                    document.body.style.overflow = '';\n\n                    // Set visited cookie for 7 days\n                    const d = new Date();\n                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));\n                    document.cookie = \"visited=true; expires=\" + d.toUTCString() + \"; path=/\";\n                }, 2600);")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated animation to fix ghosting")
