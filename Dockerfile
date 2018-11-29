FROM php:7-cli

ARG FIREFOX_DOWNLOAD_URL="https://download.mozilla.org/?product=firefox-latest-ssl&os=linux64&lang=en-US"

# bzip2 - required to extract Firefox
# ca-certificates - required for wget to download over ssl
# curl - required for NPM install script
# git - required for checking out theme production branches
# gnupg - required for NPM install script
# ibdbus-glib-1-2 - required for Firefox
# libgtk-3-0 - required for Firefox
# libxt6 - required for Firefox
# software-properties-common - required for NPM install script
# wget - required to download Firefox
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install \
   bzip2 \
   ca-certificates \
   curl \
   git \
   gnupg \
   libdbus-glib-1-2 \
   libgtk-3-0 \
   libxt6 \
   software-properties-common \
   wget

RUN wget --no-verbose -O /tmp/firefox.tar.bz2 $FIREFOX_DOWNLOAD_URL && \
    tar -C /opt -xjf /tmp/firefox.tar.bz2 && \
    rm /tmp/firefox.tar.bz2 && \
    ln -fs /opt/firefox/firefox /usr/bin/firefox

# https://tecadmin.net/install-latest-nodejs-npm-on-debian/
COPY install_npm.sh /install_npm.sh
RUN chmod +x ./install_npm.sh && ./install_npm.sh
RUN apt-get -qqy --no-install-recommends install nodejs

RUN npm install -g pixelmatch

RUN git config --global user.email "nathan@simpleupdates.com"
RUN git config --global user.name "Nathan Arthur"

# Locally /app is re-mounted as a volume. In CI, a volume is not used.
COPY . /app

CMD bash

ENTRYPOINT ["/app/app.php", "/theme"]