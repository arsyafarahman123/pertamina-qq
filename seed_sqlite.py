import sqlite3
import json
from datetime import datetime

conn = sqlite3.connect('d:/lab-qq/database/database.sqlite')
cursor = conn.cursor()

with open('d:/lab-qq/checklists_seed.json', 'r', encoding='utf-8') as f:
    records = json.load(f)

count = 0
now = datetime.now().strftime('%Y-%m-%d %H:%M:%S')

for r in records:
    # check existing
    cursor.execute("SELECT id FROM checklist_mt_maos WHERE nomor_polisi = ? AND tanggal_periksa = ?", (r['nomor_polisi'], r['tanggal_periksa']))
    row = cursor.fetchone()
    if row:
        cursor.execute("""
            UPDATE checklist_mt_maos 
            SET pemilik = ?, tanggal_exp = ?, tera = ?, results = ?, notes = ?, ket_tambahan = ?, status = ?, user_id = ?, created_by = ?, updated_at = ?
            WHERE id = ?
        """, (
            r['pemilik'], r['tanggal_exp'], json.dumps(r['tera']), json.dumps(r['results']), 
            json.dumps(r['notes']), r['ket_tambahan'], r['status'], r['user_id'], r['created_by'], now, row[0]
        ))
    else:
        cursor.execute("""
            INSERT INTO checklist_mt_maos (nomor_polisi, pemilik, tanggal_exp, tanggal_periksa, tera, results, notes, ket_tambahan, status, user_id, created_by, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (
            r['nomor_polisi'], r['pemilik'], r['tanggal_exp'], r['tanggal_periksa'], 
            json.dumps(r['tera']), json.dumps(r['results']), json.dumps(r['notes']), 
            r['ket_tambahan'], r['status'], r['user_id'], r['created_by'], now, now
        ))
    count += 1

conn.commit()
cursor.execute("SELECT COUNT(*) FROM checklist_mt_maos")
total = cursor.fetchone()[0]
conn.close()

print(f"Successfully processed {count} records! Total in local SQLite: {total}")
