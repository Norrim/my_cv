# Projet Symfony 8 — Guide pour Claude Code

## Contexte

Ce projet est une application **Symfony 8** exécutée dans **Docker via docker compose**.

L'environnement est utilisé depuis **WSL + PhpStorm**.

### Stack technique

* PHP 8.4
* Symfony 8
* PostgreSQL
* Redis
* RabbitMQ
* Nginx
* Mailhog
* Docker Compose
* PHPUnit
* PHPStan
* PHP-CS-Fixer

Toutes les commandes doivent être exécutées **via le Makefile**.

Ne jamais exécuter directement `php`, `composer` ou `bin/console` sur l'hôte.

---

# Architecture du projet

Structure Symfony standard :

```
src/
 ├─ Controller/
 ├─ Entity/
 ├─ Repository/
 ├─ Service/
 ├─ Message/
 ├─ MessageHandler/
 └─ Command/

config/
migrations/
tests/
```

Principes :

* Les **controllers doivent rester fins**
* La **logique métier doit être dans des services**
* Les **repositories contiennent uniquement l'accès aux données**
* Les **DTO peuvent être utilisés pour les entrées API**
* Les **Command Symfony** sont utilisées pour les tâches CLI
* Les **Messages + Handlers** pour les traitements async via Messenger

---

# Règles de développement

Toujours respecter :

* PSR-12
* Symfony Best Practices
* Typage strict PHP
* Injection de dépendances
* Code testable

Ne jamais :

* mettre de logique métier dans les controllers
* utiliser des variables globales
* modifier `.env`
* modifier les fichiers Docker sans demander

Toujours privilégier :

* services Symfony
* Value Objects
* DTO pour les entrées API complexes
* repository pattern pour l'accès aux données

---

# Commandes disponibles

Toutes les commandes passent par `make`.

## Docker

Lancer l'environnement :

```
make up
```

Stopper :

```
make down
```

Logs :

```
make logs
```

Shell dans PHP :

```
make php
```

---

# Symfony

Console Symfony :

```
make c
```

Cache :

```
make cache-clear
make cache-warmup
```

---

# Doctrine

Créer une migration :

```
make diff
```

Exécuter les migrations :

```
make migrate
```

Reset DB (DEV uniquement) :

```
make db-reset
```

---

# Tests

Tests unitaires :

```
make test
```

PHPUnit :

```
make phpunit
```

---

# Qualité de code

Vérification complète :

```
make qa
```

PHP CS Fixer :

```
make cs
make cs-fix
```

PHPStan :

```
make stan
```

---

# Messenger (RabbitMQ)

Worker async :

```
make consume
```

---

# Base de données

Shell PostgreSQL :

```
make db-shell
```

Fixtures :

```
make fixtures
```

---

# Workflow recommandé

Quand tu modifies du code :

1. proposer le code
2. expliquer brièvement les changements
3. proposer les commandes à lancer

Exemple :

```
make migrate
make cache-clear
make test
```

---

# Docker Rules

L'application tourne **dans des containers Docker**.

Ne jamais supposer que PHP tourne sur la machine hôte.

Toutes les commandes PHP doivent être exécutées via :

```
make
```

Ne jamais lancer directement :

```
php
composer
bin/console
```

---

# Sécurité

Ne jamais :

* supprimer la base de données
* modifier les migrations existantes
* supprimer des fichiers critiques
* modifier docker-compose sans validation

---

# Bonnes pratiques Symfony

Préférer :

* Autowiring
* Attributs PHP
* DTO pour les entrées API
* Event / Messenger pour l'asynchrone

Éviter :

* les services statiques
* les singletons manuels
* la logique métier dans les controllers

---

# Format des réponses attendu

Quand tu proposes du code :

1. donner **le fichier complet**
2. expliquer brièvement
3. donner les commandes `make` à exécuter

---

# Objectif

Produire un code :

* propre
* maintenable
* compatible Symfony 8
* prêt pour la production
