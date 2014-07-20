Alerts
======

This package allows you to fluently add permanent and temporary alert messages in Laravel. The package includes a convenient function to get the HTML of all your messages in Bootstrap or Foundation markup.

## Installation

You can install this package through Composer. Add the following line to your composer.json `require` object:

```
"jcoded/alerts": "dev-master"
```

Then run composer update from the command line to install the package (you'll need to be in the same directory as composer.json):

```bash
$ composer update
```

Add the service provider to the `providers` array in `app/config/app.php`:

```
'JCoded\Alerts\AlertsServiceProvider'
```

Add the alias to the `aliases` array in `app/config/app.php`:

```
'Alert' => 'JCoded\Alerts\Facades\Alert'
```

Finally add the dismiss route to your `app/routes.php` file to enable dismissals to be 'remembered' across requests (feel free to change the URI if it conflicts with another in your application):

```php
Route::post('dismiss', 'JCoded\Alerts\AlertController@rememberDismiss');
```

You're ready to go!

## Configuration

By default this package outputs Bootstrap markup, you can change to Foundation markup by adding the configuration file to your project and amending the `template_framework` variable.

To add the configuration file run (you'll need to be in you project's root directory):

```bash
$ php artisan config:publish jcoded/alerts
```
The configuration file will be located in: `app/config/packages/jcoded/alerts/config.php`.

The configuration file also allows you to change the Session key used to store alert messages, and to add custom classes to each alert `<div>` tag.

## Usage

The package has 4 levels of alert: `success`, `info`, `warning`, `error`; they correspond to the Bootstrap and Foundation classes with the exception of the `error` level, in Bootstrap this is the `danger` class, in Foundation this is the `alert` class.

### Temporary alerts

A temporary alert is shown only in the request immediately following the alert's creation. A common use case is displaying error messages from the user's last request.

Temporary alerts are the default behaviour so all you need to do is:

```php
Alert::success('Your request was successful');
Alert::error('Something has gone wrong.');
```

### Permanent alerts

A permanent alert is available to all requests following the alert's creation until the alert is dismissed. A common use case is displaying maintenance messages, or letting a user know part of their profile is incomplete.

To add a permanent alert:

```php
Alert::add('info','Please <a href="/account/email">validate your email address</a>');
```

### Dismissible alerts

You can make any alert dismissible by appending `Dismissible` to the end of a method call or setting the third parameter to true when using the `add` method:

```php
Alert::successDismissible('Your request was successful');
Alert::add('info','We have loads of <a href="/specials">special offers today only</a>!', true);
```

### Alert links

The example above uses a link to help the user get to the correct place for the alert. If you are using Bootstrap don't forget to add the `alert-link` class to the `<a>` tag.

### Checking an alert exists

When a permanent alert is dismissed it remains in the alert messages but is marked as dismissed. You can check to see if an alert exists by using `Alert::has()`. This is useful when adding messages in multiple places in your code to avoid duplicate messages sent to the user.

```php
if( !Alert::has('info','We have some special offers on today') ) {
	Alert::add('info','We have some special offers on today');
}
```

### Displaying alerts

Displaying alerts is easy with the built in `html` function. 

To display all alerts in your template simply add:

```php
{{ Alert::html() }}
```
