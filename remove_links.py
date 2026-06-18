import glob
import re

files = glob.glob('*.html')

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    # Desktop nav links
    content = content.replace('<a href="gallery.html" class="transition-opacity opacity-80 hover:opacity-100">Gallery</a>', '')
    content = content.replace('<a href="blog.html" class="transition-opacity opacity-80 hover:opacity-100">Blog</a>', '')
    
    # Mobile nav links
    content = content.replace('<a href="gallery.html" class="text-black">Gallery</a>', '')
    content = content.replace('<a href="blog.html" class="text-black">Blog</a>', '')
    
    with open(f, 'w', encoding='utf-8') as file:
        file.write(content)
        
print('Removed blog and gallery from all navigation menus.')
