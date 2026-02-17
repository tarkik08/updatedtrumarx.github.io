import os
import re

files_to_update = [
    'index.html',
    'aboutus.html',
    'achievements.html',
    'testimonials.html',
    'gallery.html',
    'services.html',
    'careers.html',
    'blog.html',
    'blog-post.html',
    'copyright.html',
    'industrialdesign.html',
    'patent.html',
    'trademark.html'
]

base_dir = r'd:\updatedtrumarx.github.io'

# Template for the button
# We use a placeholder for the bottom class
button_template = '''    <!-- Floating WhatsApp Button -->
    <a id="whatsapp-float" href="https://api.whatsapp.com/send/?phone=9606010198&text&type=phone_number&app_absent=0"
        target="_blank" rel="noopener noreferrer"
        class="fixed {bottom_class} right-6 z-50 flex items-center justify-center w-14 h-14 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 group"
        aria-label="Contact us on WhatsApp">
        <i class="fab fa-whatsapp text-3xl"></i>
    </a>'''

# Template for the script (minimized indentation for safety, but formatted)
script_template = '''    <!-- Script to hide WhatsApp button when footer is visible -->
    <script>
        (function () {
            const whatsappBtn = document.getElementById('whatsapp-float');
            const footer = document.querySelector('footer');
            if (whatsappBtn && footer) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            whatsappBtn.style.opacity = '0';
                            whatsappBtn.style.pointerEvents = 'none';
                        } else {
                            whatsappBtn.style.opacity = '1';
                            whatsappBtn.style.pointerEvents = 'auto';
                        }
                    });
                }, { threshold: 0.1 });
                observer.observe(footer);
            }
        })();
    </script>'''

def update_file(filename):
    file_path = os.path.join(base_dir, filename)
    if not os.path.exists(file_path):
        print(f"File not found: {filename}")
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Determine bottom class
    # index.html usually has bottom-20 because of the scroll pill or just design preference
    if filename == 'index.html':
        bottom_class = 'bottom-20'
    else:
        bottom_class = 'bottom-6'

    new_button_html = button_template.format(bottom_class=bottom_class)

    # 1. Check if button exists
    # Regex to capture the existing button block (loosely)
    # matching <a id="whatsapp-float" ... </a>
    btn_pattern = re.compile(
        r'<!-- Floating WhatsApp Button -->\s*<a id="whatsapp-float".*?</a>',
        re.DOTALL
    )
    
    # Fallback pattern if comment is missing
    btn_pattern_fallback = re.compile(
        r'<a id="whatsapp-float".*?</a>',
        re.DOTALL
    )

    if btn_pattern.search(content):
        # Replace existing block
        content = btn_pattern.sub(new_button_html, content)
        print(f"Updated button in {filename}")
    elif btn_pattern_fallback.search(content):
         content = btn_pattern_fallback.sub(new_button_html, content)
         print(f"Updated button (fallback regex) in {filename}")
    else:
        # Insert before </body>
        # We also want to check for the script. If we insert the button, we should insert the script if it's missing.
        # But for simplicity, let's insert both if the button is missing.
        
        # Check if script exists using a unique snippet from it
        script_pattern = re.compile(r'const whatsappBtn = document.getElementById\(\'whatsapp-float\'\);')
        
        insertion_block = f"\n{new_button_html}\n"
        
        if not script_pattern.search(content):
            insertion_block += f"\n{script_template}\n"
        
        if '</body>' in content:
            content = content.replace('</body>', f'{insertion_block}</body>')
            print(f"Inserted button into {filename}")
        else:
            print(f"Could not find </body> in {filename}, skipping insertion.")
            return

    # Cleanup: If we updated the button, we should also ensure the script is there/updated?
    # Existing pages seem to have the script separate. The regex replace only touched the button.
    # If the script is already there, we leave it alone (it works generically based on ID).
    
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)

for f in files_to_update:
    try:
        update_file(f)
    except Exception as e:
        print(f"Error processing {f}: {e}")
