# 🇫🇷 Seeder de Données Françaises pour PlumbPay

Ce document explique comment utiliser le seeder de données de test françaises pour l'application PlumbPay.

## 📋 Prérequis

Avant d'utiliser le seeder, vous devez configurer les variables d'environnement suivantes.

### Variables d'Environnement

Créez un fichier `.env.local` à la racine du projet avec le contenu suivant :

```bash
# Variables d'environnement pour le seeder français
SUPER_ADMIN_EMAIL=
GENERIC_PASSWORD=
```

**Personnalisez ces valeurs selon vos besoins :**
- `SUPER_ADMIN_EMAIL` : Email du super administrateur
- `GENERIC_PASSWORD` : Mot de passe de base (sera hashé différemment pour chaque utilisateur)

## 🚀 Utilisation

### Méthode 1 : Commande personnalisée (Recommandée)

```bash
php bin/console app:seed-french-data
```

Ou avec l'option force (sans confirmation) :

```bash
php bin/console app:seed-french-data --force
```

### Méthode 2 : Doctrine Fixtures directement

```bash
php bin/console doctrine:fixtures:load --group=FrenchSeeder
```

## 📊 Données Créées

Le seeder génère les données suivantes :

### 👥 Utilisateurs (37 au total)
- **1 Super Administrateur** avec l'email configuré dans `SUPER_ADMIN_EMAIL`
- **Par entreprise (3 entreprises) :**
  - 1 Administrateur (`ROLE_ADMIN`)
  - 1 Comptable (`ROLE_ACCOUNTANT`) 
  - 10 Plombiers (`ROLE_PLUMBER`)

### 🏢 Entreprises (3)
- **Plomberie Dupont SARL** (Paris)
- **Ets Martin Chauffage** (Lyon)  
- **Dubois Sanitaire & Cie** (Marseille)

### 📦 Données par Entreprise
- **6 Catégories** : Robinetterie, Chauffage, Évacuation, Sanitaire, Outillage, Isolation
- **20 Produits** de plomberie avec prix réalistes
- **25 Clients** avec adresses françaises
- **15 Devis** avec produits associés
- **10 Factures** (70% payées, 30% impayées)

## 🔐 Authentification

### Mots de Passe
Tous les utilisateurs utilisent le mot de passe défini dans `GENERIC_PASSWORD`, mais il est hashé différemment pour chaque utilisateur avec un suffixe :
- Super Admin : `{GENERIC_PASSWORD}_super`
- Administrateurs : `{GENERIC_PASSWORD}_admin{numéro}`
- Comptables : `{GENERIC_PASSWORD}_comptable{numéro}`
- Plombiers : `{GENERIC_PASSWORD}_plombier{numéro}`

### Emails
Les emails suivent le pattern : `prenom.nom@entreprise.fr`

Exemples :
- `superadmin@plumbpay.fr` (Super Admin)
- `jean.dupont@plomberiedupont.fr` (Utilisateur d'entreprise)

## 📝 Logs

Le seeder affiche des logs pour chaque entreprise créée :
```
[INFO] L'entreprise Plomberie Dupont SARL a été créée avec succès
[INFO] L'entreprise Ets Martin Chauffage a été créée avec succès  
[INFO] L'entreprise Dubois Sanitaire & Cie a été créée avec succès
[INFO] Toutes les données de test ont été créées avec succès !
```

## ⚠️ Avertissements

- **Utilisez uniquement en environnement de développement**
- Cette commande efface et recrée les données existantes
- Assurez-vous d'avoir une sauvegarde si nécessaire
- Vérifiez que votre base de données est configurée correctement

## 🛠️ Données Techniques

### Produits de Plomberie Inclus
- **Robinetterie** : Mitigeurs, robinets, thermostatiques
- **Chauffage** : Radiateurs, chaudières, pompes à chaleur  
- **Évacuation** : Tubes PVC, regards, siphons
- **Sanitaire** : WC, lavabos, receveurs de douche
- **Outillage** : Postes à souder, coupe-tubes, pinces
- **Isolation** : Mousse PU, gaines isolantes, coquilles

### Facturation
- Prix en centimes dans la base de données
- TVA à 20% sur tous les produits
- Numérotation automatique des devis et factures
- Réductions aléatoires entre 0 et 10%

## 🐛 Dépannage

### Erreur de connexion base de données
Vérifiez votre configuration dans `.env.local` :
```bash
DATABASE_URL="mysql://user:password@127.0.0.1:3306/plumbpay"
```

### Erreur de variables d'environnement
Assurez-vous que `SUPER_ADMIN_EMAIL` et `GENERIC_PASSWORD` sont définis dans `.env.local`

### Erreur de permissions
Vérifiez que vous avez les droits d'écriture sur la base de données.

---

**Bon développement ! 🔧💧** 