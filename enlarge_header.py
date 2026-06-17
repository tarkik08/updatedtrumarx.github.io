import os
import re

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

count = 0
for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Increase header height
    new_content = content.replace('h-[44px]', 'h-[56px]')
    
    # Increase text size of menu
    new_content = new_content.replace('text-[12px] font-normal tracking-tight text-[#f5f5f7]', 'text-[14px] font-normal tracking-tight text-[#f5f5f7]')
    
    # Increase text size and padding of CTA button
    new_content = new_content.replace('px-3 py-1 rounded-full bg-[#f5f5f7] text-black font-medium text-[11px]', 'px-4 py-1.5 rounded-full bg-[#f5f5f7] text-black font-medium text-[13px]')
    
    if new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        count += 1
        print(f"Updated {f}")

print(f"Total files updated: {count}")
