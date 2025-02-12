
# Installation

1. Download the files and place the folder (unzipped) in the following directory of your installation:

   - For the default Paymenter installation: 

     `/var/www/paymenter/extensions/Others`

   - For a custom installation, use the path:

     `/yourinstallationpath/extensions/Others`

2. Go to the admin area of Paymenter and activate the extension.

3. Run the following command in the terminal:

   ```bash
   php artisan discord:link
   ```

4. Done! If you haven't already, go to the admin area, navigate to **Settings**, then **Social Login**. Enter the **Client ID** and **Secret** of your Discord bot and save the settings.

5. Optional: The Social Login checkbox for Discord can be unchecked unless you want to use the login feature. For this extension, it does not matter whether the option is on or off.

---

# Adding a PHP File to the Project

To add a PHP file to your project, simply drag and drop the file into the appropriate directory in the project structure. Depending on your installation, use one of the following paths:

- If you have the default Paymenter installation, place the file in: 

/var/www/paymenter/app/Http/Controllers

- If you have a custom installation, use the path: 

/yourinstallationpath/app/Http/Controllers

**Note:** Ensure that you place the file in the correct location so that it is properly loaded by the application and accessible for further development and integration.

---

### For Console Files

If you're adding a file to the `/Http/Console` directory, follow these steps:

- Navigate to the location where you are currently working.
- Place the file in the `/Http/Console` folder in your installation directory: 

/yourinstallationpath/app/Http/Console

Make sure the file is in the correct location so it can be loaded by the application and be ready for further development and integration.