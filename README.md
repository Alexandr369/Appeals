# Appeals

Azuriom plugin letting players appeal their bans and mutes directly from your website, with optional automatic detection from AdvancedBan or LibertyBans.

## 🇫🇷 Français

# Appeals — Système de recours de sanctions

Vos joueurs bannis ou mute n'ont aujourd'hui aucun moyen de contester leur sanction depuis votre site. Les plugins existants (AdvancedBan, LibertyBans) se limitent à **afficher** la liste des sanctions — aucun ne permet de déposer un recours. **Appeals** comble ce manque : un vrai système de recours, intégré à votre thème, qui se connecte directement à vos sanctions existantes.

### Côté joueur

- Dépose une demande de recours (motif + explication libre) contre un ban ou un mute actif. Longueur minimale et maximale de l'explication configurables, avec compteur de caractères en direct.
- Un texte d'introduction personnalisable (règles, conseils...) peut être affiché en haut du formulaire.
- **Détection automatique** de la sanction en cours si AdvancedBan ou LibertyBans est installé (raison, staff, durée affichés automatiquement) — aucune ressaisie manuelle des identifiants de connexion, Appeals réutilise directement la configuration déjà en place dans le plugin AdvancedBan/LibertyBans.
- Banni **et** muté en même temps ? Le joueur choisit lui-même, via un menu déroulant, quelle sanction il souhaite contester.
- Suivi de ses demandes avec un **historique visuel** de chaque changement de statut (déposée, à l'étude, acceptée/refusée, avec la date de chaque étape), et la réponse du staff une fois traitée.
- **Notification** sur le site (la cloche habituelle) dès que sa demande est traitée, avec lien direct vers celle-ci.
- Peut modifier sa demande tant qu'elle est en attente (permission dédiée).
- **Bannière automatique** sur tout le site pour prévenir un joueur sanctionné qu'il peut faire un recours, ou lui rappeler qu'une demande est en cours (désactivable).
- Anti-spam : une seule demande active à la fois, délai configurable avant de pouvoir refaire une demande après un refus, et option pour bloquer définitivement un nouveau recours sur une sanction déjà tranchée.

### Côté staff

- Liste des demandes avec compteurs par statut, filtres, recherche par pseudo, pagination.
- Traitement en un clic : changer le statut et répondre au joueur, avec des **réponses pré-écrites** sélectionnables dans un menu déroulant pour gagner du temps.
- Suppression d'une demande si besoin.
- Un badge rouge dans le menu d'administration indique en permanence le nombre de demandes non traitées.
- **Automatisation en jeu entièrement personnalisable** : définissez autant de commandes que vous voulez (débannir, démuter, donner un grade, etc.), chacune avec un nom et une commande ; le staff choisit laquelle exécuter dans un menu déroulant au moment d'accepter une demande, envoyée via AzLink.
- Notifications Discord par webhook à chaque nouvelle demande et à chaque changement de statut, avec un **texte de message entièrement personnalisable** ({player}, {reason}, {status}).
- Page de réglages complète : source des sanctions, types de sanctions contestables, délai et blocage anti-spam, bannière, texte d'intro, longueur de l'explication, motifs et réponses rapides (ajout/suppression en un clic), webhook et messages Discord, automatisation en jeu.
- 5 permissions distinctes pour un contrôle précis des accès (déposer, modifier, voir, traiter, gérer les réglages).

### Compatibilité

- Fonctionne seul (saisie libre par le joueur) ou connecté à **AdvancedBan** / **LibertyBans** (MySQL, MariaDB et PostgreSQL pris en charge pour LibertyBans).
- 100 % lecture seule sur ces bases externes : Appeals ne modifie jamais vos données de sanctions.
- Traductions complètes en français et en anglais.
- Compatible avec le thème par défaut d'Azuriom, construit en Bootstrap 5 pour s'intégrer à la plupart des autres thèmes.

## 🇬🇧 English

# Appeals — Sanction appeal system

Banned or muted players currently have no way to contest their sanction from your website. Existing plugins (AdvancedBan, LibertyBans) only **display** the list of sanctions — none of them let players actually appeal. **Appeals** fills that gap: a real appeal system, built into your theme, that connects directly to your existing sanctions.

### Player side

- Submit an appeal (reason + free-text explanation) against an active ban or mute. Minimum and maximum explanation length are configurable, with a live character counter.
- An optional, customizable intro text (rules, tips...) can be shown above the form.
- **Automatic sanction detection** if AdvancedBan or LibertyBans is installed (reason, staff, duration shown automatically) — no need to re-enter any database credentials, Appeals directly reuses the connection already configured in the AdvancedBan/LibertyBans plugin.
- Banned **and** muted at the same time? The player picks, from a dropdown, which sanction they want to appeal.
- Tracks their appeals with a **visual timeline** of every status change (submitted, under review, accepted/rejected, each with its own date), plus the staff's response once processed.
- Gets a **notification** on the site (the usual bell icon) as soon as their appeal is processed, linking straight to it.
- Can edit their appeal while it's still pending (dedicated permission).
- **Site-wide banner** letting a sanctioned player know they can appeal, or reminding them an appeal is already pending (can be turned off).
- Anti-spam: one active appeal at a time, a configurable delay before appealing again after a rejection, and an option to permanently block a new appeal for a sanction that has already been decided.

### Staff side

- List of appeals with per-status count cards, filters, search by player name, pagination.
- One-click processing: change the status and reply to the player, with **pre-written quick responses** selectable from a dropdown to save time.
- Delete an appeal if needed.
- A red badge in the admin menu always shows how many appeals are still awaiting processing.
- **Fully customizable in-game automation**: define as many commands as you want (unban, unmute, grant a rank, anything), each with its own name; staff picks which one to run from a dropdown when accepting an appeal, sent through AzLink.
- Discord webhook notifications on every new appeal and every status change, with **fully customizable message text** ({player}, {reason}, {status}).
- A complete settings page: sanction source, which sanction types can be appealed, anti-spam delay and permanent block, banner, intro text, explanation length, reasons and quick responses (add/remove with one click), Discord webhook and messages, in-game automation.
- 5 distinct permissions for fine-grained access control (submit, edit, view, process, manage settings).

### Compatibility

- Works standalone (free-text entry from the player) or connected to **AdvancedBan** / **LibertyBans** (MySQL, MariaDB and PostgreSQL supported for LibertyBans).
- 100% read-only on those external databases: Appeals never modifies your sanction data.
- Fully translated in French and English.
- Compatible with Azuriom's default theme, built with Bootstrap 5 to fit most other themes.
