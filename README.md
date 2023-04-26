# oc-demo-plugin
Polilla Demo plugin for testing SortableRelation in October V2 and OctoberCMS v3 


I start a fresh Demo Plugin in order to clean all the business logic and keep it as simple as I can for the test

The models of the plugin will be **Invoices** and **Items** with a Many to Many relationship using a pivot table


**ITEMS**

= Model = 


```
<?php namespace Polilla\Demo\Models;

use Model;

class Item extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'polilla_demo_items';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsToMany = [
        'invoices' => [
            \Polilla\Demo\Models\Invoice::class,
            'table' => 'polilla_demo_invoice_item',
        ]
    ];
}
```

= Table =

```
class CreateItemsTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_items', function (Blueprint $table) {
            $table->increments('id');
            $table->text('item');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_items');
    }
}
```

= Columns YAML =


```
columns:
    id:
        label: ID
        searchable: true
    item:
        label: Item
```

= Fields YAML =

```
fields:
    id:
        label: ID
        disabled: true
    item:
        label: Item Name
        type: text
```


**Invoice**

= Model = 


```
<?php namespace Polilla\Demo\Models;

use Model;

class Invoice extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SortableRelation;

    public $table = 'polilla_demo_invoices';


    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    
    public $belongsToMany = [
        'items' => [
            \Polilla\Demo\Models\Item::class,
            'table' => 'polilla_demo_invoice_item',
            'pivotSortable' => 'sort_order',
        ]
    ];
}
```

= Table = 


```
class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->text('invoice');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_invoices');
    }
}
```

= Fields YAML = 


```
fields:
    id:
        label: ID
        disabled: true
    invoice:
        label: Invoice
        type: text
        span: full
    items:
        label: Items in the invoice
        type: relation
        nameFrom: item
```
        
= Columns YAML =


```
columns:
    id:
        label: ID
        searchable: true
    invoice:
        label: Invoice
    items:
        relation: items
        select: item
```
        

**Pivot Table**


```
class CreateItemsInvoiceTable extends Migration
{
    public function up()
    {
        Schema::create('polilla_demo_invoice_item', function(Blueprint $table)
        {
            $table->integer('invoice_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->primary(['invoice_id', 'item_id']);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('polilla_demo_invoice_item');
    }
}
```

In the backend, When go to Invoice > Create invoice I get this error

> SQLSTATE[42S22]: Column not found: 1054 Unknown column 'polilla_demo_invoice_item.sort_order' in 'order clause' (SQL: select * from `polilla_demo_items` order by `polilla_demo_invoice_item`.`sort_order` asc)

**Testing** 

If you don't specify the pivot table in the model, October will reach the relation in a table named *invoice_item*, even expected I think this is a potential problem for compatibility with other plugins, but it works. So, I create a table named *invoice_item*

```
<?php namespace Polilla\Demo\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateItemsTable Migration
 */
class CreateInvoiceItem extends Migration
{
    public function up()
    {
        Schema::create('invoice_item', function(Blueprint $table)
        {
            $table->integer('invoice_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->primary(['invoice_id', 'item_id']);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_item');
    }
```

That's great! now I can create an Invoice in the backend form and the relationship is displayed as a checkbox group. I can add Items to the Invoice too and these relations are saved in the pivot table *invoice_item* with the right *sort_order* value


The problem with this scenario is that the relation manager is not displayed as expected in the Invoices view. It displays as a simple checkbox list and not as a drag and drop to modify the sort_order.

This is the Invoices Controller. It have the RelationController implemented

```
<?php namespace Polilla\Demo\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Invoices Backend Controller
 */
class Invoices extends Controller
{
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class,
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    public $relationConfig = 'config_relation.yaml';

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Polilla.Demo', 'demo', 'invoices');
    }
}
```

This is the config_relation.yaml

```
items:
    label: Invoice Items
    list: $/polilla/demo/models/item/columns.yaml
    form: $/polilla/demo/models/item/fields.yaml
    structure:
        showReorder: true
        showTree: false
```


###Conclusions: 

Using the belongsToMany relation without overriding the name of the join table + SortableRelation trait

```
<?php namespace Polilla\Demo\Models;

use Model;

/**
 * Invoices Model
 */
class Invoice extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SortableRelation;

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsToMany = [
        'items' => [
            \Polilla\Demo\Models\Item::class,
            'pivotSortable' => 'sort_order',
        ]
    ];
}
```

- In October V2.2 the relation manager do not have drag and drop and displays as a group of checkboxes

- In October v3 The relation manager displays drag and drop interface  but it doesn't keep the desired order. You can move up and down the items but always returns to the original position.

Tested in: 
- Opera 97.0.47
- Firefox 111.02
- Chrome 112.0.56
- Safari 16.4 (in this browser the drag and drop feature do not works)


Overriding the join table name for many to many relation + use SortableRelation trait

```
<?php namespace Polilla\Demo\Models;

use Model;

/**
 * Invoices Model
 */
class Invoice extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SortableRelation;

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsToMany = [
        'items' => [
            \Polilla\Demo\Models\Item::class,
            'table' => 'polilla_demo_invoice_item',
            'pivotSortable' => 'sort_order',
        ]
    ];
}
```


- In October v2.2 You get the Mysql Error described above

- In October v3 everything goes fine excepts in Safari

Tested in: 
- Opera 97.0.47
- Firefox 111.02
- Chrome 112.0.56
- Safari 16.4 (in this browser the drag and drop feature do not works)

I published the demo plugin code in my GitHub if you what to check it.  I hope to be clear (English is not my native language) and contribute in something, may be I'm implementing the relation wrong.

Have a nice day and greatings from Mexico
