# web-ftp-file-manager

## Installation

### Dev install

```
# clone the repository
git clone git@github.com:digital-canvas/web-ftp-file-manager.git
cd web-ftp-manager
# run the installation script
./install.sh
# optionally install front-end build tools (you could run `npm install` instead)
yarn
```

### Production install

 - Run `build.sh` from a dev install.  
 - This will create a distribution build in `./dist`  
 - Upload and extract the tarball to your server webroot.  
 - Edit `system/configs/config.local.php` to set the ftp server name.  
 - Make sure the `system/storage` directory is writable.
