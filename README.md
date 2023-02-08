# Networkteam.Neos.FriendlyCaptcha

Neos CMS integration of [Friendly Captcha](https://friendlycaptcha.com).

> Organizations—from startups to large enterprises—use Friendly Captcha to protect their websites and online services
> from spam and abuse. Friendly Captcha respects your user’s privacy and works automatically, so your users don’t have
> to do anything.


## Prerequisite

In order to use the friendly captcha widget you need a `sitekey` and a `secret`. You can find/create both by logging
into your Friendly Captcha account and head to the [account page](https://app.friendlycaptcha.com/account). Click
the `Create Application button` and enter the necessary details. Additional information can be found on [Friendly
Captcha installation instructions page](https://docs.friendlycaptcha.com/#/installation).

## Configuration

Add `sitekey` and `secret` in your Flow settings.

```yaml
Networkteam:
  Neos:
    FriendlyCaptcha:
      secret: '{your secret}'
      sitekey: '{your sitekey}'
```

## Usage

This package provides a prototype for rendering the widget: `Networkteam.Neos.FriendlyCaptcha:Widget`.
The prototype is supposed to be used inside form rendering.

Include the javascript sources provided by Friendly Captcha. You can do so by either importing the library to your
frontend workflow (npm, yarn, etc.), or adding the widget script.

1. https://docs.friendlycaptcha.com/#/installation?id=_2-adding-the-widget
2. https://docs.friendlycaptcha.com/#/installation?id=adding-the-widget-itself

### Using with fluid forms

To use this package with fluid forms you have to define a form element template first.
To do so set the `renderingOptions.templatePathPattern` of the `Networkteam.Neos.FriendlyCaptcha:Form.Element.FriendlyCaptcha` to your projects template folder.

```yaml
Neos:
  Neos:
    Form:
      presets:
        default:
          formElementTypes:
            'Networkteam.Neos.FriendlyCaptcha:Form.Element.FriendlyCaptcha':
              renderingOptions:
                templatePathPattern: 'resource://Your.Package/Private/Templates/Form/{@type}.html'
              properties:
                sitekey: 'your sitekey'
                puzzleEndpoint: 'https://api.friendlycaptcha.com/api/v1/puzzle'
```

Then create the template to render the needed HTML markup.
Make sure the `data-solution-field-name` is prefixed correctly to be recognized by Neos.

```html
<f:layout name="Neos.Form:Field"/>

<f:section name="field">
    <div
        class="frc-captcha"
        data-sitekey="{element.properties.sitekey}"
        data-puzzle-endpoint="{element.properties.puzzleEndpoint}"
        data-solution-field-name="--{element.rootForm.identifier}[{element.identifier}]"
        data-lang="de"
    >
    </div>
    <span id="fr-style"></span>
</f:section>
```