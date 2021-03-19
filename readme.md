# 360 Life

## Installation

```bash
composer install
yarn install
yarn watch
```

copiare .env.dist su .env e modificare la configurazione del DB. Es:

```ini
DATABASE_URL=mysql://root:root@127.0.0.1:3306/360life
DATABASE_VENDOR_VERSION=5.7
```

settare i permessi:

```bash
chmod 0777 public/media -R
```

## Fixtures

Elimina il db e lo ricrea:
```bash
./batch/rebuild-dev.sh
```

Accoda i dati senza eliminare il db:
```bash
./batch/rebuild-dev.sh --append
```

## Cron

Si possono eseguire uno alla volta o assieme. Per non stampare messaggi va usata l'opzione --silent
```bash
./bin/console sinervis:cron --script=updateEditions
./bin/console sinervis:cron --script=updateTeacherAssessments
./bin/console sinervis:cron --script=updateCertificates
```

Per eseguire tutti gli script:
```bash
./bin/console sinervis:cron --script=all --silent
```
