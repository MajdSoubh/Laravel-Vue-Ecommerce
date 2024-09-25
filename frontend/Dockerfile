FROM node:18.20-alpine3.20
RUN addgroup app && adduser -S -G app app
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN chown -R app:app /app
USER app
EXPOSE 5050
CMD ["npm","run","dev"]
