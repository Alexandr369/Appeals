# Appeals

Azuriom plugin letting players appeal their bans and mutes directly from your website, with optional automatic detection from AdvancedBan or LibertyBans.

## 🇫🇷 Français

# Appeals — Système de recours de sanctions

Vos joueurs bannis ou mute n'ont aujourd'hui aucun moyen de contester leur sanction depuis votre site. Les plugins existants (AdvancedBan, LibertyBans) se limitent à **afficher** la liste des sanctions — aucun ne permet de déposer un recours. **Appeals** comble ce manque : un vrai système de recours, intégré à votre thème, qui se connecte directement à vos sanctions existantes.

### Côté joueur

- Dépose une demande de recours (motif + explication libre) contre un ban ou un mute actif.
- **Détection automatique** de la sanction en cours si AdvancedBan ou LibertyBans est installé (raison, staff, durée affichés automatiquement) — aucune ressaisie manuelle des identifiants de connexion, Appeals réutilise directement la configuration déjà en place dans le plugin AdvancedBan/LibertyBans.
- Banni **et** muté en même temps ? Le joueur choisit lui-même, via un menu déroulant, quelle sanction il souhaite contester.
- Suivi de ses demandes et de leur statut, avec la réponse du staff une fois traitée.
- **Notification** sur le site (la cloche habituelle) dès que sa demande est traitée, avec lien direct vers celle-ci.
- Peut modifier sa demande tant qu'elle est en attente (permission dédiée).
- Anti-spam : une seule demande active à la fois, délai configurable avant de pouvoir refaire une demande après un refus, et option pour bloquer définitivement un nouveau recours sur une sanction déjà tranchée.

### Côté staff

- Liste des demandes avec filtres par statut, recherche par pseudo, pagination.
- Traitement en un clic : changer le statut (en attente / à l'étude / acceptée / refusée) et répondre au joueur.
- Suppression d'une demande si besoin.
- Un badge rouge dans le menu d'administration indique en permanence le nombre de demandes non traitées.
- **Automatisation optionnelle** : en acceptant une demande, lève automatiquement la sanction en jeu via AzLink, sans aucune manipulation manuelle côté serveur.
- Notifications Discord par webhook à chaque nouvelle demande et à chaque changement de statut.
- Page de réglages complète : source des sanctions (aucune / AdvancedBan / LibertyBans), types de sanctions contestables, délai anti-spam, motifs proposés, webhook Discord, automatisation en jeu.
- 5 permissions distinctes pour un contrôle précis des accès (déposer, modifier, voir, traiter, gérer les réglages).

### Compatibilité

- Fonctionne seul (saisie libre par le joueur) ou connecté à **AdvancedBan** / **LibertyBans** (MySQL, MariaDB et PostgreSQL pris en charge pour LibertyBans).
- 100 % lecture seule sur ces bases externes : Appeals ne modifie jamais vos données de sanctions.
- Traductions complètes en français et en anglais.
- Compatible avec le thème par défaut d'Azuriom, construit en Bootstrap 5 pour s'intégrer à la plupart des autres thèmes.

*Une version Pro est en préparation : support de LiteBans, discussion interne du staff sur une demande, et statistiques.*

## 🇬🇧 English

# Appeals — Sanction appeal system

Banned or muted players currently have no way to contest their sanction from your website. Existing plugins (AdvancedBan, LibertyBans) only **display** the list of sanctions — none of them let players actually appeal. **Appeals** fills that gap: a real appeal system, built into your theme, that connects directly to your existing sanctions.

### Player side

- Submit an appeal (reason + free-text explanation) against an active ban or mute.
- **Automatic sanction detection** if AdvancedBan or LibertyBans is installed (reason, staff, duration shown automatically) — no need to re-enter any database credentials, Appeals directly reuses the connection already configured in the AdvancedBan/LibertyBans plugin.
- Banned **and** muted at the same time? The player picks, from a dropdown, which sanction they want to appeal.
- Tracks their appeals and their status, with the staff's response once processed.
- Gets a **notification** on the site (the usual bell icon) as soon as their appeal is processed, linking straight to it.
- Can edit their appeal while it's still pending (dedicated permission).
- Anti-spam: one active appeal at a time, a configurable delay before appealing again after a rejection, and an option to permanently block a new appeal for a sanction that has already been decided.

### Staff side

- List of appeals with status filters, search by player name, pagination.
- One-click processing: change the status (pending / under review / accepted / rejected) and reply to the player.
- Delete an appeal if needed.
- A red badge in the admin menu always shows how many appeals are still awaiting processing.
- **Optional automation**: accepting an appeal can automatically lift the in-game sanction through AzLink, with no manual server-side action needed.
- Discord webhook notifications on every new appeal and every status change.
- A complete settings page: sanction source (none / AdvancedBan / LibertyBans), which sanction types can be appealed, anti-spam delay, available reasons, Discord webhook, in-game automation.
- 5 distinct permissions for fine-grained access control (submit, edit, view, process, manage settings).

### Compatibility

- Works standalone (free-text entry from the player) or connected to **AdvancedBan** / **LibertyBans** (MySQL, MariaDB and PostgreSQL supported for LibertyBans).
- 100% read-only on those external databases: Appeals never modifies your sanction data.
- Fully translated in French and English.
- Compatible with Azuriom's default theme, built with Bootstrap 5 to fit most other themes.

*A Pro version is in the works: LiteBans support, internal staff discussion on an appeal, and statistics.*
