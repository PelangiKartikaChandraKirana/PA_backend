import sys

def check_braces(filename):
    with open(filename, 'r') as f:
        lines = f.readlines()
    
    stack = []
    for i, line in enumerate(lines):
        for char in line:
            if char == '{':
                stack.append(i + 1)
            elif char == '}':
                if not stack:
                    print(f"Extra closing brace at line {i + 1}")
                    return
                stack.pop()
    
    if stack:
        print(f"Unclosed braces opened at lines: {stack}")
    else:
        print("All braces match perfectly!")

check_braces("/Users/elrya/fullstack_siapman/siapman_baru /lib/pages/camera_presensi_page.dart")
