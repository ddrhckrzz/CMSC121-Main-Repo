{
  description = "A basic flake with a shell";
  inputs.nixpkgs.url = "github:NixOS/nixpkgs/nixpkgs-unstable";
  inputs.systems.url = "github:nix-systems/default";
  inputs.flake-utils = {
    url = "github:numtide/flake-utils";
    inputs.systems.follows = "systems";
  };

  outputs =
    { nixpkgs, flake-utils, ... }:
    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = nixpkgs.legacyPackages.${system};

        # PHP version and Extensions
        myPhp = pkgs.php.withExtensions (
          { 
            enabled,
            all 
          }:
            enabled ++ [
              all.mysqli
              all.pdo_mysql
            ]
        );
        # Apache Package (change if necessary)
        myApache = pkgs.apacheHttpd;
      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            myApache
            pkgs.mariadb
            myPhp
          ];

          shellHook = ''
            # 1. Setup Project Locals
            export PROJECT_ROOT="$(pwd)"
            export DATA_DIR="$PROJECT_ROOT/.data"
            export APACHE_LOG_DIR="$DATA_DIR/apache_logs"
            export MYSQL_DATA_DIR="$DATA_DIR/mysql"
            export MYSQL_HOME="$DATA_DIR/mysql"
            
            mkdir -p "$APACHE_LOG_DIR" "$MYSQL_DATA_DIR"

            # 2. Configure MariaDB (MySQL)
            # Initialize database if it doesn't exist
            if [ ! -d "$MYSQL_DATA_DIR/mysql" ]; then
              echo "Initializing MariaDB data directory..."
              mysql_install_db --datadir="$MYSQL_DATA_DIR" --auth-root-authentication-method=normal >/dev/null
            fi

            # 3. Configure Apache (httpd.conf)
            # We generate a config file dynamically to use absolute paths
            cat > "$DATA_DIR/httpd.conf" <<EOF
            ServerRoot "${myApache}"
            Listen 8080
            
            # Load required modules
            LoadModule mpm_event_module modules/mod_mpm_event.so
            LoadModule authz_core_module modules/mod_authz_core.so
            LoadModule dir_module modules/mod_dir.so
            LoadModule mime_module modules/mod_mime.so
            LoadModule unixd_module modules/mod_unixd.so
            
            # Load PHP Module
            # Note: NixOS PHP package structure often requires locating the .so manually if not using the php-fpm method.
            # For a dev shell, we will use php-fpm proxying as it is cleaner in Nix, 
            # BUT for simplicity in a "Classic LAMP" feel, we use mod_php if available or proxy to php-fpm.
            # A simpler alternative for dev is often just 'php -S', but here is Apache:
            
            modules/mod_unixd.so
            
            DocumentRoot "$PROJECT_ROOT"
            <Directory "$PROJECT_ROOT">
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
                DirectoryIndex index.php index.html
            </Directory>

            # Basic PHP Setup (via cli wrapper or cgi if mod_php is tricky in pure flakes without overlays)
            # For a robust setup, we will use a simple proxy to php-fpm below or just rely on the user running php-fpm.
            # To keep this guide SIMPLE, we will assume you might run 'php -S' for web, 
            # BUT if you strictly want apache, we add the Proxy setup for PHP-FPM:
            
            LoadModule proxy_module modules/mod_proxy.so
            LoadModule proxy_fcgi_module modules/mod_proxy_fcgi.so
            
            <FilesMatch \.php$>
                SetHandler "proxy:unix:$DATA_DIR/php-fpm.sock|fcgi://localhost/"
            </FilesMatch>
            
            ErrorLog "$APACHE_LOG_DIR/error.log"
            CustomLog "$APACHE_LOG_DIR/access.log" common
            PidFile "$DATA_DIR/httpd.pid"
            EOF

            # 4. Create Helper Functions
            start-lamp() {
              echo "Starting MariaDB..."
              mysqld --datadir="$MYSQL_DATA_DIR" --pid-file="$DATA_DIR/mysql.pid" --socket="$DATA_DIR/mysql.sock" --skip-networking &
              
              echo "Starting PHP-FPM..."
              # Create a minimal php-fpm config
              cat > "$DATA_DIR/php-fpm.conf" <<PHP_EOF
              [global]
              pid = $DATA_DIR/php-fpm.pid
              error_log = $DATA_DIR/php-fpm.log
              [www]
              listen = $DATA_DIR/php-fpm.sock
              pm = dynamic
              pm.max_children = 5
              pm.start_servers = 2
              pm.min_spare_servers = 1
              pm.max_spare_servers = 3
              PHP_EOF
              
              ${myPhp}/sbin/php-fpm -y "$DATA_DIR/php-fpm.conf" &

              echo "Starting Apache on port 8080..."
              httpd -f "$DATA_DIR/httpd.conf" -k start
              
              echo "LAMP Stack started."
              echo "Web: http://localhost:8080"
              echo "MySQL Socket: $DATA_DIR/mysql.sock"
            }

            stop-lamp() {
              echo "Stopping Apache..."
              [ -f "$DATA_DIR/httpd.pid" ] && kill $(cat "$DATA_DIR/httpd.pid")
              
              echo "Stopping PHP-FPM..."
              [ -f "$DATA_DIR/php-fpm.pid" ] && kill $(cat "$DATA_DIR/php-fpm.pid")
              
              echo "Stopping MariaDB..."
              [ -f "$DATA_DIR/mysql.pid" ] && kill $(cat "$DATA_DIR/mysql.pid")
            }
            
            # Alias mysql to use the socket by default
            alias mysql="mysql --socket=$DATA_DIR/mysql.sock"
            
            echo "------------------------------------------------------------"
            echo "LAMP Dev Environment"
            echo "Run 'start-lamp' to start services."
            echo "Run 'stop-lamp' to stop services."
            echo "------------------------------------------------------------"
          '';
        };
      }
    );
}
