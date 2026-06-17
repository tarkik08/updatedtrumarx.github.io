import os
import re

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

new_nav_block = """    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-[100] transition-colors duration-300 border-b border-white/10"
        style="background-color: rgba(22, 22, 23, 0.8); backdrop-filter: saturate(180%) blur(20px); -webkit-backdrop-filter: saturate(180%) blur(20px);">

        <div class="max-w-5xl mx-auto px-4">
            <div class="flex justify-between items-center h-[44px] w-full">
                <!-- Desktop Menu (Spread evenly) -->
                <div class="hidden md:flex items-center justify-between w-full text-[12px] font-normal tracking-tight text-[#f5f5f7]">
                    <a href="index.html" class="transition-opacity opacity-80 hover:opacity-100">Home</a>
                    <a href="aboutus.html" class="transition-opacity opacity-80 hover:opacity-100">About Us</a>
                    <a href="achievements.html" class="transition-opacity opacity-80 hover:opacity-100">Achievements</a>
                    <a href="testimonials.html" class="transition-opacity opacity-80 hover:opacity-100">Testimonials</a>
                    <a href="gallery.html" class="transition-opacity opacity-80 hover:opacity-100">Gallery</a>
                    <a href="services.html" class="transition-opacity opacity-80 hover:opacity-100">Services</a>
                    <a href="careers.html" class="transition-opacity opacity-80 hover:opacity-100">Careers</a>
                    <a href="blog.html" class="transition-opacity opacity-80 hover:opacity-100">Blog</a>
                    <a href="#" onclick="openContactModal(event)" class="transition-opacity opacity-80 hover:opacity-100">Contact</a>
                    
                    <!-- CTA Button -->
                    <button onclick="openConsultationModal()" class="px-3 py-1 rounded-full bg-[#f5f5f7] text-black font-medium text-[11px] hover:bg-white transition-colors ml-4">
                        Consultation
                    </button>
                </div>

                <!-- Mobile Menu Toggle -->
                <div class="md:hidden flex justify-end w-full">
                    <button id="mobile-menu-btn" class="text-[#f5f5f7] text-lg opacity-80 hover:opacity-100">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
"""

count = 0
for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Replace from `<nav class="fixed top-0` or `<nav class="sticky` to just before `<!-- Mobile Menu -->`
    pattern = re.compile(r'<!-- Navigation -->\s*<nav[^>]*>.*?</div>\s*</div>\s*</div>\s*(?=<!-- Mobile Menu -->)', re.DOTALL)
    
    new_content, num_subs = pattern.subn(new_nav_block, content)
    
    if num_subs > 0 and new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        count += 1
        print(f"Updated {f}")

print(f"Total files updated: {count}")
