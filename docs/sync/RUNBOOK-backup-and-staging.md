# Runbook — Backup cPanel & Build a Local Staging Copy

Do this **before** any migration or sync touches real data. Goal: a full backup of
production, plus a throwaway local copy of the cPanel data we can rehearse against.

## A. Back up the cPanel (production) database

1. Log into **cPanel** → **phpMyAdmin**.
2. Select the `webdev_corndogku` database in the left sidebar.
3. Top menu → **Export**.
   - Export method: **Quick**
   - Format: **SQL**
   - Click **Export** → save the `.sql` file somewhere safe (date it, e.g.
     `cpanel-webdev_corndogku-2026-06-07.sql`).
4. Keep this file. If anything ever goes wrong on cPanel, re-importing this dump
   restores the database exactly as it was now.

> Alternative: cPanel → **Backup** / **Backup Wizard** → *Download a MySQL Database
> Backup*. Same result.

## B. Back up the LOCAL database too

Before we run migrations on local, snapshot it as well:

```bash
# adjust port/credentials to match your .env (DB_PORT=6000)
mysqldump -h 127.0.0.1 -P 6000 -u root -p webdev_corndogku \
  > ~/backups/local-webdev_corndogku-2026-06-07.sql
```

## C. Build a local staging copy of the cPanel data

This lets us rehearse the migrations + baseline reconciliation offline, with **zero**
risk to production.

1. Create an empty local database:

   ```bash
   mysql -h 127.0.0.1 -P 6000 -u root -p -e "CREATE DATABASE corndogku_cpanel_copy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

2. Import the cPanel dump from step A into it:

   ```bash
   mysql -h 127.0.0.1 -P 6000 -u root -p corndogku_cpanel_copy \
     < ~/Downloads/cpanel-webdev_corndogku-2026-06-07.sql
   ```

3. Tell me when `corndogku_cpanel_copy` exists — I'll point a second Laravel
   connection at it so we can rehearse the whole sync against real cPanel data
   locally before we ever touch the live server.

## Checklist

- [ ] cPanel DB exported and saved (step A)
- [ ] Local DB dumped and saved (step B)
- [ ] `corndogku_cpanel_copy` created and imported (step C)

Once these three are done, it is safe to run the migrations on local.

## D. Timezone check BEFORE deploying to cPanel (important)

The app uses WIB (`Asia/Jakarta`) and both DB connections are pinned to
`DB_TIMEZONE=+07:00`. For the sync to never drift, cPanel must use the **same**
session timezone its data was already written under. Before setting
`DB_TIMEZONE` on cPanel, check what it is now (cPanel → phpMyAdmin → SQL tab):

```sql
SELECT @@session.time_zone, @@global.time_zone, NOW(), UTC_TIMESTAMP();
```

- If `NOW()` is **7 hours ahead of** `UTC_TIMESTAMP()` → cPanel is already on WIB.
  Setting `DB_TIMEZONE=+07:00` is a safe no-op. ✅
- If `NOW()` **equals** `UTC_TIMESTAMP()` → cPanel runs on UTC. Do **not** blindly
  set `+07:00` (it would shift every displayed time by 7h). Flag it and we adjust
  the value / migrate the data deliberately.

Record the result here: cPanel session tz = `__________`

