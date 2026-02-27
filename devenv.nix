{ pkgs, config, lib, ... }:

{
  # 1. PHP Setup
  languages.php.enable = true;
  languages.php.extensions = [ "mysqli" "pdo_mysql" ];
  languages.php.fpm.pools.web.settings = {
    "pm" = "dynamic";
    "pm.max_children" = 5;
    "pm.start_servers" = 2;
    "pm.min_spare_servers" = 1;
    "pm.max_spare_servers" = 3;
    "listen" = "127.0.0.1:9000"; 
  };

  # 2. MariaDB Setup
  services.mysql.enable = true;
  services.mysql.initialDatabases = [{ name = "lamp_dev"; }];

  # 3. Caddy Setup
  services.caddy.enable = true;
  services.caddy.virtualHosts."http://localhost:8080" = {
    extraConfig = ''
      root * .
      php_fastcgi 127.0.0.1:9000
      file_server
    '';
  };

  scripts.devup.exec = ''
    pkill -9 -f php-fpm 2>/dev/null || true
    devenv up
  '';

  # 4. Foolproof Startup Alias
  enterShell = ''
    echo "----------------------------------------------------"
    echo "Pure Devenv Caddy/PHP/MariaDB Stack Ready"
    echo "Run 'devup' (instead of devenv up) to start safely."
    echo "----------------------------------------------------"
  '';
}