with open('index.html', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("duration: 700", "duration: 1500")
content = content.replace("'cubic-bezier(0.25, 1, 0.5, 1)'", "'cubic-bezier(0.25, 0.1, 0.25, 1)'") # ease-in-out curve
content = content.replace("setTimeout(() => {\n                            stamp.classList.add('show');\n                        }, 300);", "setTimeout(() => {\n                            stamp.classList.add('show');\n                        }, 750);")
content = content.replace("setTimeout(function () {\n                    splash.style.display = 'none';\n                    document.body.style.overflow = '';\n\n                    // Set visited cookie for 7 days\n                    const d = new Date();\n                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));\n                    document.cookie = \"visited=true; expires=\" + d.toUTCString() + \"; path=/\";\n                }, 2300);", "setTimeout(function () {\n                    splash.style.display = 'none';\n                    document.body.style.overflow = '';\n\n                    // Set visited cookie for 7 days\n                    const d = new Date();\n                    d.setTime(d.getTime() + (7 * 24 * 60 * 60 * 1000));\n                    document.cookie = \"visited=true; expires=\" + d.toUTCString() + \"; path=/\";\n                }, 3300);")

with open('index.html', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated animation values")
