#!/bin/bash
TARGET=/usr/local/bin/create-app
PHARDIR=/usr/share/antcreator
PHARPATH=$PHARDIR/antcreator.phar
LINK=/usr/local/bin/create-app

check_root() {
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit 1
fi    
}

install_files() {

    echo ""
    echo "Installing files..."

    if [ ! -f antcreator.phar ]; then
        echo "antcreator.phar is not here. Re-building phar..."
        php buildphar.php
    fi

    if [ ! -d  /usr/share/antcreator/ ]; then
        mkdir /usr/share/antcreator/
    fi

    chmod +x antcreator.phar

    
    mv antcreator.phar $PHARPATH

    echo "antcreator.phar installed to: $PHARPATH"

    if [ ! -L /usr/local/bin/create-app ]; then
        ln -s $PHARPATH $LINK
    fi

    echo "Symlink installed $LINK -> $PHARPATH"
    echo "done"
    echo ""
}

remove_files() {
    
    [ ! -L $TARGET ] && unlink $TARGET
    echo "Unlnked: $TARGET"
    rm -v $PHARPATH
    echo "Removed $PHARPATH"
    echo "Remove $PHARDIR? [y/N]"
    read CHOICE

    [ "$CHOICE" == "Y" ] && rm -vfr $PHARDIR

    echo "Uninstall complete."
}

show_help() {
    cat <<'EOF'

SUMMARY:

Installs the PHP-Ant app creator scripts.

SYNTAX:

install.sh [ install | remove ]

WHERE:
  install  Installs the scripts into /usr/local/bin via a symlink to this
           directory. If you move these files, it will break the link. So,
           You should probably extract all these files to /usr/src/ to keep
           them safe long term.

  remove   Removes the symlinks from /usr/local/bin, but leaves the source
           files in tact.

For support, or to open issues, create a Github issue.

EOF
exit 0
}

if [ $# -ne 1 ];
    then show_help
fi

case "$1" in
    install)
        check_root
        install_files
        ;;
    remove)
        check_root
        remove_files
        ;;
    help)
        show_help
        ;;
    *)
        show_help
        ;;
esac