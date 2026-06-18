import glob

with open('index.html', 'r', encoding='utf-8') as f:
    index_content = f.read()

nav_start = index_content.find('<nav class="fixed top-0 w-full z-[100]')
if nav_start == -1:
    print('Could not find nav in index.html')
    exit(1)

nav_end_marker = '</nav>'
nav_end = index_content.find(nav_end_marker, nav_start) + len(nav_end_marker)
nav_block = index_content[nav_start:nav_end]

print('Found nav block of length:', len(nav_block))

files = glob.glob('*.html')
files.remove('index.html')

for f in files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
    
    f_nav_start = content.find('<nav')
    if f_nav_start != -1:
        f_nav_end = content.find('</nav>', f_nav_start) + len('</nav>')
        new_content = content[:f_nav_start] + nav_block + content[f_nav_end:]
        with open(f, 'w', encoding='utf-8') as file:
            file.write(new_content)
        print('Updated nav in', f)
    else:
        print('No nav found in', f)
