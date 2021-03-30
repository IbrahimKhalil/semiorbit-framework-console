# Semiorbit Framework CLI

#### Create Model

```sh
php sc make:model {model} {--table=1} {-c} {-u} {--case=p} {-r}
```


```sh
php sc mdl {model} {--table=1} {-c} {-u} {--case=p} {-r}
```


`model` Model name

`--table` `[optional]` related table

`--case` `p`= PascalCase `[default]`, `c`= camelCase, `s`=snake_case

`-c` clean
`-u` update
`-r` overwrite


##
#### Create Controller

```sh
php sc make:controller {ctrl} {-a} {-b} {-c} {-r}
```

```sh
php sc ctrl {ctrl} {-a} {-b} {-c} {-r}
```

`ctrl` Controller name

`-a` RestController (Api Controller)

`-b` BaseController

`-c` clean
`-r` overwrite

##
#### Create Package

```shell
php sc make:package {package} {-r} {-f}
```

```shell
php sc pkg {package} {-r} {-f}
```

`package` Package name

`-r` overwrite

'-f' create all package folders


##
##### Set Global Variables

```shell
php sc use {key} {value} {-c}
```

##
##### Generate Language File

```shell
php sc make:lang {dict} {--table=1} {--lang=0} {-c} {-u} {-r}
```

```shell
php sc lng {dict} {--table=1} {--lang=0} {-c} {-u} {-r}
```

##
##### Create All (Controller + Model + language Files)
```shell
php sc mk {name} {--table=1} {--case=p} {-r}
```

##
##### Clear Cache
```shell
php sc cache:clear {-f} {--key}
```

###### Clear Config Cache
```shell
php sc ccc
```

###### Clear Framework Cache
```shell
php sc ccf
```

###### Clear All Cache
```shell
php sc cc
```

