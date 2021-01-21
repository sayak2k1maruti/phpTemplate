<h1>All mySQL code to setUp DAtabase</h1>
<br/><br/>

<h2>accounts-rj : <i>holds all accounts information</i></h2>

```sql
CREATE TABLE `accounts-rj` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY(`id`)
);
```

<h2>personalDetails</h2>

```sql
CREATE TABLE `personalDetails` (
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `mobile_number` bigint(100) NOT NULL,
  `city` varchar(225) DEFAULT NULL,
  `address` text,
  PRIMARY KEY(`entry_id`)
);
```

<h2>academicDetails</h2>

```sql

CREATE TABLE `academicDetails` (
  `entry_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(25) DEFAULT NULL,
  `stream` varchar(25) DEFAULT NULL,
  `targetYear` int(11) DEFAULT NULL,
  PRIMARY KEY(`entry_id`)
);
```

<h2>profilePic</h2>

```sql
CREATE TABLE `profilePic` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_url` text,
  PRIMARY KEY(`id`)
);
```

<hr>
<br/><br/>
<a href = './pdo.php'><div>
<h1>Also Don't forget to upload pdo.php</h1>

```php
<?php
	$pdo = new PDO('mysql:host=sql210.epizy.com;port=3306;dbname=epiz_26398620_Portfolio', 'epiz_26398620', '6ToxhKwbAM2m');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
```
</div></a>


@sayak