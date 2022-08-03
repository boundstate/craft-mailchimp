# Mailchimp plugin for Craft CMS 4.x

Subscribe users to Mailchimp lists in Craft CMS

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require boundstate/craft-mailchimp

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Mailchimp.

4. In the Control Panel, go to Settings → Plugins → Mailchimp and configure the plugin.

## Usage

Your subscribe form template can look something like this:

```twig
<form method="post" action="" accept-charset="UTF-8">
    {{ csrfInput() }}
    <input type="hidden" name="action" value="mailchimp/subscribe">
    {{ redirectInput('subscribe/thanks') }}

    <h3><label for="name">Your Name</label></h3>
    <input id="name" type="text" name="mergeFields[NAME]" value="{{ subscription.mergeFields.NAME ?? '' }}" required>

    <h3><label for="email">Your Email</label></h3>
    <input id="email" type="email" name="email" value="{{ subscription.email ?? '' }}" required>
    {{ subscription.getErrors('email')|join() }}

    <input type="hidden" name="tags[]" value="Tag 1">
    <input type="hidden" name="tags[]" value="Tag 2">

    <input type="submit" value="Subscribe">
</form>
```

The only required field is `email`. Everything else is optional.

### Redirecting after submit

If you have a `redirect` hidden input, the user will get redirected to it upon successfully subscribing. The following variables can be used within the URL/path you set:

- `{email}`
- `{mergeFields}`
- `{tags}`

For example, if you wanted to redirect to a `subscribe/thanks` page and pass the user’s name to it, you could set the input like this:

```twig
{{ redirectInput('subscribe/thanks?name={mergeFields.NAME}') }}
```

In your `subscribe/thanks` template, you can access URL parameters using `craft.app.request.getQueryParam()`:

```twig
<p>Thanks for subscribing, {{ craft.app.request.getQueryParam('name') }}!</p>
```

Note that if you don’t include a `redirect` input, the current page will get reloaded.

### Flash messages & API errors

When a subscribe form is submitted, the plugin will set a `notice` or `success` flash message on the user session. You can display it in your template like this:

```twig
{% if craft.app.session.hasFlash('notice') %}
    <p class="message notice">{{ craft.app.session.getFlash('notice') }}</p>
{% elseif craft.app.session.hasFlash('error') %}
    <p class="message error">{{ craft.app.session.getFlash('error') }}</p>
{% endif %}
```

If the Mailchimp API returns an error, the plugin also sets the `subscription.apiError` variable. You can display it in your template like this:

```twig
{% if subscription is defined and subscription.apiError %}
    <h3>{{ subscription.apiError.title }}</h3>
    <p>{{ subscription.apiError.detail }}</p>
{% endif %}
```
