<?php
 

 class Restaurant extends Account {

        public static $table = 'restaurants'; // Assuming your restaurant table name is 'restaurants'
      
        protected static $pdo;

        private static function setup()
        {
            if (!self::$pdo) {
                $db = new Db();
                self::$pdo = $db->connect();
            }

            parent::setPdo(self::$pdo);
            parent::setTable(self::$table);
        }

        public static function checkIfRestaurantMailExists($mail) {
            self::setup();
            return parent::checkIfMailExists($mail);
        }

        public static function registerRestaurant($data) {
            self::setup();
            
            return parent::register($data);
        }

        public static function loginRestaurant($data) {
            self::setup();
            return parent::login($data);
        }

        public static function getRestaurantData($restId) {
            self::setup();
            return parent::getData($restId);
        }

        public static function resetRestaurantPassword($newpword, $mail) {
            self::setup();
            return parent::resetPassword($newpword, $mail);
        }

        public static function restaurantForgotPword($data)
        {
            self::setup();
            return parent::forgotPword($data);
        }


        public static function haversineDistance($userLat, $userLon, $restLat, $restLon) {
            $earthRadius = 6371; // Radius of the Earth in kilometers
            $deltaLat = deg2rad($restLat - $userLat);
            $deltaLon = deg2rad($restLon - $userLon);
            $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
                 cos(deg2rad($userLat)) * cos(deg2rad($restLat)) *
                 sin($deltaLon / 2) * sin($deltaLon / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = $earthRadius * $c;
            return $distance;
        }

        public static function getAllRestaurants() {
            self::setup();
            $sql = "SELECT * FROM restaurants";
            $stmt = self::$pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
    
        public static function getNearByRestaurant($userId) {
            // Get user's location data
           
            $userData = User::getUserData($userId);
        
            $userLat = $userData['lat'];
            $userLon = $userData['lon'];
            $distanceThreshold = 5; // in kilometers
            $closestRestaurants=[];

           $allRestaurant= self::getAllRestaurants();

            foreach ($allRestaurant as $restuarant){
                $restLat=$restuarant["lon"];
                $restLon=$restuarant["lat"];
                 
                $distance=self::haversineDistance($userLat,$userLon,$restLat,$restLon);
                if($distance<=$distanceThreshold){
                    $restuarant['distance']=$distance;
                    $closestRestaurants[]=$restuarant;
    
                }

            }

            usort($closestRestaurants, function($a, $b) {
                return $a['distance'] - $b['distance'];
            });
    
            return $closestRestaurants;




        }

    

        public static function editRestaurantData(object $data) {
            self::setup();
            $restId = $data->restId;
            $new_address = $data->address;
            $new_image = $data->image;

            $restaurant_category_id = $data->restaurant_category_id;
            $response=array();
            $sql = "UPDATE " . self::$table . " SET image = :image, address = :address, restaurant_category_id=:restaurant_category_id WHERE id = :restId";
            $stmt = self::$pdo->prepare($sql);
            $stmt->bindParam(':image', $new_image);
            $stmt->bindParam(':address', $new_address);
            $stmt->bindParam(':restaurant_category_id', $restaurant_category_id);
            $stmt->bindParam(':restId', $restId);
            
            if ($stmt->execute()) {
                $response=array(
                   "id" => $restId,
                    "address"=>$new_address,
                    'image'=>$new_image,
                    "restaurant_category_id"=>$restaurant_category_id
                );
                return $response; // Return true to indicate successful update
            } else {
                return false; // Return false to indicate update failure
            }
        }

        public static function getAllFoodCategories() {
            self::setup();
            // Query to fetch all food categories
            $sql = "SELECT * FROM restaurant_category";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
    
            return $foodCategories = $stmt->fetchAll(PDO::FETCH_ASSOC);
           
        }

        public static function getFoodCategoryWithHighestLikes() {
            self::setup();
        
            $response = array();
        
            // Query to get all food categories ordered by the number of likes
            $sql = "SELECT fc.category_id, fc.category_name, COALESCE(SUM(rl.value), 0) AS total_likes
                    FROM restaurant_category fc
                    LEFT JOIN restaurants r ON r.restaurant_category_id = fc.category_id
                    LEFT JOIN restaurant_likes rl ON r.id = rl.restaurant_id
                    GROUP BY fc.category_id, fc.category_name
                    ORDER BY total_likes DESC";
        
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute();
        
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if ($result) {
                $response = $result;
            }
        
            return $response;
        }
        
        
        

        public static function toggleLikeDislike($restId, $userId, $value) {
            self::setup();
        
            $response = array();
        
            // Check if the user has already liked/disliked the restaurant
            $checkLikedDislikedSql = "SELECT COUNT(*) as count FROM restaurant_likes WHERE restaurant_id = :restaurant_id AND user_id = :user_id";
            $stmtCheckLikedDisliked = self::$pdo->prepare($checkLikedDislikedSql);
            $stmtCheckLikedDisliked->bindParam(":restaurant_id", $restId);
            $stmtCheckLikedDisliked->bindParam(":user_id", $userId);
            $stmtCheckLikedDisliked->execute();
            $hasLikedDisliked = $stmtCheckLikedDisliked->fetch(PDO::FETCH_ASSOC)["count"];
        
            if ($hasLikedDisliked === 0) {
                // Insert a new like/dislike into the 'restaurant_likes' table
                $status = ($value === 1) ? "liked" : "unliked";
                $insertSql = "INSERT INTO restaurant_likes (restaurant_id, user_id, value, status) VALUES (:restaurant_id, :user_id, :value, :status)";
                $stmtInsert = self::$pdo->prepare($insertSql);
                $stmtInsert->bindParam(":restaurant_id", $restId);
                $stmtInsert->bindParam(":user_id", $userId);
                $stmtInsert->bindParam(":value", $value);
                $stmtInsert->bindParam(":status", $status);
                $stmtInsert->execute();
        
                $response["status"] = $status;
            } else {
                // Remove the like/dislike from the 'restaurant_likes' table
                $deleteSql = "DELETE FROM restaurant_likes WHERE restaurant_id = :restaurant_id AND user_id = :user_id";
                $stmtDelete = self::$pdo->prepare($deleteSql);
                $stmtDelete->bindParam(":restaurant_id", $restId);
                $stmtDelete->bindParam(":user_id", $userId);
                $stmtDelete->execute();
        
                $response["status"] = "removed";
            }

            return $response;
        
            
        }
        
        

        public static function getRestaurantsWithLikes() {
            self::setup();
    
            $response = array();

    
            // Get restaurants and their like counts, ordered by like count
            $sqlRestaurantsWithLikes = "SELECT r.id, r.name, COUNT(rl.restaurant_id) AS like_count 
                                        FROM restaurants r
                                        LEFT JOIN restaurant_likes rl ON r.id = rl.restaurant_id
                                        WHERE rl.value = 1
                                        GROUP BY r.id, r.name
                                        ORDER BY like_count DESC";
    
            $stmtRestaurantsWithLikes = self::$pdo->prepare($sqlRestaurantsWithLikes);
            $stmtRestaurantsWithLikes->execute();
            $restaurantsWithLikes = $stmtRestaurantsWithLikes->fetchAll(PDO::FETCH_ASSOC);
    
            // Get the total number of registered users (you need to adjust this query based on your database structure)
            $sqlTotalUsers = "SELECT COUNT(*) as user_count FROM tblusers";
            $stmtTotalUsers = self::$pdo->prepare($sqlTotalUsers);
            $stmtTotalUsers->execute();
            $resultTotalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC);
            $totalUsers = $resultTotalUsers["user_count"];
    
            foreach ($restaurantsWithLikes as $restaurant) {
                $restaurantId = $restaurant["id"];
                $restaurantName = $restaurant["name"];
                $likeCount = $restaurant["like_count"];
    
                // Get the total number of dislikes for the restaurant
                $sqlDislikes = "SELECT COUNT(*) as dislike_count FROM restaurant_likes WHERE restaurant_id = :restaurant_id AND value = 0";
                $stmtDislikes = self::$pdo->prepare($sqlDislikes);
                $stmtDislikes->bindParam(":restaurant_id", $restaurantId);
                $stmtDislikes->execute();
                $resultDislikes = $stmtDislikes->fetch(PDO::FETCH_ASSOC);
                $dislikeCount = $resultDislikes["dislike_count"];
    
                // Calculate the like and dislike percentages
                $likePercentage = ($likeCount / $totalUsers) * 100;
                $dislikePercentage = ($dislikeCount / $totalUsers) * 100;
    
                $response[] = array(
                    "restaurant_id" => $restaurantId,
                    "restaurant_name" => $restaurantName,
                    "like_count" => $likeCount,
                    "like_percentage" => $likePercentage . '%',
                    "dislike_count" => $dislikeCount,
                    "dislike_percentage" => $dislikePercentage . '%'
                );
            }
    
            return $response;
        }



        
        
        
        
        

       
    }
?>