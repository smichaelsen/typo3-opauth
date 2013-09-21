# [Opauth](http://github.com/opauth/opauth) extension for TYPO3
### Opauth implement OAuth2 Layer for TYPO3 CMS

You can see demo at: http://typo3.denysbutenko.com

#Install
### From github

```bash 
cd /path/to/your/typo3conf/ext/
git clone https://github.com/thedarki/typo3-opauth.git opauth
```

1. Open your system backend: http://localhost/typo3/index.php
2. Auth in backend.
3. Select "Extension Manager" in left bar.
4. In search-box input "opauth" without quotes.
5. Activate it
6. Click on name of extension for opening settings.

### From TER (TYPO3 Extension Repository)

In future...

#Configuration
For able auth with social network we need to configure his settings.

## Main Settings
1. Click on checkbox for enable it for **Frontend** or **Backend** or both.
2. By default creating new user in backend is disabled. For enable it click on **createAdminBeUsers**
3. For frontend user needed to select storagePid.

### Facebook:
1. Open tab with facebook settings.
2. Click on checkbox for enable it.
3. Open [https://developers.facebook.com/apps](https://developers.facebook.com/apps)
4. Create you app
5. Copy **App-Id** and paste into **App Id _[facebook.facebookAppId]_**
6. Copy **App-Secret** into **App Secret Token _[facebook.facebookAppSecret]_**
7. Click 'Save'

### Twitter:
1. Open tab with twitter settings.
2. Click on checkbox for enable it.
3. Open [https://dev.twitter.com/apps](https://dev.twitter.com/apps)
4. Click to [Create a new application](https://dev.twitter.com/apps/new)
5. Copy **Consumer key** and paste into **Consumer Key _[twitter.twitterConsumerKey]_**
6. Copy **Consumer secret** into **Consumer Secret _[twitter.twitterConsumerSecret]_**
7. Click 'Save'


#Currently Supported

* Facebook
* Twitter

** I've only tested it with Facebook. This does not mean that it won't work for other Oauth2 providers. Refer to http://opauth.org/ for help on implementing it. **
