{ pkgs, config, ... }:

let
  # Generate the Apache config natively in Nix
  apacheConf = pkgs.writeText "httpd.conf" ''
    ServerRoot "${pkgs.apacheHttpd}"
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
    
    DocumentRoot "''${DEVENV_ROOT}"
    <Directory "''${DEVENV_ROOT}">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:${config.languages.php.fpm.pools.web.socket}|fcgi://localhost/"
    </FilesMatch>
    
    ErrorLog "''${DEVENV_STATE}/apache_error.log"
    CustomLog "''${DEVENV_STATE}/apache_access.log" common
    PidFile "''${DEVENV_STATE}/httpd.pid"
  '';

in {
  # 1. PHP Setup
  languages.php.enable = true;
  languages.php.extensions = [ "mysqli" "pdo_mysql" ];
  languages.php.fpm.pools.web.settings = {
    "pm" = "dynamic";
    "pm.max_children" = 5;
    "pm.start_servers" = 2;
    "pm.min_spare_servers" = 1;
    "pm.max_spare_servers" = 3;
  };

  # 2. MariaDB Setup
  services.mysql.enable = true;
  services.mysql.initialDatabases = [{ name = "lamp_dev"; }];

  # 3. Apache Setup
  packages = [ pkgs.apacheHttpd ];
  processes.apache.exec = "httpd -f ${apacheConf} -DFOREGROUND";

  # 4. Greeting message when you enter the shell
  enterShell = ''
    echo "----------------------------------------------------"
    echo "Pure Devenv LAMP Stack Ready"
    echo "Run 'devenv up' to start the server."
    echo "----------------------------------------------------"
  '';
}