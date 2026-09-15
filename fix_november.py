import json
import sqlite3

# 1. Update JSON seed file
with open('checklists_seed.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

count = 0
for item in data:
    tgl = str(item.get('tanggal_periksa', ''))
    if tgl in ['2026-11-13', '2026-11-18']:
        new_tgl = '2025' + tgl[4:]
        nopol = item.get('nomor_polisi')
        print(f"Fixed JSON: {nopol} {tgl} -> {new_tgl}")
        item['tanggal_periksa'] = new_tgl
        count += 1

with open('checklists_seed.json', 'w', encoding='utf-8') as f:
    json.dump(data, f, indent=2)

print(f"checklists_seed.json updated: {count} records fixed.")

# 2. Update local SQLite DB
sqlite_conn = sqlite3.connect('database/database.sqlite')
cursor = sqlite_conn.cursor()
cursor.execute("UPDATE checklist_mt_maos SET tanggal_periksa = '2025-11-13' WHERE tanggal_periksa = '2026-11-13'")
cursor.execute("UPDATE checklist_mt_maos SET tanggal_periksa = '2025-11-18' WHERE tanggal_periksa = '2026-11-18'")
sqlite_conn.commit()
print("Local SQLite DB updated.")
sqlite_conn.close()

