Description
===

Ce plugin permet d'afficher un digicode logiciel pour piloter une alarme.

![widget](../images/widget.png)

Il inclut les fonctionnalités suivantes :

- Timer (configurable) avant activation de l'alarme
- Test des ouvrants avant activation
- Test de l'état de l'alarme avant activation
- Message sur état
- Blocage de la fonction retour du navigateur

Installation
===

### Pré-requis
Pour utiliser ce plugin il faut disposer des commandes suivantes :
- Etat de l'alarme de l'alarme
- Activation Totale de l'alarme
- Activation Partielle
- Etat des portes
- Etat des fenêtres

Configuration
===

## Configuration générale du plugin

Il n'y a aucune configuration générale pour ce plugin.

## Installation
Télécharger le plugin depuis le [Market](https://www.jeedom.com/market/index.php?v=d&p=market&type=plugin&&name=digicode) Jeedom

## Configuration
Une fois le plugin installé, il faut aller dans le menu Plugin -> Sécurité -> Digicode Plugin et créer un équipement.

Il faut ensuite donner les informations de votre alarme.

![equipement](../images/equipement.png)

- 1 : La commande qui fournie le statut de l'alarme
- 2 : La commande qui active le mode Total
- 3 : La commande qui active le mode Partiel
- 4 : La commande qui déactive l'alarme
- 5 et 6 : Les commandes qui fournie le statut des ouvrants (virtuel généré par le résumé domotique)
- 7 : Délais d'activation
8 - Inversion de l'état des ouvrants

### Création des utilisateurs
La création, la modification et la suppression des utilisateurs se fait directement depuis le widget en cliquant sur la roue crantée qui se situe en bas à droite.
Le code utilisateur est basé sur 4 chiffres
Le code maitre est basé sur 5 chiffres.

![configuration](../images/configuration.png)

NB : cette icone est inaccessible lorsque l'arlame est activée.

Après avoir cliqué sur l'icone, une fenêtre s'ouvre et permet la gestion des comptes.

![utilisateurs](../images/utilisateurs.png)

### Utilisation

Pour activer ou désactiver l'alarme, il suffit de taper un code utilisateur (4 chiffres) + la lettre correspondante au mode d'alarme
Pour activer ou désactiver le code maitre, il suffit de taper un code maitre (5 chiffres)

Changelog
===
### Version 1.0 (version market 2018-08-19 22:01:25)
- Verion initiale

### Version 1.1 (version market 2018-10-07 22:01:25)
- Ajout transparence sur design
- Ajout code maitre pour vérouiller la configuration du widget
- Ajout possibilité d'inverser l'état des ouvrants
