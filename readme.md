# Dbtool
A database structure dumping tool. It dumps tables structure, views, triggers, functions and procedures.

## Installation
1) Add Dbtool to your project using [Composer](http://getcomposer.org/):
```
$ composer require filsedla/dbtool
```

2) Create a script located somewhere inside your project, e.g. `<project_root>/tools/db/`.
 
See the [example](example/) subdirectory for two versions, an integration into a [nette-based]https://nette.org/ project,
or other project.

## Usage

Manually run the above script each time you update your project's database structure. The idea is to commit complete
database structure dumps to your VCS so that the database structure is 'versioned' together with changes in the project 
code.

