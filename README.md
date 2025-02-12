# Installation

1. Download the files and place the folder (unzipped) in the following directory of your installation:

   - For the default Paymenter installation: 

     `/var/www/paymenter/extensions/Others`

   - For a custom installation, use the path:

     `/yourinstallationpath/extensions/Others`

2. If you're adding a file to the `/Http/Console` directory, follow these steps:

   - Navigate to the location where you are currently working.
   - Place the file in the `/Http/Console` folder in your installation directory: 

     `/yourinstallationpath/app/Http/Console`

   Make sure the file is in the correct location so it can be loaded by the application and be ready for further development and integration.

3. Go to the admin area of Paymenter and activate the extension.

4. Run the following command in the terminal:

   ```bash
   php artisan discord:link
   ```

5. Done! If you haven't already, go to the admin area, navigate to **Settings**, then **Social Login**. Enter the **Client ID** and **Secret** of your Discord bot and save the settings.

6. Optional: The Social Login checkbox for Discord can be unchecked unless you want to use the login feature. For this extension, it does not matter whether the option is on or off.
