import re

with open('resources/views/superadmin/pengguna/create.blade.php', 'r') as f:
    create_html = f.read()

with open('resources/views/superadmin/pengguna/edit.blade.php', 'r') as f:
    edit_html = f.read()

# Extract Face Scan UI from create
scan_ui_match = re.search(r'<!-- Right: Face Scan -->\s*(<div class="space-y-6">.*?</div>\s*</div>\s*</form>)', create_html, re.DOTALL)
if not scan_ui_match:
    print("Failed to find scan UI in create")
    exit(1)

scan_ui = scan_ui_match.group(1)

# Extract Script from create
script_match = re.search(r'(<script>.*?</script>)', create_html, re.DOTALL)
if not script_match:
    print("Failed to find script in create")
    exit(1)

script_content = script_match.group(1)

# In edit.blade.php, we need to replace the entire Right: Face Scan up to the script
# BUT we need to inject the existing face UI logic into the new scan UI
existing_face_php = """                        @php
                            $activeFace = $user->employee ? $user->employee->activeFace : null;
                        @endphp"""

existing_face_ui = """
                            @if($activeFace)
                            <!-- Existing Face -->
                            <div id="existing-face-ui" class="h-full flex flex-col items-center justify-center relative">
                                <img src="{{ asset('storage/' . $activeFace->image_path) }}" class="h-full w-full object-cover grayscale-[30%]">
                                <div class="absolute inset-0 bg-indigo-900/10 mix-blend-multiply"></div>
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center">
                                    <button type="button" id="btn-re-scan" class="bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-[9px] font-black text-indigo-700 shadow-xl hover:bg-white transition active:scale-95 uppercase tracking-widest">PERBARUI FOTO</button>
                                </div>
                            </div>
                            @endif
"""

# Modify the extracted scan_ui
scan_ui = scan_ui.replace('<div id="camera-placeholder" class="h-full', '<div id="camera-placeholder" class="{{ $activeFace ? \'hidden\' : \'\' }} h-full')
scan_ui = scan_ui.replace('<div id="camera-placeholder"', existing_face_ui + '\n                            <div id="camera-placeholder"')

# Also add the PHP block at the top of the Face Scan container
scan_ui = scan_ui.replace('<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg p-6 flex flex-col items-center">', 
                          '<div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-lg p-6 flex flex-col items-center">\n' + existing_face_php)

# Also change SIMPAN PENGGUNA to UPDATE PENGGUNA in the submit button
scan_ui = scan_ui.replace('SIMPAN PENGGUNA', 'UPDATE PENGGUNA')
scan_ui = scan_ui.replace('disabled:bg-slate-300 disabled:shadow-none" disabled>', 'disabled:bg-slate-300 disabled:shadow-none">')

# Modify the extracted script to handle existingFaceUI
script_content = script_content.replace("const btnSubmit = document.getElementById('btn-submit-form');", 
"""const btnSubmit = document.getElementById('btn-submit-form');
        const existingFaceUI = document.getElementById('existing-face-ui');
        const btnReScan = document.getElementById('btn-re-scan');
        
        if(btnReScan) {
            btnReScan.addEventListener('click', () => {
                existingFaceUI.classList.add('hidden');
                placeholder.classList.remove('hidden');
            });
        }""")

script_content = script_content.replace("""            streamImg.src = "";
            activeCamera.classList.add('hidden');
            placeholder.classList.remove('hidden');""",
"""            streamImg.src = "";
            activeCamera.classList.add('hidden');
            if(existingFaceUI) {
                existingFaceUI.classList.remove('hidden');
            } else {
                placeholder.classList.remove('hidden');
            }""")

# Now replace the parts in edit_html
edit_html = re.sub(r'<!-- Right: Face Scan -->\s*<div class="space-y-6">.*?</form>', '<!-- Right: Face Scan -->\n                ' + scan_ui, edit_html, flags=re.DOTALL)
edit_html = re.sub(r'<script>.*?</script>', script_content, edit_html, flags=re.DOTALL)

with open('resources/views/superadmin/pengguna/edit.blade.php', 'w') as f:
    f.write(edit_html)

print("Done")
