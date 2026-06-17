import os
import re

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

count = 0
for file in html_files:
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()

    # Change sticky to fixed w-full
    content = content.replace('class="sticky top-0 z-50 legal-nav transition-colors duration-300"',
                              'class="fixed top-0 w-full z-[100] legal-nav transition-colors duration-300"')
    
    # Also just in case it doesn't match exactly, do regex
    content = re.sub(r'class="sticky top-0 z-50([^"]*)"', r'class="fixed top-0 w-full z-[100]\1"', content)

    # Change py-1.5 to py-1
    content = content.replace('max-w-7xl mx-auto px-6 py-1.5', 'max-w-7xl mx-auto px-6 py-0')
    
    # Change logo height h-10 to h-6
    content = content.replace('class="h-10 w-auto"', 'class="h-6 w-auto"')
    content = content.replace('class="h-12 w-auto"', 'class="h-6 w-auto"') # in case some files have h-12

    # Change desktop menu size
    content = content.replace('gap-3 text-[14px] font-medium tracking-normal', 'gap-4 text-[12px] font-medium tracking-wide')
    
    # Change mobile menu button padding
    content = content.replace('px-5 py-2.5 rounded-lg text-white font-medium text-[14px]', 'px-4 py-1.5 rounded-full text-white font-medium text-[12px]')

    if content != open(file, 'r', encoding='utf-8').read():
        with open(file, 'w', encoding='utf-8') as f:
            f.write(content)
        count += 1
        print(f"Updated {file}")

print(f"Total files updated: {count}")
