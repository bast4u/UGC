# Read Me

## Installer l'environnement de développement


### Installer PHP8, Composer, Symfony & MongoDB

Les étapes varient en fonction votre OS, voir:

- PHP: https://www.php.net/manual/en/install.php
- Composer: https://getcomposer.org/download/
- Symfony: https://symfony.com/doc/current/setup.html
- MongoDB: https://www.mongodb.com/docs/manual/installation/#mongodb-installation-tutorials
- MongoDB librairies for PHP: https://www.mongodb.com/docs/php-library/current/tutorial/install-php-library/


### Récupérer le code depuis le repository Github

```shell
mkdir Code
cd Code && git clone https://github.com/teamugc/projet_ugc.git
```


### Installer les dépendances

```shell
composer install
```


### Démarrer le serveur Symfony

```shell
symfony server:start
```

### Explication du projet

Le projet consiste en une application similaire à UGC, où les utilisateurs peuvent s'inscrire et choisir des préférences telles que leur emplacement dans la salle de cinéma, leur(s) style(s) de films préférés, leur(s) acteur(s) préféré(s) et leur cinéma préféré. 

Une fois inscrits, les utilisateurs auront accès à une barre de widget personnalisée lorsqu'ils se connectent, qui leur montrera les prochains films à l'affiche en fonction de leurs préférences.
 
Dans ce projet, Symfony est utilisé pour gérer la logique de l'application, la gestion des utilisateurs, la personnalisation des préférences, et pour fournir une barre de widget qui affiche les prochains films à l'affiche en fonction des choix de l'utilisateur.

L'application est basée sur Symfony avec l'utilisation de la doctrine mongoDB ODM pour gérer les données. Le projet va récupérer le titre et l'année de sortie des films disponibles chez les cinémas UGC en se connectant aux données d'allociné. Il s'agit ensuite de faire une requête à l'api TMDB pour faire une comparaison sur les titres et années de sortie des films pour, si concordance il y a, récupérer les informations supplémentaires telles que l'image, le synopsis...

 Le contrôleur gérera les requêtes HTTP et le flux d'exécution, tandis que les entités "Utilisateur" et "Film" seront représentées sous forme de Documents MongoDB. Un Repository permettra d'accéder et de manipuler facilement les données des utilisateurs et des films. Le projet vise à offrir une expérience personnalisée et conviviale aux utilisateurs en leur fournissant des recommandations de films adaptées à leurs goûts.
