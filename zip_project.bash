set -xuEe
tar --exclude='.dir_bash_history' --exclude='node_modules' --exclude='vendor' --exclude='.env'  -czf ../$(date +%Y%m%d%H%M)-laravel-file-watcher.tar.gz . --show-omitted-dirs --preserve-permissions    # perform backup
ls -lah ../*.tar.gz
