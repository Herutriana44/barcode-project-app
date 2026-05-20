import os
import shutil
import subprocess
import zipfile
import json
from datetime import datetime

CONFIG_FILE = 'deploy_config.json'
CONFIG_EXAMPLE = 'deploy_config.json.example'

def print_header(text):
    print("\n" + "="*50)
    print(f" {text}")
    print("="*50)

def run_command(command, shell=True):
    print(f"Running: {command}")
    try:
        subprocess.run(command, shell=shell, check=True)
        return True
    except subprocess.CalledProcessError as e:
        print(f"Error running command: {e}")
        return False

def load_config():
    if not os.path.exists(CONFIG_FILE):
        print_header("ERROR: CONFIG FILE MISSING")
        print(f"File '{CONFIG_FILE}' tidak ditemukan.")
        print(f"Silakan salin '{CONFIG_EXAMPLE}' menjadi '{CONFIG_FILE}'")
        print("Lalu isi data-data hosting Anda di dalamnya.")
        return None
    
    try:
        with open(CONFIG_FILE, 'r') as f:
            return json.load(f)
    except Exception as e:
        print(f"Error reading JSON: {e}")
        return None

def create_env_file(config):
    example_path = '.env.example'
    if not os.path.exists(example_path):
        with open(example_path, 'w') as f:
            f.write("APP_NAME=Laravel\nAPP_ENV=local\nAPP_KEY=\nAPP_DEBUG=true\nAPP_URL=http://localhost\n")
    
    with open(example_path, 'r') as f:
        lines = f.readlines()

    new_lines = []
    for line in lines:
        if line.startswith('APP_NAME='):
            new_lines.append(f'APP_NAME="{config.get("APP_NAME", "Laravel")}"\n')
        elif line.startswith('APP_ENV='):
            new_lines.append('APP_ENV=production\n')
        elif line.startswith('APP_DEBUG='):
            new_lines.append('APP_DEBUG=false\n')
        elif line.startswith('APP_URL='):
            new_lines.append(f'APP_URL={config.get("APP_URL", "http://localhost")}\n')
        elif line.startswith('DB_HOST='):
            new_lines.append(f'DB_HOST={config.get("DB_HOST", "localhost")}\n')
        elif line.startswith('DB_DATABASE='):
            new_lines.append(f'DB_DATABASE={config.get("DB_DATABASE", "")}\n')
        elif line.startswith('DB_USERNAME='):
            new_lines.append(f'DB_USERNAME={config.get("DB_USERNAME", "")}\n')
        elif line.startswith('DB_PASSWORD='):
            new_lines.append(f'DB_PASSWORD="{config.get("DB_PASSWORD", "")}"\n')
        else:
            new_lines.append(line)

    with open('.env.production', 'w') as f:
        f.writelines(new_lines)
    
    print("Generated .env.production")

def zip_project(zip_name):
    print(f"Zipping project into {zip_name}...")
    exclude_patterns = [
        '.git', 'node_modules', '.env', 'prepare_deployment.py',
        'deploy_config.json', 'deploy_config.json.example', '.env.production',
        'tests', '.phpunit.result.cache', '.vscode', '.idea',
        'storage/logs', 'storage/framework/cache/data',
        'storage/framework/sessions', 'storage/framework/testing',
        'storage/framework/views',
    ]

    with zipfile.ZipFile(zip_name, 'w', zipfile.ZIP_DEFLATED) as zipf:
        for root, dirs, files in os.walk('.'):
            dirs[:] = [d for d in dirs if not any(d == ex or root.endswith(ex) for ex in exclude_patterns)]
            for file in files:
                if any(file == ex for ex in exclude_patterns) or file.endswith('.zip'):
                    continue
                file_path = os.path.join(root, file)
                archive_name = os.path.relpath(file_path, '.')
                zipf.write(file_path, archive_name)
        
        if os.path.exists('.env.production'):
            zipf.write('.env.production', '.env')
            
    print(f"Project zipped successfully: {zip_name}")

def main():
    print_header("LARAVEL DEPLOYMENT PREPARER (JSON MODE)")
    
    config = load_config()
    if not config:
        return

    # 2. Build Assets
    if config.get("RUN_NPM_BUILD", True):
        print_header("BUILDING ASSETS")
        if not run_command("npm install && npm run build"):
            print("Failed to build assets. Proceeding anyway...")

    # 3. Prepare Dependencies
    if config.get("RUN_COMPOSER_INSTALL", True):
        print_header("PREPARING PHP DEPENDENCIES")
        if not run_command("composer install --no-dev --optimize-autoloader"):
            print("Failed to install dependencies. Proceeding anyway...")

    # 4. Generate .env
    print_header("GENERATING PRODUCTION .ENV")
    create_env_file(config)

    # 5. Package for Upload
    print_header("PACKAGING")
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    zip_name = f"deploy_{timestamp}.zip"
    zip_project(zip_name)

    print_header("DEPLOYMENT READY")
    print(f"1. Upload '{zip_name}' ke hosting.")
    print(f"2. Extract di folder project hosting.")
    print(f"3. Pastikan DB '{config.get('DB_DATABASE')}' sudah dibuat di hosting.")
    print("="*50)

if __name__ == "__main__":
    main()
