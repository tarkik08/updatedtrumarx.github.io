import os
import re

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

new_nav_block = """            <div class="flex justify-center items-center relative h-10 w-full">
                <!-- Desktop Menu (Centered) -->
                <div class="hidden md:flex items-center gap-4 text-[12px] font-bold tracking-wide">
                    <a href="index.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Home</a>
                    <a href="aboutus.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">About Us</a>
                    <a href="achievements.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Achievements</a>
                    <a href="testimonials.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Testimonials</a>
                    <a href="gallery.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Gallery</a>
                    <a href="services.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Services</a>
                    <a href="careers.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Careers</a>
                    <a href="blog.html" class="text-black transition-all px-2 py-2 hover:text-gray-600">Blog</a>
                    <a href="#" onclick="openContactModal(event)" class="text-black transition-all px-2 py-2 hover:text-gray-600">Contact</a>
                </div>

                <!-- CTA Button & Mobile Toggle (Right) -->
                <div class="absolute right-0 flex items-center gap-4">
                    <!-- Desktop CTA -->
                    <button onclick="openConsultationModal()" class="hidden md:block px-4 py-1.5 rounded-full text-white font-bold text-[12px] transition-all shadow-sm bg-black hover:bg-gray-800">
                        Consultation
                    </button>
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-black text-xl">
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
        
    # Replace from `<div class="flex justify-between items-center">` to just before `<!-- Mobile Menu -->`
    pattern = re.compile(r'<div class="flex justify-between items-center">.*?</div>\s*</div>\s*(?=<!-- Mobile Menu -->)', re.DOTALL)
    
    new_content, num_subs = pattern.subn(new_nav_block, content)
    
    if num_subs > 0 and new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        count += 1
        print(f"Updated {f}")

print(f"Total files updated: {count}")
