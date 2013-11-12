set :application, "Adstacy"
set :user,        "ubuntu"
set :domain,      "54.254.148.228"
set :deploy_to,   "/app/adstacy"
set :app_path,    "app"
set :web_path,    "web"

set :repository,  "git@bitbucket.org:wlzch/ads.git"
set :scm,         :git
set :branch,       "master"
set :ssh_options, {:forward_agent => true}
ssh_options[:keys] = [File.join(ENV["AWS_HOME"], "suwandi-keypair-singapore.pem")]
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `subversion`, `mercurial`, `perforce`, or `none`

set :model_manager, "doctrine"
# Or: `propel`

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

set  :use_sudo, true # must use sudo because setfacl will fail if not
set  :keep_releases, 8
set  :deploy_via, :remote_cache
set  :branch, :master
set  :repository_cache, "cached_copy"

set  :shared_files, ["app/config/parameters.yml"]
set  :shared_children, ["app/logs", "app/sessions", "web/uploads", "web/media", "web/files", "vendor", "node_modules"]
set  :writable_dirs, ["app/cache", "app/logs", "app/sessions", "web/uploads", "web/media", "web/files"]
set  :webserver_user, "www-data"
set  :permission_method, :acl
set  :use_set_permissions, true
set  :use_composer, true
set  :dump_assetic_assets, true

# Change ACL on the app/logs and app/cache directories
after "symfony:composer:install", "npm:install"
after "deploy", "symfony:clear_apc"
before "symfony:assetic:dump", "symfony:dump_js_routing"
before "symfony:assetic:dump", "symfony:dump_js_translations"
 
namespace :npm do
  desc "Run npm install"
  task :install, :roles => :app, :except => { :no_release => true } do
    run "#{try_sudo} sh -c 'cd #{latest_release} && npm install'";
    capifony_puts_ok
  end
end

namespace :symfony do
  desc "Clear apc cache"
  task :clear_apc do
    capifony_pretty_print "--> Clear apc cache"
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} apc:clear --env=prod'"
    capifony_puts_ok
  end

  desc "Dump js routing"
  task :dump_js_routing do
    capifony_pretty_print "--> Dump js routing"
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} fos:js-routing:dump -e=prod'"
    capifony_puts_ok
  end

  desc "Dump js translations"
  task :dump_js_translations do
    capifony_pretty_print "--> Dump js translations"
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} funddy:jstranslations:dump'"
    capifony_puts_ok
  end
end

namespace :adstacy do
    task :populate_redis do
        capifony_pretty_print "--> Populating redis data"
        run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} adstacy:redis:populate all -e=prod'"
        capifony_puts_ok
    end

    task :clean_notification do
        capifony_pretty_print "--> Cleaning notification"
        run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} adstacy:notification:clean -e=prod'"
        capifony_puts_ok
    end
end

# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL
