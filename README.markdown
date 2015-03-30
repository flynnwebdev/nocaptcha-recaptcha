# noCaptcha reCAPTCHA 

## Installation
1. Get reCAPTCHA API keys from https://www.google.com/recaptcha/intro/index.html
2. Upload the 'nocaptcha-recaptcha' folder in this archive to your Symphony 'extensions' folder.
3. Enable it at System > Extensions.
4. Go to System > Preferences and enter your reCAPTCHA private/public API key pair.
5. Add the "reCAPTCHA Verification" filter rule to your Event via Blueprints > Events
6. Save the Event.
7. Add "reCAPTCHA: Public Key" Data Source to your page (or to globals).
8. Add the following line to your form: 

```HTML    
<div class="g-recaptcha" data-sitekey="{/data/recaptcha}"></div>
```
9. Add the following line to head, or the page you want to use reCaptcha on:

```HTML
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
```

Get developer documentation here: https://developers.google.com/recaptcha/
