![PHP Composer](https://github.com/jeyroik/extas-repositories-fields-uuid/workflows/PHP%20Composer/badge.svg?branch=master&event=push)
![codecov.io](https://codecov.io/gh/jeyroik/extas-repositories-fields-uuid/coverage.svg?branch=master)

# Описание

Allow to auto-fill fields on create with uuid.

# Usage

1. Add plugin for your repo create stage.
2. Install plugin.
3. Place field marker (see below) into field value.

# Applicable field markers

- `@uuid6`
- `@uuid4`
- `@uuid5.<namespace>.<name>`

# Example

Prepare repository + item class.
```php
namespace example;

use extas\components\repositories\Repository;
use extas\components\Item;
use extas\components\THasId;

class Example extends Item
{
    use THasId;

    public function getUuid()
    {
        return $this->config['uuid'] ?? '';
    }

    protected function getSubjectForExtension() : string
    {
        return 'example';
    }
}

class ExampleRepo extends Repository
{
    protected string $name = 'example';
    protected string $itemClass = Example::class;
}
```

extas.json
```json
{
  "plugins": [
    {
      "class": "extas\\components\\plugins\\repositories\\PluginUUidField",
      "stage": "extas.example.create.before"
    }
  ]
}
```

Usage
```php
use example\Example;
use example\ExampleRepo;

$example = new Example(['uuid' => '@uuid4']);
$repo = new ExampleRepo();
$created = $repo->create($example);
echo $created->getUuid(); // something like af29a3f4-f865-3a4a-8a87-dc8dc0b813cr
```

# Notice

Your repository should allow `create before stage` (this is by default).

See `extas\components\repositories\Repository::isAllowCreateBeforeStage` property for details.
