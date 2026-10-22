# Appeals

Azuriom plugin letting players appeal a sanction directly from your website. Unlike a generic support ticket, accepting an appeal can automatically execute an in-game command (unban, unmute, anything) — every appeal is tied to structured sanction data (type, date, staff), never free-form text. Works with any game or moderation system; AdvancedBan/LibertyBans auto-detection is an optional bonus for Minecraft servers.

## 🇫🇷 Français

# Appeals — Système de recours de sanctions

Vos joueurs sanctionnés n'ont aujourd'hui aucun moyen de contester leur sanction depuis votre site. **Ce n'est pas un système de tickets générique** comme le plugin Support : accepter une demande peut **exécuter automatiquement une commande en jeu** (débannir, démuter, n'importe quoi d'autre) — un ticket de support classique ne pourra jamais faire ça. Chaque demande Appeals est en plus structurée autour d'une sanction précise (type, date, responsable), que celle-ci soit détectée automatiquement ou renseignée par le joueur, jamais un simple texte libre. **Appeals fonctionne avec n'importe quel jeu ou système de modération** — AdvancedBan et LibertyBans (Minecraft) ne sont que des intégrations optionnelles qui évitent au joueur de ressaisir les informations à la main.

### Côté joueur

- Dépose une demande de recours contre une sanction : type, date, responsable et motif de la sanction, en plus de son explication. Longueur minimale et maximale de l'explication configurables, avec compteur de caractères en direct.
- Si aucun plugin de sanctions compatible n'est installé (serveur non-Minecraft, ou autre système de modération), le joueur renseigne lui-même ces informations dans un formulaire dédié — **Appeals reste pleinement fonctionnel sans aucune dépendance à un jeu en particulier**.
- Un texte d'introduction personnalisable (règles, conseils...) peut être affiché en haut du formulaire.
- **Détection automatique** de la sanction en cours si AdvancedBan ou LibertyBans est installé (raison, staff, durée affichés automatiquement) — aucune ressaisie manuelle des identifiants de connexion, Appeals réutilise directement la configuration déjà en place dans le plugin AdvancedBan/LibertyBans.
- Banni **et** muté en même temps ? Le joueur choisit lui-même, via un menu déroulant, quelle sanction il souhaite contester.
- Suivi de ses demandes avec un **historique visuel** de chaque changement de statut (déposée, à l'étude, acceptée/refusée, avec la date de chaque étape), et la réponse du staff une fois traitée.
- **Notification** sur le site (la cloche habituelle) dès que sa demande est traitée, avec lien direct vers celle-ci.
- Peut modifier sa demande tant qu'elle est en attente (permission dédiée).
- **Bannière automatique** sur tout le site pour prévenir un joueur sanctionné qu'il peut faire un recours, ou lui rappeler qu'une demande est en cours (désactivable).
- Anti-spam : une seule demande active à la fois, délai configurable avant de pouvoir refaire une demande après un refus, et option pour bloquer définitivement un nouveau recours sur une sanction déjà tranchée.

### Côté staff

- **Automatisation en jeu entièrement personnalisable** — ce qu'un système de tickets ne peut pas faire : définissez autant de commandes que vous voulez (débannir, démuter, donner un grade, etc.), chacune avec un nom et une commande ; le staff choisit laquelle exécuter dans un menu déroulant au moment d'accepter une demande, envoyée via AzLink.
- Liste des demandes avec compteurs par statut, filtres, recherche par pseudo, pagination.
- Traitement en un clic : changer le statut et répondre au joueur, avec des **réponses pré-écrites** sélectionnables dans un menu déroulant pour gagner du temps.
- Suppression d'une demande si besoin.
- Un badge rouge dans le menu d'administration indique en permanence le nombre de demandes non traitées.
- Notifications Discord par webhook à chaque nouvelle demande et à chaque changement de statut, avec un **texte de message entièrement personnalisable** ({player}, {reason}, {status}).
- Page de réglages complète : source des sanctions, types de sanctions contestables, délai et blocage anti-spam, bannière, texte d'intro, longueur de l'explication, motifs et réponses rapides (ajout/suppression en un clic), webhook et messages Discord, automatisation en jeu.
- 5 permissions distinctes pour un contrôle précis des accès (déposer, modifier, voir, traiter, gérer les réglages).

### Compatibilité

- Fonctionne seul, avec saisie manuelle structurée par le joueur (type, date, responsable), ou connecté à **AdvancedBan** / **LibertyBans** (MySQL, MariaDB et PostgreSQL pris en charge pour LibertyBans).
- 100 % lecture seule sur ces bases externes : Appeals ne modifie jamais vos données de sanctions.
- Traductions complètes en français et en anglais.
- Compatible avec le thème par défaut d'Azuriom, construit en Bootstrap 5 pour s'intégrer à la plupart des autres thèmes.

## 🇬🇧 English

# Appeals — Sanction appeal system

Sanctioned players currently have no way to contest their sanction from your website. **This is not a generic ticket system** like the Support plugin: accepting a request can **automatically execute an in-game command** (unban, unmute, anything else) — a regular support ticket can never do that. Every Appeals request is also structured around a specific sanction (type, date, staff), whether detected automatically or entered by the player, never plain free text. **Appeals works with any game or moderation system** — AdvancedBan and LibertyBans (Minecraft) are only optional integrations that save the player from typing the details in by hand.

### Player side

- Submit an appeal against a sanction: type, date, staff member and reason for the sanction, on top of their own explanation. Minimum and maximum explanation length are configurable, with a live character counter.
- If no compatible sanction plugin is installed (non-Minecraft server, or another moderation system), the player fills in these details themselves in a dedicated form — **Appeals stays fully functional with no dependency on any particular game**.
- An optional, customizable intro text (rules, tips...) can be shown above the form.
- **Automatic sanction detection** if AdvancedBan or LibertyBans is installed (reason, staff, duration shown automatically) — no need to re-enter any database credentials, Appeals directly reuses the connection already configured in the AdvancedBan/LibertyBans plugin.
- Banned **and** muted at the same time? The player picks, from a dropdown, which sanction they want to appeal.
- Tracks their appeals with a **visual timeline** of every status change (submitted, under review, accepted/rejected, each with its own date), plus the staff's response once processed.
- Gets a **notification** on the site (the usual bell icon) as soon as their appeal is processed, linking straight to it.
- Can edit their appeal while it's still pending (dedicated permission).
- **Site-wide banner** letting a sanctioned player know they can appeal, or reminding them an appeal is already pending (can be turned off).
- Anti-spam: one active appeal at a time, a configurable delay before appealing again after a rejection, and an option to permanently block a new appeal for a sanction that has already been decided.

### Staff side

- **Fully customizable in-game automation** — something a ticket system cannot do: define as many commands as you want (unban, unmute, grant a rank, anything), each with its own name; staff picks which one to run from a dropdown when accepting an appeal, sent through AzLink.
- List of appeals with per-status count cards, filters, search by player name, pagination.
- One-click processing: change the status and reply to the player, with **pre-written quick responses** selectable from a dropdown to save time.
- Delete an appeal if needed.
- A red badge in the admin menu always shows how many appeals are still awaiting processing.
- Discord webhook notifications on every new appeal and every status change, with **fully customizable message text** ({player}, {reason}, {status}).
- A complete settings page: sanction source, which sanction types can be appealed, anti-spam delay and permanent block, banner, intro text, explanation length, reasons and quick responses (add/remove with one click), Discord webhook and messages, in-game automation.
- 5 distinct permissions for fine-grained access control (submit, edit, view, process, manage settings).

### Compatibility

- Works standalone, with structured manual entry from the player (type, date, staff), or connected to **AdvancedBan** / **LibertyBans** (MySQL, MariaDB and PostgreSQL supported for LibertyBans).
- 100% read-only on those external databases: Appeals never modifies your sanction data.
- Fully translated in French and English.
- Compatible with Azuriom's default theme, built with Bootstrap 5 to fit most other themes.
