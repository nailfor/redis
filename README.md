# PHP Redis client for Laravel

Redis client for Eloquent ORM

## Features
All models are inherited from Illuminate\Database\Eloquent\Model so most methods work natively

### Model Supports

| Method | Is Working |
| --- | :---: |
| CURD | Yes |
| Condition Select | id only |
| filling | yes |
| Limit | Not yet |
| Chunking | Not yet |
| Transaction | Not yet |
| Insert a lot of data | Not yet |
| Delete a lot of data | Not yet |
| Update a lot of data | Not yet |
| Relationship | Yes |

### Key Supports
| Model type | Constant |
| --- | :---: |
| HASH | TYPE_HASH |
| SET | TYPE_SET |

### Key structure

Sample key structure for a Redis model in Laravel:

`{config.redis.options.prefix}{model_table_name|class_name}:{primary_key}`

- model_table_name: The name of the current model table which set like 'protected $table = "name"'.
- primary_key: The primary key of the model (id).

Example key:

`rdb_product:1`

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require nailfor/redis
```
or add

```json
"nailfor/redis" : "*"
```
to the require section of your application's `composer.json` file.

## Configure

Add config/app.php

```php
    'providers' => [
        ...
        nailfor\Redis\RedisServiceProvider::class,

```
and config/database.php
```php
    'connections' => [
        ...
        'redis' => [ //the name of connection in your models(default)
            'driver'    => 'redis',
        ],

```

## Usage

### Models

```
├── DbProduct.php
├── RdbBrand.php
├── RdbProduct.php
└── User.php
```

DbProduct is a regular Eloquent model.
```php
<?php

namespace App\Models;

use App\Models\DbCategory;
use App\Models\RdbBrand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DbProduct extends Model
{
    protected $table = 'product';

    //sql to sql relationship
    public function Category(): BelongsTo 
    {
        return $this->belongsTo(DbCategory::class, 'category_id');
    }

    //sql to redis relationship
    public function Brand(): HasOne
    {
        return $this->hasOne(RdbBrand::class, 'brand_id');
    }
}

```

RdbProduct is a redis cache for DbProduct
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use nailfor\Redis\Eloquent\Model;

class RdbProduct extends Model
{
    protected $table = 'product';

    protected $fillable = [
        'id',
        'name',
        'article',
        'brand_id',
        'category_id',
    ];

    //Since the model type is HSET, all fields are stored as a string.
    protected $casts = [
        'id' => 'integer',
        'brand_id' => 'integer',
    ];

    //redis to redis relationship
    public function Brand(): BelongsTo
    {
        return $this->belongsTo(RdbBrand::class, 'brand_id');
    }

    //redis to sql relationship
    public function Category(): BelongsTo
    {
        return $this->belongsTo(DbCategory::class, 'category_id');
    }
}

```

RdbBrand some Redis model for Brands
```php
<?php

namespace App\Models;

use nailfor\Redis\Eloquent\Model;

class RdbBrand extends Model
{
    //without var $table the key will be "rdb_brand"
}

```
### Insert

```php
    $product = DbProduct::find(1);

    $db              = new RdbProduct();
    $db->id          = $product->id;
    $db->name        = $product->name;
    $db->article     = $product->article;
    $db->brand_id    = $product->brand_id;
    $db->category_id = $product->category_id;
    $db->save();
```

Or, because we set $fillable
```php
    $product = DbProduct::find(1);

    $db = new RdbProduct();
    $db->fill($product->toArray());
    $db->save();
```

### Retrieving Models
```php
    $product = RdbProduct::find(2);
    //or
    $product = RdbProduct::where('id', 2)->first();

```

```php
    //get all products
    $products = RdbProduct::with([
            'Brand',
        ])
        ->get()
    ;

    //get only id 1,2,3...
    $products = RdbProduct::with([
            'Brand',
            'Category',
        ])
        ->whereIn('id', [1,2,3])
        ->get()
    ;

    foreach($products as $product) {
        $brand      = $product->Brand;      //relation to Redis model RdbBrand
        $category   = $product->Category;   //relation to SQL model DbCategory

        //of course u can modify this models here
        $brand->type = 'sometype';
        $brand->save();
    }

```


## Credits

- [nailfor](https://github.com/nailfor)

License
-------

The GNU License (GNU). Please see [License File](LICENSE.md) for more information.
