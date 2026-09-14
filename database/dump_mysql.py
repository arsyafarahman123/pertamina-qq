import sqlite3
import re

conn = sqlite3.connect('d:/lab-qq/database/database.sqlite')
cursor = conn.cursor()

output_sql = "-- Database Dump Pertamina QQ Fuel Terminal Maos\n"
output_sql += "-- Compatible with MySQL / MariaDB (cPanel & InfinityFree)\n\n"
output_sql += "SET FOREIGN_KEY_CHECKS=0;\n\n"

# Get all tables
cursor.execute("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';")
tables = [row[0] for row in cursor.fetchall()]

for table in tables:
    output_sql += f"-- Table structure for `{table}`\n"
    output_sql += f"DROP TABLE IF EXISTS `{table}`;\n"
    
    # Get columns
    cursor.execute(f"PRAGMA table_info('{table}')")
    cols = cursor.fetchall()
    col_defs = []
    for col in cols:
        col_id, name, col_type, notnull, dflt_val, pk = col
        m_type = col_type.upper()
        if 'INT' in m_type:
            m_type = 'BIGINT UNSIGNED' if pk else 'BIGINT'
        elif 'VARCHAR' in m_type or 'TEXT' in m_type or not m_type:
            m_type = 'VARCHAR(255)' if 'VARCHAR' in m_type else 'TEXT'
        elif 'DATETIME' in m_type:
            m_type = 'DATETIME'
        elif 'DATE' in m_type:
            m_type = 'DATE'
        elif 'FLOAT' in m_type or 'DOUBLE' in m_type or 'NUMERIC' in m_type:
            m_type = 'DOUBLE'
        
        nullable = "NOT NULL" if notnull else "NULL"
        if pk:
            col_defs.append(f"`{name}` {m_type} AUTO_INCREMENT PRIMARY KEY")
        else:
            col_defs.append(f"`{name}` {m_type} {nullable}")
            
    output_sql += f"CREATE TABLE `{table}` (\n  " + ",\n  ".join(col_defs) + "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n"
    
    # Get rows
    cursor.execute(f"SELECT * FROM `{table}`")
    rows = cursor.fetchall()
    if rows:
        col_names = [f"`{col[1]}`" for col in cols]
        output_sql += f"INSERT INTO `{table}` (" + ", ".join(col_names) + ") VALUES\n"
        row_strs = []
        for r in rows:
            vals = []
            for v in r:
                if v is None:
                    vals.append("NULL")
                elif isinstance(v, (int, float)):
                    vals.append(str(v))
                else:
                    escaped = str(v).replace("'", "''").replace("\\", "\\\\")
                    vals.append(f"'{escaped}'")
            row_strs.append("(" + ", ".join(vals) + ")")
        output_sql += ",\n".join(row_strs) + ";\n\n"

output_sql += "SET FOREIGN_KEY_CHECKS=1;\n"

with open('d:/lab-qq/database/pertamina_qq_mysql.sql', 'w', encoding='utf-8') as f:
    f.write(output_sql)

print("SQL Dump created successfully!")
