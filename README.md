<h1>Moodle OTP Authentication Plugin</h1>
<h2>Overview</h2>
This Moodle plugin provides an OTP (One-Time Password) authentication method via WhatsApp, utilizing Twilio API. It enables new users to create accounts on your LMS site through a secure OTP verification process sent via WhatsApp.
<h2>Features</h2>
<ul>
	<li><b>WhatsApp OTP Verification:</b> Users can verify their accounts using OTPs sent to their WhatsApp mobile numbers.</li>
	<li><b>Twilio Integration:</b> Leverages Twilio’s API for sending OTPs.</li>
	<li><b>Secure Account Creation:</b> Enhances security by requiring OTP verification for new account registrations.</li>
	<li><b>OTP Resend Time Limit:</b> Configurable time limit to prevent users from requesting an OTP too frequently.</li>
	<li><b>OTP Expiry Time:</b> Configurable time period for how long the OTP is valid.</li>
</ul>
<h2>Requirements</h2>
<ul>
	<li><b>Moodle version:</b> 3.9 or higher.</li>
	<li><b>PHP version:</b> 7.2 or higher</li>
	<li>Twilio account with WhatsApp access.</li>
</ul>
<h2>Installation</h2>
<h3>1. Download the Plugin</h3>
Clone this repository to your Moodle auth directory:<br>
<a href="https://github.com/erudisiya/auth_twiliootp">https://github.com/erudisiya/auth_twiliootp</a><br>
<h3>2. Configure Twilio</h3>
<ul>
	<li><b>Twilio Account SID and Auth Token:</b> You’ll need these credentials from your Twilio account.</li>
	<li><b>Twilio Number:</b> Set up your Twilio number for WhatsApp.</li>
</ul>
<h3>3. Plugin Configuration</h3>
<ol>
        <li>Log in to your Moodle site as an administrator.</li>
        <li>Navigate to Site administration > Plugins > Authentication > Manage authentication.</li>
        <li>Enable the "Twilio OTP Authentication" authentication method.</li>
	<li>Configure the plugin settings by providing your Twilio Account SSID, Auth Token, and Twilio Number.</li>
</ol>
<h2>Usage</h2>
<h3>1. Account Registration:</h3>
<ul>
	<li>When a new user registers on your Moodle site, they will receive an OTP via WhatsApp.</li>
	<li>The user must enter the OTP to complete their account creation process.</li>
</ul>
<h3>2. Troubleshooting:</h3>
<ul>
	<li>Ensure Twilio credentials are correctly configured.</li>
	<li>Check Twilio’s WhatsApp configuration to ensure your number is set up correctly.</li>
</ul>
<h2>Configuration Settings</h2>
<ul>
	<li>Twilio Account SSID: Your Twilio Account SSID.</li>
	<li>Twilio Auth Token: Your Twilio Auth Token.</li>
	<li>Twilio Number: Your Twilio number is configured for WhatsApp.</li>
	<li>OTP Expiry Time: The time (in minutes) until the OTP expires.</li>
	<li>OTP Resend Time: The time (in seconds) until the OTP resend</li>
</ul>
<h2>More Documentation</h2>
More information with screenshots of this plugin can be found on the <a href="https://erudisiya.com/twiliootp-moodle-plugin-authentication/">Twilio OTP</a>.
<h2>License</h2>
Code is under the <a href="https://github.com/erudisiya/auth_twiliootp/edit/main/LICENSE">GNU GENERAL PUBLIC LICENSE v3</a>.
