# 🐳 Guide Docker - PlumbPay

## 🚀 Démarrage Rapide

### Développement
```bash
# Démarrer tous les services
docker-compose up -d

# Voir les logs
docker-compose logs -f

# Arrêter
docker-compose down
```

### Production
```bash
# Démarrer en production
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d

# Arrêter
docker-compose -f docker-compose.yml -f docker-compose.prod.yml down
```

## 📋 Services Disponibles

| Service | Port | Description |
|---------|------|-------------|
| **Web** | 80 | Application Symfony |
| **Adminer** | 8080 | Gestion base de données |
| **Mailcatcher** | 1080 | Test des emails |
| **PostgreSQL** | 5432 | Base de données |

## 🔧 Commandes Utiles

```bash
# Reconstruire les images
docker-compose build

# Voir l'état des services
docker-compose ps

# Accéder au container PHP
docker-compose exec php bash

# Voir les logs d'un service
docker-compose logs php

# Nettoyer tout
docker-compose down -v --rmi all
```

## ⚙️ Variables d'Environnement

Créez un fichier `.env.local` :

```bash
# Base de données
POSTGRES_DB=plumbpay
POSTGRES_USER=symfony
POSTGRES_PASSWORD=ChangeMe

# Symfony
APP_SECRET=votre_secret_ici
APP_ENV=dev

# Email (Brevo)
BREVO_API_KEY=votre_cle_api_brevo
ENCRYPTION_KEY=votre_cle_chiffrement_32_caracteres

# Debug
XDEBUG_MODE=off
```

## 🎯 URLs d'Accès

- **Application** : http://localhost
- **Adminer** : http://localhost:8080
- **Mailcatcher** : http://localhost:1080

## 🚨 Production

Pour la production, assurez-vous de :

1. ✅ Configurer toutes les variables d'environnement
2. ✅ Utiliser des mots de passe forts
3. ✅ Activer HTTPS
4. ✅ Configurer les sauvegardes de la base de données 