import os
import sys
import ftplib
import time

FTP_HOST = 'ftpupload.net'
FTP_USER = 'if0_42910768'
FTP_PASS = '21t0qsE3M6Oxork'

EXCLUDE_DIRS = {'.git', 'node_modules', '.idea', '.vscode', 'tests'}
EXCLUDE_FILES = {'upload_ftp.py', 'dir_result.txt', 'link.txt', 'sizes.txt', 'views-list.txt', 
                 'login-56-106.txt', 'login-mid.txt', 'migrate_result.txt', 'migrate_status.txt',
                 'storage_link.txt', 'riwayat-mid.txt', 'app-head.txt', 'brand-lines.txt'}

def connect_ftp():
    print(f'Connecting to {FTP_HOST}...')
    ftp = ftplib.FTP(FTP_HOST, timeout=30)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    return ftp

def ensure_dir(ftp, path):
    parts = path.strip('/').split('/')
    cur = ''
    for p in parts:
        cur += '/' + p
        try:
            ftp.cwd(cur)
        except Exception:
            try:
                ftp.mkd(cur)
                ftp.cwd(cur)
            except Exception:
                pass

def upload_project(local_dir, remote_base='/htdocs'):
    ftp = connect_ftp()
    ensure_dir(ftp, remote_base)
    
    total_files = 0
    uploaded_files = 0
    
    for root, dirs, files in os.walk(local_dir):
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]
        for f in files:
            if f not in EXCLUDE_FILES and not f.endswith('.tmp'):
                total_files += 1
                
    print(f'Total files to process: {total_files}')
    
    for root, dirs, files in os.walk(local_dir):
        dirs[:] = [d for d in dirs if d not in EXCLUDE_DIRS]
        
        rel_dir = os.path.relpath(root, local_dir).replace(chr(92), '/')
        if rel_dir == '.':
            target_remote_dir = remote_base
        else:
            target_remote_dir = f'{remote_base}/{rel_dir}'
            
        ensure_dir(ftp, target_remote_dir)
        ftp.cwd(target_remote_dir)
        
        for f in files:
            if f in EXCLUDE_FILES or f.endswith('.tmp'):
                continue
                
            local_path = os.path.join(root, f)
            remote_name = f
            
            if rel_dir == '.' and f == '.env.production':
                remote_name = '.env'
                
            uploaded_files += 1
            if uploaded_files % 50 == 0 or uploaded_files == total_files:
                print(f'[{uploaded_files}/{total_files}] Uploading: {rel_dir}/{f}')
                
            for attempt in range(3):
                try:
                    with open(local_path, 'rb') as fp:
                        ftp.storbinary(f'STOR {remote_name}', fp)
                    break
                except Exception as e:
                    print(f'  Retry {attempt+1} on {f}: {e}')
                    time.sleep(2)
                    try:
                        ftp = connect_ftp()
                        ftp.cwd(target_remote_dir)
                    except Exception:
                        pass
                        
    ftp.quit()
    print('Upload completed successfully!')

if __name__ == '__main__':
    upload_project(os.path.abspath('.'))
