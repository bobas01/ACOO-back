# Documentation API ACOO

## Vue d'ensemble

Cette API gère les actualités (News) et les événements (Events) avec une logique métier spéciale.

## Logique News/Events

- **Une News peut exister seule** (simple actualité)
- **Un Event est toujours lié à une News** (relation OneToMany)
- **Les champs title et content sont partagés** entre News et Event associé

## Documentation disponible

### 1. Documentation API Platform standard
- **URL** : `/api` (Swagger UI)
- **Description** : Documentation automatique générée par API Platform
- **Limitation** : Ne reflète pas la logique personnalisée de création d'événement

### 2. Documentation personnalisée
- **URL** : `/api-docs/news-events`
- **Description** : Documentation détaillée de la logique News/Events
- **Format** : JSON avec exemples complets

### 3. Documentation OpenAPI personnalisée
- **URL** : `/api-docs-custom.yaml`
- **Description** : Spécification OpenAPI 3.0 complète
- **Usage** : Importable dans Postman ou autres outils

## Création d'une News avec Event

### Méthode recommandée : POST `/news`

```json
{
    "title": "Titre de la news",
    "content": "Description de la news",
    "images": ["data:image/jpg;base64,..."],
    "id_admin": 1,
    "startDatetime": "20/06/2024 15:00",
    "endDatetime": "20/06/2024 17:00",
    "eventType": "match",
    "location": "Stade Municipal de la ville",
    "sport": 1
}
```

### Logique de création

1. **Si `startDatetime` est présent** : Crée automatiquement un Event associé
2. **Si `startDatetime` est absent** : Crée seulement une News

### Champs obligatoires
- `title` : Titre de l'actualité
- `content` : Contenu de l'actualité

### Champs optionnels pour Event
- `startDatetime` : Date/heure de début (format: "JJ/MM/AAAA HH:mm")
- `endDatetime` : Date/heure de fin (format: "JJ/MM/AAAA HH:mm")
- `eventType` : Type d'événement (défaut: "default")
- `location` : Lieu de l'événement
- `sport` : ID du sport associé

## Routes disponibles

### News
- `GET /news` - Liste toutes les actualités
- `GET /news/{id}` - Détails d'une actualité
- `POST /news` - Créer une actualité (avec ou sans événement)
- `POST /news/{id}` - Modifier une actualité
- `DELETE /news/{id}` - Supprimer une actualité

### Events
- `GET /events` - Liste tous les événements
- `GET /events/{id}` - Détails d'un événement
- `POST /events` - Créer un événement (crée aussi une news)
- `POST /events/{id}` - Modifier un événement
- `DELETE /events/{id}` - Supprimer un événement

## Format des dates

- **Entrée** : Format français "JJ/MM/AAAA HH:mm"
- **Sortie** : Format français "JJ/MM/AAAA HH:mm"

## Gestion des images

- Les images sont envoyées en base64 dans le tableau `images`
- Format attendu : `data:image/jpeg;base64,...`
- Les images sont sauvegardées dans `public/uploads/images/news/`
- L'URL de l'image est retournée dans la réponse

## Exemple de réponse

```json
{
    "id": 1,
    "title": "Titre de la news",
    "content": "Description de la news",
    "images": ["http://localhost:8000/uploads/images/news/image.jpg"],
    "event": {
        "id": 1,
        "title": "Titre de la news",
        "content": "Description de la news",
        "eventType": "match",
        "location": "Stade Municipal de la ville",
        "startDatetime": "20/06/2024 15:00",
        "endDatetime": "20/06/2024 17:00",
        "sport": {
            "id": 1,
            "name": "Football"
        }
    },
    "created_at": "20/03/2024 10:00",
    "updated_at": "20/03/2024 10:00",
    "published_at": "20/03/2024 10:00",
    "id_admin": "/admin/1"
}
```

## Cas d'usage recommandés

### Pour le front-end
- **Consulter les actualités** : Utiliser `/news`
- **Consulter les événements** : Utiliser `/events`
- **Créer une actualité simple** : POST `/news` sans champs d'événement
- **Créer une actualité avec événement** : POST `/news` avec champs d'événement

### Pour la gestion
- **Modifier un événement** : POST `/events/{id}`
- **Supprimer un événement** : DELETE `/events/{id}`
- **Consulter un événement spécifique** : GET `/events/{id}`

## Notes importantes

1. **Duplication title/content** : C'est normal car les champs sont partagés entre News et Event
2. **Routes Events séparées** : Utiles pour la gestion et la consultation
3. **Contrôleur personnalisé** : Gère la logique de création d'événement via news
4. **Documentation API Platform** : Limitée car ne reflète pas la logique personnalisée 