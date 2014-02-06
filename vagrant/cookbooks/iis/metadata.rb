name             "iis"
maintainer       "Opscode, Inc."
maintainer_email "cookbooks@opscode.com"
license          "Apache 2.0"
description      "Installs/Configures Microsoft Internet Information Services"
long_description IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version          "2.0.0"
supports         "windows"
depends          "windows", ">= 1.2.6"
