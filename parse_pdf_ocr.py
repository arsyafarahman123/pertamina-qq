import json

# We define the column headers for each block
cols_b1 = [round(0.690 + i*0.001, 3) for i in range(28)] # 0.690 - 0.717
cols_b2 = [round(0.718 + i*0.001, 3) for i in range(28)] # 0.718 - 0.745
cols_b3 = [round(0.746 + i*0.001, 3) for i in range(14)] + [round(0.800 + i*0.001, 3) for i in range(14)] # 0.746-0.759 & 0.800-0.813
cols_b4 = [round(0.814 + i*0.001, 3) for i in range(28)] # 0.814 - 0.841
cols_b5 = [round(0.842 + i*0.001, 3) for i in range(28)] # 0.842 - 0.869

print("Columns ready.")
