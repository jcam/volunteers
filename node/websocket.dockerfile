# Fetch node dependencies
# - generates node_modules
FROM node:8 as nodebuild
COPY package*.json /usr/src/app/
COPY websocket-server.js /usr/src/app/
COPY config.js /usr/src/app/
WORKDIR /usr/src/app
RUN npm install
CMD ["node","websocket-server.js"]
