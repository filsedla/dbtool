# Dbtool
A MySQL database structure dumping tool. It dumps tables structure, views, triggers, functions and procedures.

## Installation
1) Add Dbtool to your project using [Composer](http://getcomposer.org/):
```
$ composer require filsedla/dbtool
```

2) Create a script located somewhere inside your project, e.g. `<project_root>/tools/db/` with the following content 
(see the [example](example/) subdirectory for a commented version).

```
$container = require __DIR__ . '/../../app/bootstrap.php';

$dbTool = $container->createInstance(\Filsedla\DbTool\DbTool::class, [
   $container->getService('database.context'),
   __DIR__
]);

$dbTool->process();
```
Note: in this example the database is dumped to the same directory where the script is located.


## Usage

Manually run the above script each time you update your project's database structure. The idea is to commit complete
database structure dumps to your VCS so that the database structure is 'versioned' together with changes in the project 
code.

