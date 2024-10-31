=== ProScores - Live Scores ===
Contributors: proscores
Donate Link: https://nowpayments.io/donation/proscores
Tags: scores, live, livescore, score, soccer
Requires at least: 3.5
License: GPLv2 or later
Tested up to: 6.6.1
Stable tag: 1.0.7

ProScores provides a fully customizable and responsive live scores page, free of ads and iframes. Developed by Livescores.pro

== Description ==
ProScores provides a fully customizable and responsive live scores page specifically designed for soccer enthusiasts, free of ads and iframes. Developed by [Livescores.pro](https://livescores.pro), this plugin offers real-time updates and a sleek, modern design tailored to soccer fans and websites. Whether you're managing a soccer blog, a fan site, or a professional sports news platform, ProScores integrates seamlessly with your WordPress site, delivering an unparalleled user experience.

With ProScores, you can customize every aspect of the live soccer scores page to match your website's theme and branding. Choose from a variety of color schemes, fonts, and layout options to create a look that is uniquely yours. The plugin's intuitive settings panel makes it easy to adjust configurations without any coding knowledge.

== Features ==

* Live Football(Soccer) Scores
* Scores for the leagues you want to display, such as EPL, Serie A, La Liga
* Live Match Tracking in text and animated formats
* Responsive design
* Ad-free experience
* Fully customizable
* Support SSL (HTTPS)
* Automatic Time Zone


== Displaying Live Score ==

Use the `[proscores]` shortcode where you want to display the live scores. You can also use the `list` attribute to change the match list. For example: `[proscores list="live"]`.

For the `list` attribute, you may use the following values: `today`, `live`, `tomorrow`, `yesterday`. The default value is `today`.

- `today` = today’s matches
- `live` = live matches
- `yesterday` = yesterday’s matches
- `tomorrow` = tomorrow’s matches

== Displaying Standings / Tables ==

Use the `[prostandings]` shortcode where you want to display the standings. The default league is the English Premier League. You can use the `path` attribute to change the league. For example, `[prostandings path="/a/23/league/spain-la-liga/standings"]` will display the La Liga standings.

Other attributes you can use to change the view of the standings include:

- `[prostandings compact="1"]` will show only points, played games, and goal difference.
- `[prostandings logos="0"]` will hide team logos.
- `[prostandings legends="0"]` will hide promotion information.

For other leagues, visit our site at [LiveScores.pro](https://livescores.pro) and navigate to the desired league's standings page. In the address bar, you will see a URL like `https://livescores.pro/a/50/league/england-premier-league/standings`. Copy the path part, which in this example is `/a/50/league/england-premier-league/standings`, and use it in the shortcode.


== Supported Languages ==
* English
* Español 
* Deutsch
* Italiano
* Português
* Português/Brasil
* Русский
* Türkçe
* Polski
* Română
* Ελληνικά
* Svenska
* Srpski
* Српски
* Swahili
* हिंदी

== Screenshots ==

1. Livescore (Mobile)
2. Match Details
3. ProScores Settings
4. Standings

== Installation ==

1. Upload the `proscores` folder to the `/wp-content/plugins/` directory
2. Activate the 'ProScores - Live Scores' through the 'Plugins' menu in WordPress
3. Configure the plugin from Settings > ProScores in your admin menu
4. Use `[proscores]` shortcode where you want to display scores

== Frequently Asked Questions ==

= How can I display scores? =
Use the `[proscores]` shortcode wherever you want.

= How can I display just in-play scores? =
Use the shortcode with the list attribute valued as `live`, i.e., `[proscores list="live"]`. You can also use `today`, `tomorrow`, and `yesterday` values.

= Can I display scores just for a league? =
Yes, you can. Here are some examples of the usage shortcode:

* England - Premier League: `[proscores list="league" path="/a/50/league/england-premier-league/"]`
* Spain - La Liga: `[proscores list="league" path="/a/23/league/spain-la-liga/"]`
* Italy - Serie A: `[proscores list="league" path="/a/71/league/italy-serie-a/"]`
* Germany - Bundesliga: `[proscores list="league" path="/a/78/league/germany-bundesliga/"]`
* France - Ligue 1: `[proscores list="league" path="/a/331/league/france-ligue-1/"]`
* UEFA - Champions League: `[proscores list="league" path="/a/529/league/europe-uefa-european-championship/"]`
* UEFA - Europa League: `[proscores list="league" path="/a/307/league/europe-uefa-europa-league/"]`

For other leagues, visit our site at [LiveScores.pro](https://livescores.pro) and navigate to the desired league page. In the address bar, you will see a URL like `https://livescores.pro/a/50/league/england-premier-league/`. Copy the path part, which in this example is `/a/50/league/england-premier-league/`, and use it in the shortcode.

= Is it free? =
Yes, it’s free. But it also has premium features. ProScores offers live match tracking in both text and animated formats. The animated live match tracker is a premium feature, which requires purchasing a token for access.

== External Service Usage ==
ProScores Plugin utilizes an external service provided by [ProScores Widgets](https://widgets.proscores.app/) to enhance live score tracking functionality. The service is used to fetch live scores and updates based on user preferences.

**Data Usage:**
- The plugin sends requests to ProScores Widgets API endpoints for live score data.
- No user-specific data is transmitted; only general preferences (such as language settings) are sent.

**Terms of Use and Privacy Policy:**
- For more information, please refer to ProScores Widgets' [Terms of Use](https://widgets.proscores.app/wordpress/terms) and [Privacy Policy](https://widgets.proscores.app/wordpress/privacy).

For any questions or concerns regarding the use of this service, please contact us at widgets@proscores.app

== Upgrade Notice ==
1st version released.

== Changelog ==
1st version released.