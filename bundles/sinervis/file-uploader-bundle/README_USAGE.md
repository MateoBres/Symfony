Usage
============
### Available features in Sinervis File Annotation
```php
Important: All the annotations are cached. So whenever you change them later make sure the cache is cleared.
```
<br>
<br>

Add following annotation to the class where you are going to add the uploadble field.
```php
@SinervisUploadable()
```
add following annotion to the file field/s
```php
@SinervisUploadableField(
    destination="/data",
    maxFileSize="8",
    allowedMimeTypes={"jpeg", "png"},
    permissions={
       "voter": "App\Security\AdminFlock\AdminVoter::VIEW",
       "roles": {"ROLE_ADMIN", "ROLE_VACAPUTANGA"}
    }
)
```
Attention! 
```
- do not add 'inversedBy' property to the relationship

- maxileSize is in 'MB'

- permissions wil work as an 'OR'. In above case if the user has
    - the give voter permission OR
    - has ROLE_ADMIN OR ROLE_VACAPUTANGA
  will be able to download the file.
```

### E.g.
Uploadable entity
```php
/**
 * @ORM\Entity()
 * @SinervisUploadable()
 */
```
Single file field.
```php
/**
 * @ORM\ManyToOne(targetEntity="Sinervis\FileUploaderBundle\Entity\SvFile", cascade={"all"})
 * @SinervisUploadableField(destination="/data")
 */
```
Multiple file field.
```php
/**
 * @ORM\ManyToMany(targetEntity="Sinervis\FileUploaderBundle\Entity\SvFile", cascade={"all"})
 * @SinervisUploadableField(destination="/data/equipment")
 */
```
make sure to verify that file is not null in add method.
```php
public function addTestFile(?SvFile $testFile = null)
{
    if ($testFile) {
        $this->testFiles[] = $testFile;
    }

    return $this;
} 
```

### How to add file field in forms.

Single file field.
```yaml
->add('testFile', SinervisFileType::class, [
    'label' => 'My sv file',
    'required' => false,
])
```

Multiple file field.<br>
Make sure `'block_prefix' => 'sv_file_collection'` line is added.
```yaml
->add('testFiles', CollectionType::class, [
    'label' => 'Wow',
    'entry_type' => SinervisFileType::class,
    'allow_add' => true,
    'allow_delete' => true,
    'by_reference' => false,
    'block_prefix' => 'sv_file_collection',
    'attr' => [
        'add_more_button_label' => 'Nuovo File'
    ]
])
```

### Render file fields in show.
single file `{{ sinervis_file(svFile) }}` <br>
multiple files `{{ sinervis_files(svFile) }}`