# Documentation API ACOO - Guide Simple

## Base URL
```
http://localhost:8000/api
```

---

## 🔓 ROUTES PUBLIQUES (Pas d'authentification requise)

### 1. AUTHENTIFICATION

#### Connexion Admin
```diff
+ POST /admin/login
```
Content-Type: application/json

Body:
{
    "email": "admin@acoo.fr",
    "password": "motdepasse"
}

Réponse 200:
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "admin": {
        "id": 1,
        "name": "Admin",
        "email": "admin@acoo.fr"
    }
}

#### Demande de réinitialisation de mot de passe
```diff
+ POST /admin/forgot-password
```
Content-Type: application/json

Body:
{
    "email": "admin@acoo.fr"
}

Réponse 200:
{
    "message": "Email de réinitialisation envoyé"
}

#### Réinitialisation de mot de passe
```diff
+ POST /admin/reset-password
```
Content-Type: application/json

Body:
{
    "token": "token_de_reset",
    "password": "nouveau_mot_de_passe"
}

Réponse 200:
{
    "message": "Mot de passe mis à jour avec succès"
}

### 2. CONSULTATION DES DONNÉES

#### Liste des actualités
```diff
+ GET /news
```

Réponse 200:
[
    {
        "id": 1,
        "title": "Titre de l'actualité",
        "content": "Contenu de l'actualité",
        "images": ["http://localhost:8000/uploads/images/news/image.jpg"],
        "event": {
            "id": 1,
            "startDatetime": "20/06/2024 15:00",
            "endDatetime": "20/06/2024 17:00",
            "eventType": "match",
            "location": "Stade Municipal",
            "sport": {
                "id": 1,
                "name": "Football"
            }
        },
        "created_at": "20/03/2024 10:00"
    }
]

#### Détail d'une actualité
```diff
+ GET /news/{id}
```

Réponse 200:
{
    "id": 1,
    "title": "Titre de l'actualité",
    "content": "Contenu de l'actualité",
    "images": ["http://localhost:8000/uploads/images/news/image.jpg"],
    "event": {
        "id": 1,
        "startDatetime": "20/06/2024 15:00",
        "endDatetime": "20/06/2024 17:00",
        "eventType": "match",
        "location": "Stade Municipal",
        "sport": {
            "id": 1,
            "name": "Football"
        }
    },
    "created_at": "20/03/2024 10:00"
}

#### Liste des événements
```diff
+ GET /events
```

Réponse 200:
[
    {
        "id": 1,
        "title": "Titre de l'événement",
        "content": "Contenu de l'événement",
        "startDatetime": "20/06/2024 15:00",
        "endDatetime": "20/06/2024 17:00",
        "eventType": "match",
        "location": "Stade Municipal",
        "sport": {
            "id": 1,
            "name": "Football"
        }
    }
]

#### Détail d'un événement
```diff
+ GET /events/{id}
```

Réponse 200:
{
    "id": 1,
    "title": "Titre de l'événement",
    "content": "Contenu de l'événement",
    "startDatetime": "20/06/2024 15:00",
    "endDatetime": "20/06/2024 17:00",
    "eventType": "match",
    "location": "Stade Municipal",
    "sport": {
        "id": 1,
        "name": "Football"
    }
}

#### Liste des sports
```diff
+ GET /sports
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Football",
        "description": "Sport collectif",
        "images": ["http://localhost:8000/uploads/images/sports/football.jpg"]
    }
]

#### Détail d'un sport
```diff
+ GET /sports/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Football",
    "description": "Sport collectif",
    "images": ["http://localhost:8000/uploads/images/sports/football.jpg"]
}

#### Liste des équipes
```diff
+ GET /teams
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Équipe A",
        "description": "Description de l'équipe",
        "sport": {
            "id": 1,
            "name": "Football"
        },
        "images": ["http://localhost:8000/uploads/images/teams/equipe-a.jpg"]
    }
]

#### Détail d'une équipe
```diff
+ GET /teams/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Équipe A",
    "description": "Description de l'équipe",
    "sport": {
        "id": 1,
        "name": "Football"
    },
    "images": ["http://localhost:8000/uploads/images/teams/equipe-a.jpg"]
}

#### Liste des galeries
```diff
+ GET /gallery
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Galerie 2024",
        "description": "Photos de la saison 2024",
        "images": ["http://localhost:8000/uploads/images/gallery/photo1.jpg"]
    }
]

#### Détail d'une galerie
```diff
+ GET /gallery/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Galerie 2024",
    "description": "Photos de la saison 2024",
    "images": ["http://localhost:8000/uploads/images/gallery/photo1.jpg"]
}

#### Liste des partenaires
```diff
+ GET /partners
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Partenaire XYZ",
        "description": "Description du partenaire",
        "website": "https://partenaire.com",
        "images": ["http://localhost:8000/uploads/images/partners/logo.jpg"]
    }
]

#### Détail d'un partenaire
```diff
+ GET /partners/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Partenaire XYZ",
    "description": "Description du partenaire",
    "website": "https://partenaire.com",
    "images": ["http://localhost:8000/uploads/images/partners/logo.jpg"]
}

#### Liste des vidéos
```diff
+ GET /videos
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Présentation du club",
        "videoUrl": "https://www.youtube.com/embed/xxxx",
        "highlighting": true
    }
]

#### Détail d'une vidéo
```diff
+ GET /videos/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Présentation du club",
    "videoUrl": "https://www.youtube.com/embed/xxxx",
    "highlighting": true
}

#### Page d'introduction
```diff
+ GET /introduction
```

Réponse 200:
{
    "id": 1,
    "title": "Bienvenue à l'ACOO",
    "content": "L'Association Culturelle et Omnisports d'Orléans...",
    "images": ["http://localhost:8000/uploads/images/introduction/banner.jpg"]
}

#### Informations de contact
```diff
+ GET /contact
```

Réponse 200:
{
    "id": 1,
    "address": "123 Rue de la Paix, 45000 Orléans",
    "phone": "02 38 00 00 00",
    "email": "contact@acoo.fr",
    "openingHours": "Lundi-Vendredi: 9h-18h"
}

#### Liste du staff
```diff
+ GET /staffs
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Jean Dupont",
        "role": "Entraîneur",
        "description": "Entraîneur principal",
        "images": ["http://localhost:8000/uploads/images/staffs/jean.jpg"]
    }
]

#### Détail d'un membre du staff
```diff
+ GET /staffs/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Jean Dupont",
    "role": "Entraîneur",
    "description": "Entraîneur principal",
    "images": ["http://localhost:8000/uploads/images/staffs/jean.jpg"]
}

#### Liste des prix
```diff
+ GET /prize-list
```

Réponse 200:
[
    {
        "id": 1,
        "title": "Champion de France",
        "description": "Victoire au championnat",
        "year": 2024,
        "images": ["http://localhost:8000/uploads/images/prizes/trophy.jpg"]
    }
]

#### Détail d'un prix
```diff
+ GET /prize-list/{id}
```

Réponse 200:
{
    "id": 1,
    "title": "Champion de France",
    "description": "Victoire au championnat",
    "year": 2024,
    "images": ["http://localhost:8000/uploads/images/prizes/trophy.jpg"]
}

#### Planning récurrent
```diff
+ GET /recurring-schedule
```

Réponse 200:
[
    {
        "id": 1,
        "dayOfWeek": "Lundi",
        "startTime": "18:00",
        "endTime": "20:00",
        "activity": "Entraînement football",
        "location": "Stade Municipal"
    }
]

#### Détail d'un planning récurrent
```diff
+ GET /recurring-schedule/{id}
```

Réponse 200:
{
    "id": 1,
    "dayOfWeek": "Lundi",
    "startTime": "18:00",
    "endTime": "20:00",
    "activity": "Entraînement football",
    "location": "Stade Municipal"
}

#### Exceptions au planning
```diff
+ GET /schedule-exeption
```

Réponse 200:
[
    {
        "id": 1,
        "date": "25/12/2024",
        "startTime": "14:00",
        "endTime": "16:00",
        "activity": "Match de Noël",
        "location": "Stade Municipal",
        "isCancelled": false
    }
]

#### Détail d'une exception
```diff
+ GET /schedule-exeption/{id}
```

Réponse 200:
{
    "id": 1,
    "date": "25/12/2024",
    "startTime": "14:00",
    "endTime": "16:00",
    "activity": "Match de Noël",
    "location": "Stade Municipal",
    "isCancelled": false
}

#### Réseaux sociaux
```diff
+ GET /social-medias
```

Réponse 200:
[
    {
        "id": 1,
        "platform": "Facebook",
        "url": "https://facebook.com/acoo",
        "icon": "fab fa-facebook"
    }
]

#### Détail d'un réseau social
```diff
+ GET /social-medias/{id}
```

Réponse 200:
{
    "id": 1,
    "platform": "Facebook",
    "url": "https://facebook.com/acoo",
    "icon": "fab fa-facebook"
}

#### Images
```diff
+ GET /images
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Image de présentation",
        "description": "Image pour la page d'accueil",
        "imageData": "http://localhost:8000/uploads/images/images/presentation.jpg"
    }
]

#### Détail d'une image
```diff
+ GET /images/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Image de présentation",
    "description": "Image pour la page d'accueil",
    "imageData": "http://localhost:8000/uploads/images/images/presentation.jpg"
}

#### Pictures
```diff
+ GET /pictures
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Photo d'équipe",
        "description": "Photo de l'équipe championne",
        "imageData": "http://localhost:8000/uploads/images/pictures/equipe.jpg"
    }
]

#### Détail d'une picture
```diff
+ GET /pictures/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Photo d'équipe",
    "description": "Photo de l'équipe championne",
    "imageData": "http://localhost:8000/uploads/images/pictures/equipe.jpg"
}

#### Questions
```diff
+ GET /questions
```

Réponse 200:
[
    {
        "id": 1,
        "question": "Quelle est la politique de remboursement ?",
        "answer": "Les remboursements sont possibles sous 30 jours..."
    }
]

#### Détail d'une question
```diff
+ GET /questions/{id}
```

Réponse 200:
{
    "id": 1,
    "question": "Quelle est la politique de remboursement ?",
    "answer": "Les remboursements sont possibles sous 30 jours..."
}

#### Contacts club
```diff
+ GET /contact-club
```

Réponse 200:
[
    {
        "id": 1,
        "name": "Contact Principal",
        "email": "contact@acoo.fr",
        "phone": "02 38 00 00 00",
        "role": "Secrétaire"
    }
]

#### Détail d'un contact club
```diff
+ GET /contact-club/{id}
```

Réponse 200:
{
    "id": 1,
    "name": "Contact Principal",
    "email": "contact@acoo.fr",
    "phone": "02 38 00 00 00",
    "role": "Secrétaire"
}

---

## 🔒 ROUTES PRIVÉES (Authentification JWT requise)

**En-tête requis :**
```
Authorization: Bearer <votre_token_jwt>
```

### 1. GESTION ADMIN

#### Mise à jour du profil admin
```diff
+ POST /admin/update
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouveau nom",
    "email": "nouveau@email.fr",
    "password": "nouveau_mot_de_passe"
}

Réponse 200:
{
    "message": "Profil mis à jour avec succès",
    "admin": {
        "id": 1,
        "name": "Nouveau nom",
        "email": "nouveau@email.fr"
    }
}

### 2. CRÉATION D'ENTITÉS

#### Créer une actualité (avec ou sans événement)
```diff
+ POST /news
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Titre de l'actualité",
    "content": "Contenu de l'actualité",
    "images": ["data:image/jpeg;base64,..."],
    "id_admin": 1,
    "startDatetime": "20/06/2024 15:00",
    "endDatetime": "20/06/2024 17:00",
    "eventType": "match",
    "location": "Stade Municipal",
    "sport": 1
}

Réponse 201:
{
    "id": 1,
    "title": "Titre de l'actualité",
    "content": "Contenu de l'actualité",
    "images": ["http://localhost:8000/uploads/images/news/image.jpg"],
    "event": {
        "id": 1,
        "startDatetime": "20/06/2024 15:00",
        "endDatetime": "20/06/2024 17:00",
        "eventType": "match",
        "location": "Stade Municipal",
        "sport": {
            "id": 1,
            "name": "Football"
        }
    },
    "created_at": "20/03/2024 10:00"
}

#### Créer un événement
```diff
+ POST /events
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Titre de l'événement",
    "content": "Contenu de l'événement",
    "startDatetime": "20/06/2024 15:00",
    "endDatetime": "20/06/2024 17:00",
    "eventType": "match",
    "location": "Stade Municipal",
    "sport": 1,
    "id_admin": 1
}

Réponse 201:
{
    "id": 1,
    "title": "Titre de l'événement",
    "content": "Contenu de l'événement",
    "startDatetime": "20/06/2024 15:00",
    "endDatetime": "20/06/2024 17:00",
    "eventType": "match",
    "location": "Stade Municipal",
    "sport": {
        "id": 1,
        "name": "Football"
    },
    "news": {
        "id": 1,
        "title": "Titre de l'événement",
        "content": "Contenu de l'événement"
    }
}

#### Créer une vidéo
```diff
+ POST /videos
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Présentation du club",
    "videoUrl": "https://www.youtube.com/embed/xxxx",
    "highlighting": true
}

Réponse 201:
{
    "id": 1,
    "name": "Présentation du club",
    "videoUrl": "https://www.youtube.com/embed/xxxx",
    "highlighting": true
}

#### Créer un sport
```diff
+ POST /sports
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Football",
    "description": "Sport collectif",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "name": "Football",
    "description": "Sport collectif",
    "images": ["http://localhost:8000/uploads/images/sports/football.jpg"]
}

#### Créer une équipe
```diff
+ POST /teams
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Équipe A",
    "description": "Description de l'équipe",
    "sport": 1,
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "name": "Équipe A",
    "description": "Description de l'équipe",
    "sport": {
        "id": 1,
        "name": "Football"
    },
    "images": ["http://localhost:8000/uploads/images/teams/equipe-a.jpg"]
}

#### Créer une galerie
```diff
+ POST /gallery
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Galerie 2024",
    "description": "Photos de la saison 2024",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "name": "Galerie 2024",
    "description": "Photos de la saison 2024",
    "images": ["http://localhost:8000/uploads/images/gallery/photo1.jpg"]
}

#### Créer un partenaire
```diff
+ POST /partners
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Partenaire XYZ",
    "description": "Description du partenaire",
    "website": "https://partenaire.com",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "name": "Partenaire XYZ",
    "description": "Description du partenaire",
    "website": "https://partenaire.com",
    "images": ["http://localhost:8000/uploads/images/partners/logo.jpg"]
}

#### Créer un membre du staff
```diff
+ POST /staffs
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Jean Dupont",
    "role": "Entraîneur",
    "description": "Entraîneur principal",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "name": "Jean Dupont",
    "role": "Entraîneur",
    "description": "Entraîneur principal",
    "images": ["http://localhost:8000/uploads/images/staffs/jean.jpg"]
}

#### Créer un prix
```diff
+ POST /prize-list
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Champion de France",
    "description": "Victoire au championnat",
    "year": 2024,
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 201:
{
    "id": 1,
    "title": "Champion de France",
    "description": "Victoire au championnat",
    "year": 2024,
    "images": ["http://localhost:8000/uploads/images/prizes/trophy.jpg"]
}

#### Créer un planning récurrent
```diff
+ POST /recurring-schedule
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "dayOfWeek": "Lundi",
    "startTime": "18:00",
    "endTime": "20:00",
    "activity": "Entraînement football",
    "location": "Stade Municipal"
}

Réponse 201:
{
    "id": 1,
    "dayOfWeek": "Lundi",
    "startTime": "18:00",
    "endTime": "20:00",
    "activity": "Entraînement football",
    "location": "Stade Municipal"
}

#### Créer une exception au planning
```diff
+ POST /schedule-exeption
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "date": "25/12/2024",
    "startTime": "14:00",
    "endTime": "16:00",
    "activity": "Match de Noël",
    "location": "Stade Municipal",
    "isCancelled": false
}

Réponse 201:
{
    "id": 1,
    "date": "25/12/2024",
    "startTime": "14:00",
    "endTime": "16:00",
    "activity": "Match de Noël",
    "location": "Stade Municipal",
    "isCancelled": false
}

#### Créer/Mettre à jour l'introduction
```diff
+ POST /introduction
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Bienvenue à l'ACOO",
    "content": "L'Association Culturelle et Omnisports d'Orléans...",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "title": "Bienvenue à l'ACOO",
    "content": "L'Association Culturelle et Omnisports d'Orléans...",
    "images": ["http://localhost:8000/uploads/images/introduction/banner.jpg"]
}

#### Créer/Mettre à jour les informations de contact
```diff
+ POST /contact
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "address": "123 Rue de la Paix, 45000 Orléans",
    "phone": "02 38 00 00 00",
    "email": "contact@acoo.fr",
    "openingHours": "Lundi-Vendredi: 9h-18h"
}

Réponse 200:
{
    "id": 1,
    "address": "123 Rue de la Paix, 45000 Orléans",
    "phone": "02 38 00 00 00",
    "email": "contact@acoo.fr",
    "openingHours": "Lundi-Vendredi: 9h-18h"
}

#### Créer un réseau social
```diff
+ POST /social-medias
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "platform": "Facebook",
    "url": "https://facebook.com/acoo",
    "icon": "fab fa-facebook"
}

Réponse 201:
{
    "id": 1,
    "platform": "Facebook",
    "url": "https://facebook.com/acoo",
    "icon": "fab fa-facebook"
}

#### Créer une image
```diff
+ POST /images
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Image de présentation",
    "description": "Image pour la page d'accueil",
    "imageData": "data:image/jpeg;base64,..."
}

Réponse 201:
{
    "id": 1,
    "name": "Image de présentation",
    "description": "Image pour la page d'accueil",
    "imageData": "http://localhost:8000/uploads/images/images/presentation.jpg"
}

#### Créer une picture
```diff
+ POST /pictures
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Photo d'équipe",
    "description": "Photo de l'équipe championne",
    "imageData": "data:image/jpeg;base64,..."
}

Réponse 201:
{
    "id": 1,
    "name": "Photo d'équipe",
    "description": "Photo de l'équipe championne",
    "imageData": "http://localhost:8000/uploads/images/pictures/equipe.jpg"
}

#### Créer une question
```diff
+ POST /questions
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "question": "Quelle est la politique de remboursement ?",
    "answer": "Les remboursements sont possibles sous 30 jours..."
}

Réponse 201:
{
    "id": 1,
    "question": "Quelle est la politique de remboursement ?",
    "answer": "Les remboursements sont possibles sous 30 jours..."
}

#### Créer un contact club
```diff
+ POST /contact-club
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Contact Principal",
    "email": "contact@acoo.fr",
    "phone": "02 38 00 00 00",
    "role": "Secrétaire"
}

Réponse 201:
{
    "id": 1,
    "name": "Contact Principal",
    "email": "contact@acoo.fr",
    "phone": "02 38 00 00 00",
    "role": "Secrétaire"
}

### 3. MODIFICATION D'ENTITÉS

#### Modifier une actualité
```diff
+ POST /news/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Nouveau titre",
    "content": "Nouveau contenu",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "title": "Nouveau titre",
    "content": "Nouveau contenu",
    "images": ["http://localhost:8000/uploads/images/news/nouvelle-image.jpg"],
    "updated_at": "20/03/2024 11:00"
}

#### Modifier un événement
```diff
+ POST /events/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Nouveau titre",
    "content": "Nouveau contenu",
    "startDatetime": "21/06/2024 16:00",
    "endDatetime": "21/06/2024 18:00",
    "eventType": "entraînement",
    "location": "Nouveau stade",
    "sport": 2
}

Réponse 200:
{
    "id": 1,
    "title": "Nouveau titre",
    "content": "Nouveau contenu",
    "startDatetime": "21/06/2024 16:00",
    "endDatetime": "21/06/2024 18:00",
    "eventType": "entraînement",
    "location": "Nouveau stade",
    "sport": {
        "id": 2,
        "name": "Basketball"
    }
}

#### Modifier une vidéo
```diff
+ POST /videos/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouvelle présentation",
    "videoUrl": "https://www.youtube.com/embed/yyyy",
    "highlighting": false
}

Réponse 200:
{
    "id": 1,
    "name": "Nouvelle présentation",
    "videoUrl": "https://www.youtube.com/embed/yyyy",
    "highlighting": false
}

#### Modifier un sport
```diff
+ POST /sports/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Football Américain",
    "description": "Nouvelle description",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "name": "Football Américain",
    "description": "Nouvelle description",
    "images": ["http://localhost:8000/uploads/images/sports/football-americain.jpg"]
}

#### Modifier une équipe
```diff
+ POST /teams/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Équipe B",
    "description": "Nouvelle description",
    "sport": 2,
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "name": "Équipe B",
    "description": "Nouvelle description",
    "sport": {
        "id": 2,
        "name": "Basketball"
    },
    "images": ["http://localhost:8000/uploads/images/teams/equipe-b.jpg"]
}

#### Modifier une galerie
```diff
+ POST /gallery/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Galerie 2025",
    "description": "Nouvelle description",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "name": "Galerie 2025",
    "description": "Nouvelle description",
    "images": ["http://localhost:8000/uploads/images/gallery/photo2.jpg"]
}

#### Modifier un partenaire
```diff
+ POST /partners/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouveau Partenaire",
    "description": "Nouvelle description",
    "website": "https://nouveau-partenaire.com",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "name": "Nouveau Partenaire",
    "description": "Nouvelle description",
    "website": "https://nouveau-partenaire.com",
    "images": ["http://localhost:8000/uploads/images/partners/nouveau-logo.jpg"]
}

#### Modifier un membre du staff
```diff
+ POST /staffs/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Marie Martin",
    "role": "Entraîneuse",
    "description": "Nouvelle description",
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "name": "Marie Martin",
    "role": "Entraîneuse",
    "description": "Nouvelle description",
    "images": ["http://localhost:8000/uploads/images/staffs/marie.jpg"]
}

#### Modifier un prix
```diff
+ POST /prize-list/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "title": "Vice-champion",
    "description": "Nouvelle description",
    "year": 2025,
    "images": ["data:image/jpeg;base64,..."]
}

Réponse 200:
{
    "id": 1,
    "title": "Vice-champion",
    "description": "Nouvelle description",
    "year": 2025,
    "images": ["http://localhost:8000/uploads/images/prizes/medaille.jpg"]
}

#### Modifier un planning récurrent
```diff
+ POST /recurring-schedule/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "dayOfWeek": "Mardi",
    "startTime": "19:00",
    "endTime": "21:00",
    "activity": "Entraînement basketball",
    "location": "Gymnase"
}

Réponse 200:
{
    "id": 1,
    "dayOfWeek": "Mardi",
    "startTime": "19:00",
    "endTime": "21:00",
    "activity": "Entraînement basketball",
    "location": "Gymnase"
}

#### Modifier une exception au planning
```diff
+ POST /schedule-exeption/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "date": "26/12/2024",
    "startTime": "15:00",
    "endTime": "17:00",
    "activity": "Match de fin d'année",
    "location": "Nouveau stade",
    "isCancelled": true
}

Réponse 200:
{
    "id": 1,
    "date": "26/12/2024",
    "startTime": "15:00",
    "endTime": "17:00",
    "activity": "Match de fin d'année",
    "location": "Nouveau stade",
    "isCancelled": true
}

#### Modifier un réseau social
```diff
+ POST /social-medias/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "platform": "Instagram",
    "url": "https://instagram.com/acoo",
    "icon": "fab fa-instagram"
}

Réponse 200:
{
    "id": 1,
    "platform": "Instagram",
    "url": "https://instagram.com/acoo",
    "icon": "fab fa-instagram"
}

#### Modifier une image
```diff
+ POST /images/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouvelle image",
    "description": "Nouvelle description",
    "imageData": "data:image/jpeg;base64,..."
}

Réponse 200:
{
    "id": 1,
    "name": "Nouvelle image",
    "description": "Nouvelle description",
    "imageData": "http://localhost:8000/uploads/images/images/nouvelle-image.jpg"
}

#### Modifier une picture
```diff
+ POST /pictures/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouvelle photo",
    "description": "Nouvelle description",
    "imageData": "data:image/jpeg;base64,..."
}

Réponse 200:
{
    "id": 1,
    "name": "Nouvelle photo",
    "description": "Nouvelle description",
    "imageData": "http://localhost:8000/uploads/images/pictures/nouvelle-photo.jpg"
}

#### Modifier une question
```diff
+ POST /questions/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "question": "Nouvelle question ?",
    "answer": "Nouvelle réponse..."
}

Réponse 200:
{
    "id": 1,
    "question": "Nouvelle question ?",
    "answer": "Nouvelle réponse..."
}

#### Modifier un contact club
```diff
+ POST /contact-club/{id}
```
Authorization: Bearer <token>
Content-Type: application/json

Body:
{
    "name": "Nouveau Contact",
    "email": "nouveau@acoo.fr",
    "phone": "02 38 00 00 01",
    "role": "Président"
}

Réponse 200:
{
    "id": 1,
    "name": "Nouveau Contact",
    "email": "nouveau@acoo.fr",
    "phone": "02 38 00 00 01",
    "role": "Président"
}

### 4. SUPPRESSION D'ENTITÉS

#### Supprimer une actualité
```diff
+ DELETE /news/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Actualité supprimée avec succès"
}

#### Supprimer un événement
```diff
+ DELETE /events/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Événement supprimé avec succès"
}

#### Supprimer une vidéo
```diff
+ DELETE /videos/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Vidéo supprimée avec succès"
}

#### Supprimer un sport
```diff
+ DELETE /sports/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Sport supprimé avec succès"
}

#### Supprimer une équipe
```diff
+ DELETE /teams/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Équipe supprimée avec succès"
}

#### Supprimer une galerie
```diff
+ DELETE /gallery/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Galerie supprimée avec succès"
}

#### Supprimer un partenaire
```diff
+ DELETE /partners/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Partenaire supprimé avec succès"
}

#### Supprimer un membre du staff
```diff
+ DELETE /staffs/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Membre du staff supprimé avec succès"
}

#### Supprimer un prix
```diff
+ DELETE /prize-list/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Prix supprimé avec succès"
}

#### Supprimer un planning récurrent
```diff
+ DELETE /recurring-schedule/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Planning supprimé avec succès"
}

#### Supprimer une exception au planning
```diff
+ DELETE /schedule-exeption/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Exception supprimée avec succès"
}

#### Supprimer un réseau social
```diff
+ DELETE /social-medias/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Réseau social supprimé avec succès"
}

#### Supprimer une image
```diff
+ DELETE /images/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Image supprimée avec succès"
}

#### Supprimer une picture
```diff
+ DELETE /pictures/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Picture supprimée avec succès"
}

#### Supprimer une question
```diff
+ DELETE /questions/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Question supprimée avec succès"
}

#### Supprimer un contact club
```diff
+ DELETE /contact-club/{id}
```
Authorization: Bearer <token>

Réponse 200:
{
    "message": "Contact club supprimé avec succès"
}

---

## 📝 NOTES IMPORTANTES

### Format des images
- Les images sont envoyées en base64 dans le champ `images` (tableau)
- Format : `data:image/jpeg;base64,<données_base64>`
- Les images sont stockées dans `public/uploads/images/`

### Gestion des erreurs
- `400` : Requête invalide
- `401` : Non autorisé (token manquant ou invalide)
- `404` : Ressource non trouvée
- `500` : Erreur serveur interne

### Logique spéciale
- **Vidéos** : Une seule vidéo peut avoir `highlighting=true` à la fois
- **News/Events** : Un Event est toujours lié à une News
- **Images** : Vérification que le fichier existe avant suppression

### Commandes utiles
```bash
# Créer une migration
php bin/console make:migration

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Vider le cache
php bin/console cache:clear
``` 