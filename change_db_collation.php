<?php 

$tables = fetch_all("SHOW TABLES;");
  
run_query("ALTER DATABASE `arht_db` CHARACTER SET utf8 COLLATE utf8_general_ci;"); 
  
foreach ($tables as $table) { 
    $table_name = $table['Tables_in_arht_db']; 
     
    if (!empty($table_name)) {  
        run_query("ALTER TABLE `{$table_name}` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;"); 
    } 
} 
  
?> 