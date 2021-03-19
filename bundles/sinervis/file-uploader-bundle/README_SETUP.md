Installation
============

### Step 1: Copy the Bundle

Copy "sinervis" folder that contains "file-uploader-bundle" anywhere you want. 



### Step 2: Configure composer

Then, copy and paste the following code block to `composer.json` file of your project.
If "repositories" key already exists add the copy only the json object that contains 
type and url keys. 
<br><br>Make sure the path to the bundle is correctly set under 'url' key. 

```json
"repositories": [
    {
        "type": "path",
        "url": "../sinervis/file-uploader-bundle"
    }
]
```
### Step 3: Install the bundle
execute following command
```json
composer require sinervis/file-uploader-bundle:*@dev
```

check if the bundle is enabled in config/bundles.php. If not add the following line
```
Sinervis\FileUploaderBundle\SinervisFileUploaderBundle::class => ['all' => true]
```

### Step 4: Install and configure assets
execute following command
```json
php bin/console assets:install --symlink --relative
```
Make sure that you include the css and js files in your template:
```json
<link rel="stylesheet" href="/bundles/sinervisfileuploader/js/sv_file_uploader.css">
<script src="{{ asset('/bundles/sinervisfileuploader/js/sv_file_uploader.js') }}"></script>
```


### Step 5: Register the Routes

Load the bundle's routing definition in the application by creating the following file:
```yaml
config/routes/sinervis_file_uploader.yml
```
add the following content inside it
```yaml
sinervis_file_uploader:
    resource: '@SinervisFileUploaderBundle/Resources/config/routes.xml'
```
### Step 6: Create temporary table
```yaml
php bin/console doctrine:database:import vendor/sinervis/file-uploader-bundle/src/DataFixtures/sv_tmp_file.sql
```
