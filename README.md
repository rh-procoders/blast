# How to Run the Gulpfile

This document provides step-by-step instructions on how to run the **Gulp** setup for compiling SCSS files.

---

## 📌 Step 1: Install Node.js (If Not Installed)
Check if you have Node.js installed:

```sh
node -v
npm -v
```

If not installed, download and install it from [Node.js official website](https://nodejs.org/).

---

## 📌 Step 2: Install Gulp CLI Globally
If you haven’t installed Gulp globally, run:

```sh
npm install -g gulp-cli
```

---

## 📌 Step 3: Navigate to Your Project Directory
In **Terminal (Mac/Linux) or Command Prompt (Windows)**, run:

```sh
cd /Users/renatohoxha/Local Sites/blast-2025/app/public/wp-content/themes/blast-2025
```

---

## 📌 Step 4: Install Required Dependencies
Run the following command to install the necessary packages:

```sh
npm install
```

---

## 📌 Step 5: Run Gulp

### 1️⃣ Run the Build Process (Compile SCSS Once)
```sh
gulp build
```

### 2️⃣ Watch for File Changes and Auto-Compile SCSS
```sh
gulp watch
```

### 3️⃣ Run Both Build & Watch Together (Default Task)
```sh
gulp
```

---

## 📌 Step 6: Verify SCSS Compilation
- If successful, compiled CSS files will be located in:
  - `./assets/css/`
  - `./blocks/**/css/`
- If errors occur, Gulp will display them in the console.

---

## 🚀 Notes & Troubleshooting
✔ If you get `command not found: gulp`, reinstall Gulp globally:  
   ```sh
   npm install -g gulp-cli
   ```

✔ If you get dependency errors, **delete `node_modules`** and reinstall:  
   ```sh
   rm -rf node_modules package-lock.json
   npm install
   ```

✔ If SCSS doesn't compile, check file paths and ensure SCSS syntax is correct.

---

### ✅ Your SCSS files should now compile automatically when modified! 🎉

