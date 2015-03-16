dnl $Id$
dnl config.m4 for extension HwCrypt

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(HwCrypt, for HwCrypt support,
dnl Make sure that the comment is aligned:
dnl [  --with-HwCrypt             Include HwCrypt support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(HwCrypt, whether to enable HwCrypt support,
dnl Make sure that the comment is aligned:
[  --enable-HwCrypt           Enable HwCrypt support])

if test "$PHP_HWCRYPT" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-HwCrypt -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/HwCrypt.h"  # you most likely want to change this
  dnl if test -r $PHP_HWCRYPT/$SEARCH_FOR; then # path given as parameter
  dnl   HWCRYPT_DIR=$PHP_HWCRYPT
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for HwCrypt files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       HWCRYPT_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$HWCRYPT_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the HwCrypt distribution])
  dnl fi

  dnl # --with-HwCrypt -> add include path
  dnl PHP_ADD_INCLUDE($HWCRYPT_DIR/include)

  dnl # --with-HwCrypt -> check for lib and symbol presence
  dnl LIBNAME=HwCrypt # you may want to change this
  dnl LIBSYMBOL=HwCrypt # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $HWCRYPT_DIR/lib, HWCRYPT_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_HWCRYPTLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong HwCrypt lib version or lib not found])
  dnl ],[
  dnl   -L$HWCRYPT_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(HWCRYPT_SHARED_LIBADD)

  PHP_NEW_EXTENSION(HwCrypt, HwCrypt.c, $ext_shared)
fi
