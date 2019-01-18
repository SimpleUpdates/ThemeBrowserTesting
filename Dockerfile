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
# openssh-client - required for git to clone submodules
# software-properties-common - required for NPM install script
# unzip - required for extracting circle-github-bot
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
   openssh-client \
   software-properties-common \
   unzip \
   wget

RUN wget --no-verbose -O /tmp/firefox.tar.bz2 $FIREFOX_DOWNLOAD_URL && \
    tar -C /opt -xjf /tmp/firefox.tar.bz2 && \
    rm /tmp/firefox.tar.bz2 && \
    ln -fs /opt/firefox/firefox /usr/bin/firefox

# https://tecadmin.net/install-latest-nodejs-npm-on-debian/
COPY install_npm.sh /install_npm.sh
RUN chmod +x ./install_npm.sh && ./install_npm.sh
RUN apt-get -qqy --no-install-recommends install nodejs

# coffeescript - required for circle-github-bot compilation
# doiuse - identifies unsupported CSS properties
# pixelmatch - used to generate visual diffs
RUN npm install -g \
    coffeescript \
    doiuse \
    pixelmatch

RUN git config --global user.email "nathan@simpleupdates.com"
RUN git config --global user.name "Nathan Arthur"

# Locally /app is re-mounted as a volume. In CI, a volume is not used.
COPY . /app

ADD https://github.com/su-narthur/circle-github-bot/archive/master.zip /circle-github-bot.zip
RUN unzip /circle-github-bot.zip && \
    cd /circle-github-bot-master && \
    npm run-script build
RUN chmod +x /app/app.php && chmod +x /app/comment.js

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN cd /app && composer install

CMD bash

ENTRYPOINT ["/app/app.php", "/theme"]