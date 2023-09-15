<?php

   class Menu {

      public static $table="menus";
      private static $pdo;

      public static function set() {
         $db=new Db();
        return self::$pdo=$db->connect();
         
      }

   
      public  static function insertMenu(object $data) {

         $fields = [
            "menu_id" => $data->menu_id,
            "menu_name" => $data->menu_name,
            "restaurant_id" => $data->restId,
            "menu_picture" => $data->menu_picture,
            "menu_description" => $data->menu_description,
            "menu_price" => $data->menu_price
         ];

         $columns = implode(", ", array_keys($fields));
         $placeholders = implode(", ", array_fill(0, count($fields), '?'));

         try {
            $stmt = self::set()->prepare("INSERT INTO ".self::$table." ($columns) VALUES ($placeholders)");
            $i = 1;
            foreach ($fields as $value) {
                  $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                  $stmt->bindValue($i, $value, $type);
                  $i++;
            }

            if( $stmt->execute()){
               return true;

            } // Execute the query

            // Return true for success, false for failure
         } catch (PDOException $e) {
            return $e->getMessage(); // Return or throw the exception message
         }
      }

      public static function getMenuDetail($restId) {
         
         $sql = "SELECT r.name AS restaurantName, m.menu_id, m.menu_picture, m.menu_name, m.menu_description, m.menu_price
                 FROM " . Restaurant::$table . " AS r
                 JOIN " . self::$table . " AS m ON r.id = m.restaurant_id
                 WHERE r.id = :restId";
         
         $stmt = self::set()->prepare($sql);
         $stmt->bindParam(':restId', $restId, PDO::PARAM_INT);
     
         $result = array();  // Initialize an array to store menu details
         
         if ($stmt->execute()) {
              
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                 $menuDetail = array(
                     'restaurantName' => $row['restaurantName'],
                     'menuId' => $row['menu_id'],
                     'menuPicture' => $row['menu_picture'],
                     'menuName' => $row['menu_name'],
                     'menuDescription' => $row['menu_description'],
                     'menuPrice' => $row['menu_price']
                 );
                 $result[] = $menuDetail;  // Append menu details to the result array
             }
         }
         
         return $result;  // Return the array containing all menu details
     }



     





   }










?>