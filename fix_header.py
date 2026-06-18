import glob

files = [
    'industrialdesign.html', 'gallery.html', 'copyright.html', 'careers.html',
    'blog.html', 'achievements.html', 'aboutus.html', 'patent.html',
    'social.html', 'testimonials.html', 'services.html', 'trademark.html',
    'blog-post.html'
]

for f in files:
    try:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
        
        # We only want to replace the pt-12 on the very first section
        # We can just replace the first occurrence of '<section class="relative pt-12' with '<section class="relative pt-24'
        if '<section class="relative pt-12' in content:
            content = content.replace('<section class="relative pt-12', '<section class="relative pt-24', 1)
            with open(f, 'w', encoding='utf-8') as file:
                file.write(content)
            print(f'Updated {f}')
    except Exception as e:
        print(f'Error with {f}: {e}')
