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

        # 1. Define PHP
        myPhp = pkgs.php.withExtensions ({ enabled, all }: enabled ++ [ all.mysqli all.pdo_mysql ]);
        
        # 2. Define Apache
        myApache = pkgs.apacheHttpd;

        # 3. Create the 'start-lamp' script
        startLampScript = pkgs.writeShellScriptBin "start-lamp" ''
          # Access env vars set by the shellHook
          echo "Starting MariaDB..."
          # Redirect logs to a file so they don't spam the terminal
          mariadbd --datadir="$MYSQL_DATA_DIR" --pid-file="$DATA_DIR/mysql.pid" --socket="$DATA_DIR/mysql.sock" --skip-networking > "$DATA_DIR/mysql.log" 2>&1 &
          
          echo "Starting PHP-FPM..."
          echo "
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
          " > "$DATA_DIR/php-fpm.conf"
          
          ${myPhp}/sbin/php-fpm -y "$DATA_DIR/php-fpm.conf" &

          echo "Starting Apache on port 8080..."
          httpd -f "$DATA_DIR/httpd.conf" -k start
          
          echo "------------------------------------------------"
          echo "LAMP Stack started successfully!"
          echo "Web: http://localhost:8080"
          echo "DB Logs: $DATA_DIR/mysql.log"
          echo "Use 'local-mysql' to connect to the DB."
          echo "------------------------------------------------"
        '';

        # 4. Create the 'stop-lamp' script
        stopLampScript = pkgs.writeShellScriptBin "stop-lamp" ''
          echo "Stopping Apache..."
          [ -f "$DATA_DIR/httpd.pid" ] && kill $(cat "$DATA_DIR/httpd.pid") 2>/dev/null
          
          echo "Stopping PHP-FPM..."
          [ -f "$DATA_DIR/php-fpm.pid" ] && kill $(cat "$DATA_DIR/php-fpm.pid") 2>/dev/null
          
          echo "Stopping MariaDB..."
          [ -f "$DATA_DIR/mysql.pid" ] && kill $(cat "$DATA_DIR/mysql.pid") 2>/dev/null
          
          echo "All services stopped."
        '';
        
        # 5. Create a 'local-mysql' wrapper
        mysqlWrapper = pkgs.writeShellScriptBin "local-mysql" ''
          mariadb --socket="$PROJECT_ROOT/.data/mysql.sock" "$@"
        '';

      in
      {
        devShells.default = pkgs.mkShell {
          buildInputs = [
            myApache
            pkgs.mariadb
            myPhp
            startLampScript
            stopLampScript
            mysqlWrapper
          ];

          shellHook = ''
            export PROJECT_ROOT="$(pwd)"
            export DATA_DIR="$PROJECT_ROOT/.data"
            export APACHE_LOG_DIR="$DATA_DIR/apache_logs"
            export MYSQL_DATA_DIR="$DATA_DIR/mysql"
            
            mkdir -p "$APACHE_LOG_DIR" "$MYSQL_DATA_DIR"

            if [ ! -d "$MYSQL_DATA_DIR/mysql" ]; then
              echo "Initializing MariaDB data directory..."
              mariadb-install-db --datadir="$MYSQL_DATA_DIR" --auth-root-authentication-method=normal >/dev/null
            fi

            # Added 'ServerName localhost' to silence the warning
            echo "
            ServerRoot \"${myApache}\"
            Listen 8080
            ServerName localhost
            
            LoadModule mpm_event_module modules/mod_mpm_event.so
            LoadModule authz_core_module modules/mod_authz_core.so
            LoadModule dir_module modules/mod_dir.so
            LoadModule mime_module modules/mod_mime.so
            LoadModule unixd_module modules/mod_unixd.so
            LoadModule log_config_module modules/mod_log_config.so
            LoadModule proxy_module modules/mod_proxy.so
            LoadModule proxy_fcgi_module modules/mod_proxy_fcgi.so
            
            DocumentRoot \"$PROJECT_ROOT\"
            <Directory \"$PROJECT_ROOT\">
                Options Indexes FollowSymLinks
                AllowOverride All
                Require all granted
                DirectoryIndex index.php index.html
            </Directory>

            <FilesMatch \.php$>
                SetHandler \"proxy:unix:$DATA_DIR/php-fpm.sock|fcgi://localhost/\"
            </FilesMatch>
            
            ErrorLog \"$APACHE_LOG_DIR/error.log\"
            CustomLog \"$APACHE_LOG_DIR/access.log\" common
            PidFile \"$DATA_DIR/httpd.pid\"
            " > "$DATA_DIR/httpd.conf"
            
            echo "LAMP Dev Environment Loaded."
          '';
        };
      }
    );
}