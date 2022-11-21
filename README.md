# YoloCMS
Yet another simple static siteweb genetor

YoloCMS is a _personnal_ project. I made it public just in case someone else find it usefull.

** DO NOT USE IN PRODUCTION ** or do it at your own risks.

## Motivations

First of all, I missed a flat files static site generator with a clean web administration interface. [YellowCMS](https://datenstrom.se/yellow/) should have made the trick, but...

I also need a project to dig into [Symfony](https://symfony.com/). But I prefer do not use [Twig](https://twig.symfony.com/), as I will like to try [{{ mustache }}](https://mustache.github.io/) as it is implemented in **many** programmation languages, enough to be called _agnostic_. In case it appears to be not powerfull enough, I should easily migrate to [HandleBars](https://handlebarsjs.com/), which is like Mustache on steroids.

## Test server with docker

On Unixes, you can easyly start a test server using docker or podman by running the following command in the project directory :

```
docker-compose up -d
```

Then connect to http://localhost:8181.
