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

This package provides two prototypes for rendering the widget:

- `Networkteam.Neos.FriendlyCaptcha:Widget`
- `Networkteam.Neos.FriendlyCaptcha:FusionForm.Widget`

The prototypes are supposed to be used inside form rendering.

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

### Using with Fusion Form

Adding the captcha to a [Fusion Form](https://github.com/neos/fusion-form) is done by using the FusionForm.Widget
prototype `Networkteam.Neos.FriendlyCaptcha:FustionForm.Widget` inside form content. Secondly, you have to add the
captcha field to the schema configuration.

**Hint:** Make sure, that you have integrated the Friendly Captcha Javascript. Otherwise, the captcha won't work.

Example:

```fusion
prototype(Your.Package:AFusionFormElement) < prototype(Neos.Neos:ContentComponent) {

    renderer = Neos.Fusion.Form:Runtime.RuntimeForm {
        namespace = 'my-contact-form'
        
        process {
            schema {
                name = ${Form.Schema.string().isRequired()}
                email = ${Form.Schema.string().isRequired().validator('EmailAddress')}
                captcha = ${Form.Schema.string().validator('Networkteam.Neos.FriendlyCaptcha:CaptchaSolution')}            
            }
            
            content = Neos.Fusion:Component {
                renderer = afx`
                    <Neos.Fusion.Form:FieldContainer field.name="name">
                        <Neos.Fusion.Form:Input />
                    </Neos.Fusion.Form:FieldContainer>
                    
                    <Neos.Fusion.Form:FieldContainer field.name="email">
                        <Neos.Fusion.Form:Input
                            attributes.type="email"
                            attributes.autocomplete="email"
                        />
                    </Neos.Fusion.Form:FieldContainer>
                    
                    <Neos.Fusion.Form:FieldContainer field.name="captcha">
                        <Networkteam.Neos.FriendlyCaptcha:FustionForm.Widget />
                    </Neos.Fusion.Form:FieldContainer>
                `
            }
        }
    }
}
```
