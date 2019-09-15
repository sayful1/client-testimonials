=== Client Testimonials ===
Contributors: 		sayful
Tags:  				client-testimonials, testimonials, widget
Requires at least: 	5.0
Tested up to: 		5.2
Stable tag: 		3.1.0
License: 			GPLv2 or later
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
Donate link: 		https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3LZWQTHEVYWCY

Testimonials is a WordPress plugin that allows you to manage and display testimonials for your WordPress site.

== Description ==

Testimonials is a WordPress plugin that allows you to manage and display testimonials for your blog, product or service.

= Usage =

After installing and activating "Client Testimonials", go to `Admin Dashboard -> Testimonials` and create testimonials like creating a post.
You can add "Client's Name", "Business/Site Name", "Business/Site Link" and "Featured Image" for client avatar

After creating testimonials, go to post or page where you want add testimonials and write following shortcode.
`[client-testimonials]`

The shortcode can include following shortcode attribute.

| Attribute     | Default   | Description                                                               |
|---------------|-----------|---------------------------------------------------------------------------|
| `tablet`      | `1`       | Number of items to show when screen size (greater than/equal to) 769px.   |
| `desktop`     | `1`       | Number of items to show when screen size (greater than/equal to) 1024px.  |
| `widescreen`  | `1`       | Number of items to show when screen size (greater than/equal to) 1200px.  |
| `fullhd`      | `1`       | Number of items to show when screen size (greater than/equal to) 1400px.  |
| `autoplay`    | `no`      | Value can be `yes` or `no`. Set `yes` to play slider automatically.       |
| `loop`        | `yes`     | Value can be `yes` or `no`. Set `yes` to loop slider items.               |
| `nav`         | `yes`     | Value can be `yes` or `no`. Set `yes` to show slider navigation.          |
| `limit`       | `10`      | Maximum items can be show in a slider.                                    |

= Widget Usage =

<ul>
	<li>On Admin Dashboard, go to <strong>Appearance -> Widgets</strong></li>
	<li>Find <strong>Client Testimonials</strong> and click on it.</li>
	<li>Select at which widget area yor want to show it. and click <strong>Add Widget</strong></li>
	<li>Give widget title and write number of testimonials you want to show and click <strong>Save</strong></li>
</ul>


== Installation ==

Installing the plugins is just like installing other WordPress plugins. If you don't know how to install plugins, please review the three options below:

Install by Search

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Search for 'Client Testimonials' a plugin will come called 'Client Testimonials by Sayful Islam' and Click 'Install Now' and confirm your installation by clicking 'ok'
* The plugin will download and install. Just click 'Activate Plugin' to activate it.

Install by ZIP File

* From your WordPress dashboard, choose 'Add New' under the 'Plugins' category.
* Select 'Upload' from the set of links at the top of the page (the second link)
* From here, browse for the zip file included in your plugin titled 'client-testimonials.zip' and click the 'Install Now' button
* Once installation is complete, activate the plugin to enable its features.

Install by FTP

* Find the directory titles 'client-testimonials' and upload it and all files within to the plugins directory of your WordPress install (WORDPRESS-DIRECTORY/wp-content/plugins/) [e.g. www.yourdomain.com/wp-content/plugins/]
* From your WordPress dashboard, choose 'Installed Plugins' option under the 'Plugins' category
* Locate the newly added plugin and click on the \'Activate\' link to enable its features.


== Frequently Asked Questions ==
Do you have questions or issues with Client Testimonials? [Ask for support here](http://wordpress.org/support/plugin/client-testimonials)

== Screenshots ==

1. Client Testimonials Slide with one item
2. Client Testimonials Slide with three items
3. Client Testimonials Widget display
4. Client Testimonials with Page Builder by SiteOrigin
5. Client Testimonials TinyMce button
6. Client Testimonials TinyMce button popup

== Changelog ==

= version 3.1.0 - 2019-09-15 =
* Update core code.
* Add testimonials REST endpoint.
* Replace Owl carousel with Flickity carousel.
* Update Client_Testimonials_REST_Controller class with response value.
* Add Client_Testimonial_Object class for testimonials.
* Update Client_Testimonials_Shortcode class.
* Removed admin editor MCE button.

= version 3.0.0 - 2017-06-17 =
* Update core plugin
* All CSS has been merged into one file
* Owl Carousel has been updated to version v2.2.1
* *testimonial* and *testimonials-slider* has been removed
* and much more

= version 2.0.0 - 2015-05-15 =
* Added new style in slider
* Updated code with latest WordPress standard
* Added TinyMce button for better user experience
* Added Featured Image for upload client image

= version 1.0.0 =
* Initial release

== Upgrade Notice ==
Version 3.0.0 is a major update from version 2.0.0. It has been changed in style on slider and widget