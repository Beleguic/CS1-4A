# Plumbpay

<hr>

## Members 

1. [BELGUIC Thibault - @Belguic](https://github.com/Beleguic)
2. [HAYEK Jean-Paul - @jphayek](https://github.com/jphayek)
3. [PHANG Willy - @PHANGWilly](https://github.com/PHANGWilly)
4. [YVARS Clément - @clement-Yvars](https://github.com/clement-Yvars)

<hr>

## Contributions

1. [BELGUIC Thibault - @Belguic](https://github.com/Beleguic)
   - Devis
   - Facture 
   - Mail/PDF Facture
   - Amélioration statistiques
   - Séparation des entités (Produit, Categorie, Devis, Facture, Client) vis a vis de l'entreprise
   
   
2. [HAYEK Jean-Paul - @jphayek](https://github.com/jphayek)
   - Init Projet
   - Devis et Facture
   - Client
   - Mailing
   - Forgot Password
   - Admin Charts
   - Testing XSS 

3. [PHANG Willy - @PHANGWilly](https://github.com/PHANGWilly)
   - User
   - Company
   - Statistics
   - Role
   - BackOffice
   - Contact
   - Landing Page
   - Design
   - Legals
   
4. [YVARS Clément - @clement-Yvars](https://github.com/clement-Yvars)
   - Dashboard back
   - Dashboard front
   - Styling CRUDs
   - 404
   - Mail Devis
   - PDF Produit
   - PDF Devis
   - Interview / Personna
   - Figma
   
<hr>

## Links

- [Production](http://143.110.161.74/)
- [Figma](https://www.figma.com/file/4QC1nmwVNMRul5n9iwwCee/CS1-41?type=design&node-id=0%3A1&mode=design&t=1xb771ey1jM3YNur-1)
- [Interview](https://github.com/Beleguic/CS1-4A/blob/main/INTERVIEW_DULIPECC.pdf)
- [Personnas](https://github.com/Beleguic/CS1-4A/blob/main/personnas.pdf)

## Sécurité

### Variables d'Environnement Requises

```bash
# Copiez ces variables dans votre fichier .env.local
APP_SECRET=your_app_secret_here_change_this_in_production
BREVO_API_KEY=your_brevo_api_key_here
ENCRYPTION_KEY=your_32_character_encryption_key_here_change_this_in_production
```

### Mesures de Sécurité Implémentées

- ✅ Protection CSRF activée
- ✅ Sessions sécurisées (HTTPS, HttpOnly, SameSite)
- ✅ Validation des données côté serveur
- ✅ Tokens de réinitialisation avec expiration
- ✅ Headers de sécurité (CSP, HSTS, etc.)
- ✅ Logs de sécurité
- ✅ Séparation des données par entreprise
- ✅ Messages d'erreur sécurisés

### Installation

1. **Cloner le repository**
   ```bash
   git clone [url-du-repo]
   cd Symfony
   ```

2. **Configurer l'environnement**
   ```bash
   # Copier le fichier d'environnement
   cp .env .env.local
   
   # Éditer .env.local avec vos valeurs
   ```

3. **Démarrer avec Docker**
   ```bash
   # Développement
   docker-compose up -d
   
   # Production
   docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
   ```

4. **Installer les dépendances et configurer la base**
   ```bash
   # Accéder au container PHP
   docker-compose exec php bash
   
   # Installer les dépendances
   composer install
   
   # Créer la base de données
   php bin/console doctrine:database:create
   
   # Exécuter les migrations
   php bin/console doctrine:migrations:migrate
   
   # Charger les données de test
   php bin/console doctrine:fixtures:load
   ```

5. **Accéder à l'application**
   - **Web** : http://localhost
   - **Adminer** : http://localhost:8080
   - **Mailcatcher** : http://localhost:1080

📖 **Voir [DOCKER.md](DOCKER.md) pour plus de détails**
