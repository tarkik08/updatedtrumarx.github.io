import os
import re

directory = r"d:\updatedtrumarx.github.io"
html_files = [f for f in os.listdir(directory) if f.endswith(".html")]

count = 0
for file in html_files:
    filepath = os.path.join(directory, file)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Search for h1 tags
    def replace_h1_classes(match):
        h1_content = match.group(0)
        # Replace text-brand-textDark dark:text-brand-textLight with text-white
        new_h1_content = re.sub(r'text-brand-textDark\s+dark:text-brand-textLight', r'text-white', h1_content)
        return new_h1_content

    new_content = re.sub(r'<h1[^>]*>[\s\S]*?</h1>', replace_h1_classes, content)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        count += 1
        print(f"Updated {file}")

print(f"Total files updated: {count}")
