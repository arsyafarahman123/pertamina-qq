import os
import sys
import ftplib
import time

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

FTP_HOST = 'ftpupload.net'
FTP_USER = 'if0_42910768'
FTP_PASS = '21t0qsE3M6Oxork'

files_to_upload = [
    'app/Services/DensityCorrectionService.php',
    'app/Services/FuelMaosService.php',
    'resources/views/retain-sampel/form.blade.php',
    'resources/views/retain-sampel/_tutorial-modal.blade.php',
    'resources/views/retain-sampel/_slide-laporan.blade.php',
    'resources/views/retain-sampel/cetak.blade.php',
    'resources/views/dashboard/index.blade.php',
    'resources/views/auth/login.blade.php',
    'config/session.php',
    'bootstrap/app.php',
    'app/Http/Controllers/AuthController.php',
    'routes/web.php',
]

def connect_ftp():
    print(f"Connecting to {FTP_HOST}...", flush=True)
    ftp = ftplib.FTP()
    ftp.connect(FTP_HOST, 21, timeout=15)
    ftp.login(FTP_USER, FTP_PASS)
    ftp.set_pasv(True)
    print("Connected & Logged in!", flush=True)
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

def main():
    ftp = connect_ftp()
    remote_base = '/htdocs'

    for rel_path in files_to_upload:
        local_path = os.path.abspath(rel_path)
        if not os.path.exists(local_path):
            print(f"Local file not found: {local_path}")
            continue

        rel_dir = os.path.dirname(rel_path).replace('\\', '/')
        file_name = os.path.basename(rel_path)
        target_dir = f"{remote_base}/{rel_dir}" if rel_dir else remote_base

        ensure_dir(ftp, target_dir)
        ftp.cwd(target_dir)

        print(f"Uploading {rel_path} -> {target_dir}/{file_name}...", flush=True)
        for attempt in range(3):
            try:
                with open(local_path, 'rb') as fp:
                    ftp.storbinary(f"STOR {file_name}", fp)
                print(f"[OK] Uploaded {file_name}", flush=True)

                # Jika file ada di public/js/, upload juga ke /htdocs/js/
                if rel_dir == 'public/js':
                    alt_dir = f"{remote_base}/js"
                    ensure_dir(ftp, alt_dir)
                    ftp.cwd(alt_dir)
                    with open(local_path, 'rb') as fp2:
                        ftp.storbinary(f"STOR {file_name}", fp2)
                    print(f"[OK] Also Uploaded to {alt_dir}/{file_name}", flush=True)
                break
            except Exception as e:
                print(f"  Retry {attempt+1} on {file_name}: {e}", flush=True)
                time.sleep(2)
                ftp = connect_ftp()
                ftp.cwd(target_dir)

    ftp.quit()
    print("All modified files deployed successfully to InfinityFree!", flush=True)

if __name__ == '__main__':
    main()
