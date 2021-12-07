# Intranet LMECO Production
Création d'un nouvel intranet pour LMECO Production

## Environnement requis
   
* PHP >= 7.1
* Apache 2.4
* MySql >= 5.7
* Composer
* Git

## Installation
* Cloner le repository: <code>git clone https://gitlab.influcom.fr/lmeco/intranet-web.git</code>
* Installer composer : <code>composer install</code>
* Changer la BDD dans le fichier <code>.env</code> à la racine
* Création des tables : <code>php bin/console doctrine:schema:update --force</code>
* Exécuter le sql en décochant la case *Activer la vérification des clés étrangères*:
```sql
TRUNCATE `arrival`;
TRUNCATE `article`;
TRUNCATE `customer`;
TRUNCATE `dealer`;
TRUNCATE `feature_sub_product_type`;
TRUNCATE `files`;
TRUNCATE `messaging`;
TRUNCATE `messaging_files`;
TRUNCATE `production`;
TRUNCATE `production_feature_sub_product_type`;
TRUNCATE `production_history`;
TRUNCATE `product_type`;
TRUNCATE `sav`;
TRUNCATE `sav_files`;
TRUNCATE `sav_history`;
TRUNCATE `sav_nature_setting`;
TRUNCATE `sub_product_type`;
TRUNCATE `sub_product_type_feature_sub_product_type`;
TRUNCATE `suppliers`;
```
* Changer la BDD dans le fichier <code>services.yaml</code>
* Importer la BDD de l'intranet_v1
* Commenter les lignes suivantes dans les entités Production et Sav:
```php
/**
 * @ORM\PrePersist
 * @ORM\PreUpdate
 */
public function prePersist(): void
{
    $this->updatedAt = \DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
}
```
* Vérifier la présence des fichiers :
    * arrivals.csv
    * asg.csv
    * dealers.csv
    * features.csv
    * suppliers.csv
* Vérifier qu'une garantie soit disponible et marquée comme 'par défaut'

## Importation des données
Import des données de la v1 vers la v2 (à faire dans l'ordre) :
* Import des Suppliers : <code>php bin/console app:import:suppliers</code>
* Import des Arrivals : <code>php bin/console app:import:arrivals</code>
* Import des Features : <code>php bin/console app:import:features</code>
* Import des Product Type : <code>php bin/console app:update:product:types</code>
* Import des SubProduct Type : <code>php bin/console app:update:subproduct:types</code>
* Import des Articles : <code>php bin/console app:import:articles</code>
* Update des Articles depuis LMECO : <code>php bin/console app:update:articles</code>
* Import des Productions : <code>php bin/console app:import:productions</code>
* Import des Historiques de Productions : <code>php bin/console app:import:productionhistory</code>
* Import des Dealers : <code>php bin/console app:import:dealers</code>
* Import des Customers : <code>php bin/console app:import:customers</code>
* Import des Savs : <code>php bin/console app:import:savs</code>
* Import des Natures des Savs : <code>php bin/console app:import:savnature</code>
* Import des Historiques des Savs : <code>php bin/console app:import:savhistory</code>

Ne pas oublier de décommenter les lignes dans les entités Production et Sav
## Développez et amusez-vous !