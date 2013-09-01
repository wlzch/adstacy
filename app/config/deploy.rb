set :application, "Adstacy"
set :user,        "ubuntu"
set :domain,      "adstacy.com"
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
set  :keep_releases,  3
# cannot use remote_cache because of empty directories causing symlink fails
# set  :deploy_via, :remote_cache

set  :shared_files, ["app/config/parameters.yml"]
set  :shared_children, ["app/logs", "app/var/sessions", "web/uploads", "web/media", "web/files", "vendor", "node_modules"]
set  :writable_dirs, ["app/cache", "app/logs", "app/vars", "web/uploads", "web/media", "web/files"]
set  :webserver_user, "www-data"
set  :permission_method, :acl
set  :use_set_permissions, true
set  :use_composer, true
set  :dump_assetic_assets, true

# Change ACL on the app/logs and app/cache directories
after "symfony:composer:install", "npm:install"
after "deploy", "symfony:clear_apc"
 
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
    run "#{try_sudo} sh -c 'cd #{latest_release} && #{php_bin} #{symfony_console} apc:clear --env=#{symfony_env_prod}'"
    capifony_puts_ok
  end
end
# Be more verbose by uncommenting the following line
 logger.level = Logger::MAX_LEVEL
