# My CV

Projet Symfony 7 pour la gestion et l'affichage d'un CV en ligne.

## 🚀 Prérequis

- [Docker](https://docs.docker.com/get-docker/) & [Docker Compose](https://docs.docker.com/compose/install/)
- [Make](https://www.gnu.org/software/make/)

## 🛠️ Installation & Démarrage

1.  **Cloner le dépôt :**
    ```bash
    git clone <repository-url>
    cd my_cv
    ```

2.  **Lancer l'environnement Docker :**
    ```bash
    make up
    ```

3.  **Installer les dépendances PHP :**
    ```bash
    make composer
    ```

4.  **Initialiser la base de données :**
    ```bash
    make migrate
    ```

L'application est maintenant accessible (généralement sur [http://localhost](http://localhost)).

## 📖 Commandes utiles (Makefile)

Le projet utilise un `Makefile` pour simplifier les tâches courantes. Tapez `make help` pour voir toutes les commandes.

### Docker
- `make up` : Build et démarre les services.
- `make down` : Arrête et supprime les conteneurs.
- `make logs` : Affiche les logs en temps réel.
- `make php` : Ouvre un shell dans le conteneur PHP.

### Symfony & Doctrine
- `make c` : Ouvre la console Symfony (`bin/console`).
- `make migrate` : Exécute les migrations Doctrine.
- `make diff` : Génère une nouvelle migration suite à un changement d'entité.
- `make db-reset` : Réinitialise complètement la base de données (**DANGER : DEV uniquement**).
- `make fixtures` : Charge les jeux de données (si disponibles).
- `make cache-clear` : Vide le cache Symfony.

### Qualité & Tests
- `make qa` : Lance tous les outils de qualité (CS-Fixer, PHPStan, PHPUnit).
- `make cs` : Vérifie le style de code (dry-run).
- `make cs-fix` : Corrige automatiquement le style de code.
- `make stan` : Lance l'analyse statique avec PHPStan.
- `make test` : Lance les tests unitaires et fonctionnels avec PHPUnit.

### Divers
- `make consume` : Lance le worker pour traiter les messages Messenger (async).

## 🏗️ Architecture Technique

- **Backend** : Symfony 7.3+ (PHP 8.2+)
- **Base de données** : PostgreSQL (configuré via Docker)
- **Services Docker** :
  - `php` : PHP-FPM
  - `nginx` : Serveur web
  - `db` : Base de données
  - `redis` : Cache / Broker Messenger
  - `rabbitmq` : Broker Messenger (si utilisé)
  - `mailhog` : Capture des emails en développement

## 🚢 Déploiement

Le déploiement en production est automatisé via :
```bash
make deploy-prod
```
Cette commande effectue un `git pull`, build les images de production, lance les migrations et préchauffe le cache.

---
*Projet généré et maintenu avec Symfony 7.*
