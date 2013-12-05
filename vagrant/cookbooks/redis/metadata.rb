name             "redis"
maintainer       "dkd Internet Service GmbH"
maintainer_email "christian.trabold@dkd.de"
license          "Apache 2.0"
description      "Installs/Configures redis"
long_description IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version          "0.0.4"
recipe           "redis::default", "Includes the package recipe by default."
recipe           "redis::package", "Sets up a redis server."
recipe           "redis::gem", "Installs redis gem for ruby development."
recipe           "redis::source", "Builds redis server from sources."
recipe           "redis::remove", "Removes redis server and redis gem, if installed."

%w{ ubuntu debian }.each do |os|
  supports os
end
