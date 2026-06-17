import os

html_files = [f for f in os.listdir('.') if f.endswith('.html')]

old_stamp = """    <!-- Floating Trumarx Stamp -->
    <a href="index.html" 
       class="fixed top-4 left-4 z-[999] w-20 h-20 rounded-full border border-dashed border-gray-500 bg-[#161617]/80 backdrop-blur-md shadow-2xl flex flex-col items-center justify-center hover:scale-110 hover:border-brand-accent hover:bg-[#161617] transition-all duration-300 group">
        <img src="Trumarx_Logo-removebg-preview.png" alt="Trumarx" class="h-6 w-auto mb-1 opacity-90 group-hover:opacity-100 transition-opacity">
        <span class="text-[9px] uppercase tracking-widest text-gray-400 font-bold group-hover:text-white transition-colors">ESTD 2014</span>
    </a>"""

new_stamp = """    <!-- Floating Trumarx Stamp -->
    <a href="index.html" 
       class="fixed top-24 left-4 md:left-8 z-[99] w-20 h-20 rounded-full bg-white shadow-2xl flex flex-col items-center justify-center hover:scale-110 transition-all duration-300 group">
        <img src="Trumarx_Logo-removebg-preview.png" alt="Trumarx" class="h-6 w-auto mb-1 opacity-90 group-hover:opacity-100 transition-opacity">
        <span class="text-[9px] uppercase tracking-widest text-black font-bold transition-colors">ESTD 2014</span>
    </a>"""

count = 0
for f in html_files:
    with open(f, 'r', encoding='utf-8') as file:
        content = file.read()
        
    if "<!-- Floating Trumarx Stamp -->" in content:
        # Regex replacement to catch minor differences
        import re
        pattern = re.compile(r'<!-- Floating Trumarx Stamp -->.*?</a>', re.DOTALL)
        new_content, num_subs = pattern.subn(new_stamp, content)
        
        if num_subs > 0 and new_content != content:
            with open(f, 'w', encoding='utf-8') as file:
                file.write(new_content)
            count += 1
            print(f"Updated {f}")

print(f"Total files updated: {count}")
