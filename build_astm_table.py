import json

# Let's define the page text blocks
# Block 1.1 & 1.2: 0.690 to 0.717 (28 cols)
cols_1 = [round(0.690 + i*0.001, 3) for i in range(28)]
# Block 2.1 & 2.2: 0.718 to 0.745 (28 cols)
cols_2 = [round(0.718 + i*0.001, 3) for i in range(28)]
# Block 3.1 & 3.2: 0.746 to 0.759 (14 cols) + 0.800 to 0.813 (14 cols)
cols_3 = [round(0.746 + i*0.001, 3) for i in range(14)] + [round(0.800 + i*0.001, 3) for i in range(14)]
# Block 4.1 & 4.2: 0.814 to 0.841 (28 cols)
cols_4 = [round(0.814 + i*0.001, 3) for i in range(28)]
# Block 5.1 & 5.2: 0.842 to 0.869 (28 cols)
cols_5 = [round(0.842 + i*0.001, 3) for i in range(28)]

table = {} # (round(t, 1), round(rho_t, 3)) -> val

def add_lines(cols, text):
    for line in text.strip().split('\n'):
        parts = line.replace(',', '.').split()
        if not parts:
            continue
        try:
            t = round(float(parts[0]), 1)
        except ValueError:
            continue
        vals = parts[1:]
        for c, v_str in zip(cols, vals):
            try:
                v = float(v_str)
                if v > 0:
                    table[(t, round(c, 3))] = v
            except ValueError:
                pass

print("Helper ready")
