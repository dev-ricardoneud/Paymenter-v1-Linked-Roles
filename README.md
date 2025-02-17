# Paymenter v1 - Discord linked roles

## What is this?

This extension allows you to give your customers a role after they have paid for a product. This is done by linking their Discord account to their account on your website. This extension is made for [Paymenter](https://paymenter.org).

Screenshots:

User in chat: 

![image](https://github.com/CorwinDev/paymenter-discordlink/assets/88144943/9506ea21-b474-4906-bf55-5dc8010eeb77)

Linking Paymenter -> Discord

(To grant a user the role, they must click on "Linked Roles" from the server's context menu in Discord.)

![image](https://github.com/CorwinDev/paymenter-discordlink/assets/88144943/db85fd2b-bd5a-483f-8b69-cace48da967d)

## How to install

1. Download the files and add them into the following directory of your installation:

   - For the default Paymenter installation: 

     `/var/www/paymenter/extensions/Others/DiscordLinkedRoles`

   - For a custom installation, use the path:

     `/yourinstallationpath/extensions/Others/DiscordLinkedRoles`

**2.** Go to https://discord.com/developers/applications/ and create a new application.

**3.** Access the newly created application, go to the Bot tab, and create a new bot. Copy the received token and save it in a secure place.

**4.** Navigate to the OAuth2 tab and add a new "Redirect" with the value `https://yourDashboardDomainHere.example/youcansetthisperbot`.

**5.** After confirming the changes, open the General Information tab, scroll down, and paste `https://yourDashboardDomainHere.example/youcansetthisperbot` into the **LINKED ROLES VERIFICATION URL** field.

**6.** Add your bot to your server using the Url Generator from the OAuth2 tab.

**7.** Create a role on your server and go to the "Links" section to add a new requirement. You will see a list of social platforms followed by Your Application. Choose Your Application and select the requirements you prefer.

![image](https://github.com/CorwinDev/paymenter-discordlink/assets/41286754/56ed2f84-ab0d-4672-b0dc-b5b627618727)


To display a badge next to a person's nickname as shown in the screenshot below, you need to add the role in the permissions of the chosen Discord channel.

![image](https://github.com/CorwinDev/paymenter-discordlink/assets/88144943/9506ea21-b474-4906-bf55-5dc8010eeb77)

To grant a user the role, they must click on "Linked Roles" from the server's context menu in Discord.

![image](https://github.com/CorwinDev/paymenter-discordlink/assets/41286754/663a7e2f-1c2a-4247-899e-5f6031696a14)


**8.** Done!

**9.** Leave a star on the GitHub repository if you like this extension!
