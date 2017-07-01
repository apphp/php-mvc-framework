Opauth-LinkedIn
=============
[Opauth][1] strategy for LinkedIn, implemented based on https://developer.linkedin.com/documents/authentication using OAuth 2.

Opauth is a multi-provider authentication framework for PHP.

Demo: http://opauth.org#linkedin

Getting started
----------------
1. Install Opauth-LinkedIn:
   ```bash
   cd path_to_opauth/Strategy
   git clone git://github.com/uzyn/opauth-linkedin.git LinkedIn
   ```

2. Create LinkedIn application at https://www.linkedin.com/secure/developer
   - Enter your domain at JavaScript API Domain
   - There is no need to enter OAuth Redirect URL

3. Configure Opauth-LinkedIn strategy with at least `qpi_key` and `secret_key`.

4. Direct user to `http://path_to_opauth/linkedin` to authenticate

Strategy configuration
----------------------
Required parameters:

```php
<?php
'LinkedIn' => array(
	'api_key' => 'YOUR API KEY',
	'secret_key' => 'YOUR SECRET KEY'
),
```

Note: To obtain email, include `r_emailaddress` to `scope`, eg.: `'scope' => 'r_basicprofile r_emailaddress'`.

See LinkedInStrategy.php for more optional parameters.


License
---------
Opauth-LinkedIn is MIT Licensed
Copyright © U-Zyn Chua (http://uzyn.com)

[1]: https://github.com/uzyn/opauth