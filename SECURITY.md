# Guide de Sécurité - PlumbPay

## Mesures de Sécurité Implémentées

### 1. Protection CSRF
- ✅ Protection CSRF activée globalement
- ✅ Tokens CSRF sur tous les formulaires de suppression
- ✅ Validation des tokens dans les contrôleurs

### 2. Authentification et Autorisation
- ✅ Système de rôles (ROLE_SUPER_ADMIN, ROLE_PLUMBER, ROLE_ACCOUNTANT, ROLE_USER)
- ✅ Vérification des permissions par entité
- ✅ Authentification par formulaire avec remember me
- ✅ Hachage sécurisé des mots de passe (bcrypt/argon2)

### 3. Validation des Données
- ✅ Validation des mots de passe (8+ caractères, majuscules, minuscules, chiffres, caractères spéciaux)
- ✅ Validation des emails
- ✅ Contraintes sur les entités Doctrine
- ✅ Validation côté serveur et client

### 4. Headers de Sécurité
- ✅ X-Frame-Options: SAMEORIGIN
- ✅ X-Content-Type-Options: nosniff
- ✅ X-XSS-Protection: 1; mode=block
- ✅ Referrer-Policy: strict-origin-when-cross-origin
- ✅ Permissions-Policy: geolocation=(), microphone=(), camera=()
- ✅ Strict-Transport-Security: max-age=31536000; includeSubDomains

### 5. Content Security Policy (CSP)
- ✅ CSP activé via Nelmio Security Bundle
- ✅ Scripts autorisés: self, unpkg.com, js.stripe.com
- ✅ Styles autorisés: self, fonts.googleapis.com
- ✅ Images autorisées: self, data:, https:
- ✅ Fonts autorisés: self, fonts.gstatic.com

### 6. Sessions Sécurisées
- ✅ Cookies sécurisés (HTTPS uniquement)
- ✅ SameSite: strict
- ✅ Garbage collection configuré
- ✅ Durée de vie limitée

### 7. Protection contre les Attaques
- ✅ Protection contre le clickjacking
- ✅ Protection contre le MIME sniffing
- ✅ Protection contre les injections SQL (Doctrine ORM)
- ✅ Protection contre les XSS (Twig auto-escaping)

### 8. Variables d'Environnement
- ✅ Clés Stripe externalisées
- ✅ Secrets d'application externalisés
- ✅ Configuration par environnement

### 9. Gestion des Erreurs
- ✅ Pas d'exposition d'informations sensibles dans les erreurs
- ✅ Redirection sécurisée en cas d'accès refusé
- ✅ Logs d'erreurs appropriés

### 10. Sécurité des Fichiers
- ✅ Accès aux fichiers sensibles bloqué
- ✅ Uploads sécurisés
- ✅ Noms de fichiers sécurisés pour les PDFs

## Variables d'Environnement Requises

```bash
# Symfony
APP_ENV=prod
APP_SECRET=votre_secret_fort_et_unique
APP_DOMAIN=https://votre-domaine.com

# Stripe
STRIPE_PUBLIC_KEY=pk_live_votre_cle_publique
STRIPE_SECRET_KEY=sk_live_votre_cle_secrete

# Brevo
BREVO_API_KEY=votre_cle_api_brevo

# Database
DATABASE_URL="postgresql://user:password@host:5432/database"
```

## Bonnes Pratiques de Déploiement

1. **Changer APP_SECRET** en production
2. **Utiliser HTTPS** en production
3. **Configurer les logs** de sécurité
4. **Mettre à jour régulièrement** les dépendances
5. **Sauvegarder** la base de données
6. **Monitorer** les accès et erreurs

## Tests de Sécurité

- [ ] Tests d'injection SQL
- [ ] Tests XSS
- [ ] Tests CSRF
- [ ] Tests d'authentification
- [ ] Tests d'autorisation
- [ ] Tests de validation des données

## Contact Sécurité

Pour signaler une vulnérabilité de sécurité, contactez l'équipe de développement. 