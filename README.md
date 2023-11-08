# Installation et exécution du projet Symfony

## Installation

1. Cloner le repository
   `git clone https://github.com/RudyDupuis/eni_sortir-solo.git`
   `cd eni_sortir-solo`
   `composer install`

2. Configurer la base de donnée
   `cp .env .env.local`
   Changer les valeurs de User, password, url et NomBasededonnée en fonction de votre BDD
   `DATABASE_URL="mysql://User:password@url:3306/NomBasededonnée?serverVersion=8&charset=utf8mb4"`
   `symfony console doctrine:database:create`
   `symfony console doctrine:migrations:migrate`

3. Ajouter des fausses données
   `symfony console doctrine:fixtures:load`

4. Lancer le serveur
   `symfony server:start`
