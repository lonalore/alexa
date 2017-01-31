# Alexa
e107 (v2) plugin - This is an integration plugin for Amazon Echo devices, allowing e107 to respond to Alexa Skills Kit requests. Right now the plugin provides only a basic integration. Developers will need to create their own customized handler plugin to handle custom Alexa skills. [Alexa Demo plugin](https://github.com/lonalore/alexa_demo).

**Requirements**
- [e107](https://github.com/e107inc/e107) (>= v2.1.2)
- [Composer](https://getcomposer.org/)

**Before installation:**
- Need to install the dependencies for the plugin. Just run the `composer install` command from the Alexa plugin's folder.

**There are minimal prerequisites for your Amazon application:**
- Your e107 site has to be accessible online - Amazon will be sending callbacks to a URL on your site.
- It has to use SSL, only HTTPS callbacks are allowed.

**Create an Amazon application (for [Alexa demo plugin](https://github.com/lonalore/alexa_demo)):**
* Go to [https://developer.amazon.com](https://developer.amazon.com) and sign in with your Amazon account.
* Click **Apps & Services** and select **Alexa** from the menu
* Choose **Alexa Skills Kit** and add a new skill, call it **Hello Plugin**, and fill in the form:
  * For **Skill Type**, choose _Custom Interaction Model_
  * For **Invocation name**, use a short phrase of your choice such as _My Application_
  * On the **Interaction Model** tab:
    * put in **Intent Schema** from this file: [sample_intents.json](https://github.com/lonalore/alexa_demo/blob/master/sample_intents.json)
    * put in **Sample Utterances** from this file: [sample_utterances.txt](https://github.com/lonalore/alexa_demo/blob/master/sample_utterances.txt)
  * On the **Configuration** tab:
    * put in _https://your.site/alexa/endpoint_ as your **Endpoint**
    * choose _No_ for **Account linking**
  * On the **SSL Certificate** tab, choose what kind of SSL certificate is your site using
* Save the configuration
* Write down the _Application ID_ back on the original **Skill information** screen

## Support on Beerpay
Hey dude! Help me out for a couple of :beers:!

[![Beerpay](https://beerpay.io/lonalore/alexa/badge.svg?style=beer-square)](https://beerpay.io/lonalore/alexa)  [![Beerpay](https://beerpay.io/lonalore/alexa/make-wish.svg?style=flat-square)](https://beerpay.io/lonalore/alexa?focus=wish)