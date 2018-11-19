FROM php:cli

ARG FIREFOX_DOWNLOAD_URL="https://download.mozilla.org/?product=firefox-latest-ssl&os=linux64&lang=en-US"

# bzip2 - required to extract Firefox
# ca-certificates - required for wget to download over ssl
# ibdbus-glib-1-2 - required for Firefox
# libgtk-3-0 - required for Firefox
# libxt6 - required for Firefox
# wget - required to download Firefox
RUN apt-get update -qqy \
  && apt-get -qqy --no-install-recommends install \
   bzip2 \
   ca-certificates \
   libdbus-glib-1-2 \
   libgtk-3-0 \
   libxt6 \
   wget

RUN wget --no-verbose -O /tmp/firefox.tar.bz2 $FIREFOX_DOWNLOAD_URL && \
    tar -C /opt -xjf /tmp/firefox.tar.bz2 && \
    rm /tmp/firefox.tar.bz2 && \
    ln -fs /opt/firefox/firefox /usr/bin/firefox

COPY . /app/

RUN chmod +x /app/app.php

CMD bash

ENTRYPOINT ["/app/app.php", "/theme"]