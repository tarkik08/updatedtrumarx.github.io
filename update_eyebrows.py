import os
import re

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

def replace_class_string(match):
    original_class = match.group(1)
    new_class = original_class
    
    # Check if this class string matches the criteria: uppercase + tracking + accent text
    if 'uppercase' in original_class and 'tracking-' in original_class and 'brand-accent' in original_class:
        # Replace colors
        new_class = new_class.replace('text-brand-accent', 'text-white')
        new_class = new_class.replace('dark:text-white', '') # in case we create duplicates
        new_class = new_class.replace('dark:text-brand-accent', 'text-white')
        new_class = new_class.replace('text-gray-500 text-white', 'text-white') # cleanup
        new_class = new_class.replace('text-gray-500 ', '') # cleanup
        
        # Make a bit bigger
        if 'text-[10px]' in new_class:
            new_class = new_class.replace('text-[10px]', 'text-xs md:text-sm')
        elif 'text-xs' in new_class:
            new_class = new_class.replace('text-xs', 'text-sm md:text-base')
        elif 'text-sm md:text-base' in new_class:
            new_class = new_class.replace('text-sm md:text-base', 'text-base md:text-lg')
            
        # Deduplicate spaces
        new_class = ' '.join(new_class.split())
        
    return f'class="{new_class}"'

for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
        
    # Find all class="..." strings
    new_content = re.sub(r'class="([^"]*)"', replace_class_string, content)
    
    if new_content != content:
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print(f"Updated {f}")

print("Done updating formatting.")
