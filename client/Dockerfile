# Dockerfile
FROM node:lts-alpine

RUN mkdir /app
WORKDIR /app
COPY package.json ./
COPY . ./
RUN npm i
COPY src src
COPY public ./

CMD npm start