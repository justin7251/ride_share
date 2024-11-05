# Use the official Node.js image
FROM node:latest

# Set the working directory
WORKDIR /app

# Copy package files and install dependencies using npm
COPY package.json package-lock.json ./
RUN npm install

# Copy the rest of the application code
COPY . .

# Expose port 8081
EXPOSE 8081

# Start the Vite development server
CMD ["npm", "run", "dev"]
