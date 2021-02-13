## About

The app makes it easy to add [PullPreview](https://pullpreview.com) links into [Trello](https://trello.com) cards.

## Installation

The installation is very easy and straightforward:

  * Create a `.env` file with your settings.
  * Run `composer install` to install the dependencies.

```console
$ cp .env.default .env
$ composer install
```

## Trello Board

Edit your Trello board to add a custom text field to cards called `PullPreview`.

## Trello Cards

Create Trello cards that start with the name of your GitHub branch, for example a branch named `DEV-392` will need
a Trello card starting with `DEV-392: ...`.

## GitHub

### Workflow - PullPreview

In your GitHub workflow for PullPreview (`pullpreview.yml`) add the following at the end of the workflow:

```
- uses: distributhor/workflow-webhook@v1
  env:
    webhook_url: ${{ secrets.PULLPREVIEW_TRELLO_WH_URL }}
    webhook_secret: ${{ secrets.PULLPREVIEW_TRELLO_WH_SECRET }}
    data: '{ "pullpreview_url": "${{ steps.pullpreview.outputs.url }}" }'
```

### Secrets

In your project GitHub Secrets add the following entries:

* `PULLPREVIEW_TRELLO_WH_URL` - The URL to this webhook
* `PULLPREVIEW_TRELLO_WH_SECRET` - The webhook shared secret

## Overwrites

Besides the environment variables listed in the sample, you may add the following:

* `TRELLO_PULLPREVIEW_CUSTOM_FIELD` - The name of the custom field in case it is not `PullPreview`
* `GITHUB_PULLPREVIEW_WORKFLOW` - The name of the GitHub Workflow in case it not `PullPreview`

## Development & Testing

To verify the integrity of the codebase you can run the PHP linter:

```console
$ composer install
$ composer phpcs
```

## Collaboration

The GitHub repository is used to keep track of all the bugs and feature
requests; I prefer to work exclusively via GitHub and Twitter.

If you have a patch to contribute:

  * Fork this repository on GitHub.
  * Create a feature branch for your set of patches.
  * Commit your changes to Git and push them to GitHub.
  * Submit a pull request.

Shout to [@GeertHauwaerts](https://twitter.com/GeertHauwaerts) on Twitter at
any time :)

## Donations

If you like this project and you want to support the development, please consider to [donate](https://commerce.coinbase.com/checkout/45c6916d-19ae-40c9-8ef7-7fb7ad30f8e2); all donations are greatly appreciated.

* **[Coinbase Commerce](https://commerce.coinbase.com/checkout/45c6916d-19ae-40c9-8ef7-7fb7ad30f8e2)**: *BTC, BCH, DAI, ETH, LTC, USDC*
* **BTC**: *bc1q654z85zv6sujsjqk750sf4j4eahcckdtq0cqrp*
* **ETH**: *0x4d38b4EB5b0726Dc6bd5770F69348e7472954b41*
* **LTC**: *MBEaP6e4zwro6oNP54yjfC29fVqZ881wdF*
* **DOGE**: *D8LypNzP6GayEBWUKCw3KVc7gwbGBaXynT*
