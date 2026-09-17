# Script to parse all 10 pages of Table 53B from PDF OCR into JSON and PHP code

import json
import re

# Let's define the columns for each of the 5 pairs of pages:
cols_block1 = [round(0.690 + i*0.001, 3) for i in range(28)] # 0.690 to 0.717
cols_block2 = [round(0.718 + i*0.001, 3) for i in range(28)] # 0.718 to 0.745
cols_block3 = [round(0.746 + i*0.001, 3) for i in range(14)] + [round(0.800 + i*0.001, 3) for i in range(14)] # 0.746-0.759 and 0.800-0.813
cols_block4 = [round(0.814 + i*0.001, 3) for i in range(28)] # 0.814 to 0.841
cols_block5 = [round(0.842 + i*0.001, 3) for i in range(28)] # 0.842 to 0.869

print("Block columns defined successfully")
