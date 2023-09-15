<?php


    class Cart{

        public static $table="cart";
        private static $pdo;
  
        public static function set() {
           $db=new Db();
          return self::$pdo=$db->connect();
           
        }

        public static function getItemPriceFromMenu($menuId){
            
            $sql="SELECT * FROM menus WHERE menu_id = :menuId ";
            $stmt = self::set()->prepare($sql);
            $stmt->bindParam(':menuId', $menuId, PDO::PARAM_INT);
            if($stmt->execute()){
               return $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            }
        }

        // the $item is the cart
        private static $items = array();

        public static function addItems(object $data)
        {
            $menuId = $data->menuId;
            $userId = $data->userId;
            $quantity = 1;
            $Utility = new Utility();
            if ($menuDetails = self::getItemPriceFromMenu($menuId)) {
    
                $menuId = $menuDetails['menu_id'];
                $menuName = $menuDetails['menu_name'];
                $menuPrice = $menuDetails['menu_price'];
    
                if ($menuPrice !== false) {
                    $stmt = self::set()->prepare('SELECT * FROM cart WHERE user_id = :user_id AND menu_id = :menu_id');
                    $stmt->bindParam(":user_id", $userId);
                    $stmt->bindParam(":menu_id", $menuId);
                    if ($stmt->execute()) {
                        $existingCartItem = $stmt->fetch();
    
                        if ($existingCartItem) {
                            $totalQuantity = $existingCartItem["quantity"];
    
                            // Calculate the new total price based on the updated quantity
                            $totalQuantity = $existingCartItem["quantity"] + $quantity;
                            $newTotalPrice = $menuPrice * $totalQuantity;
                            // Update the cart item with the new quantity and total price
                            $stmt = self::set()->prepare('UPDATE cart SET quantity = :totalQuantity, total_price = :totalPrice WHERE user_id = :user_id AND menu_id = :menu_id');
    
                            $stmt->bindParam(":user_id", $userId);
                            $stmt->bindParam(":menu_id", $menuId);
                            $stmt->bindParam(":totalPrice", $newTotalPrice);
                            $stmt->bindParam(":totalQuantity", $totalQuantity);
                            if ($stmt->execute()) {
    
                                $newItemsInCart = self::$items = array(
                                    "menuId" => $menuId,
                                    "menuName" => $menuName,
                                    "menuPrice" => $menuPrice,
                                    "quantity" => $totalQuantity,
                                    "totalPrice" => $newTotalPrice
                                );
    
                                $Utility->response(true, "Food item updated in the cart. New total price .$newTotalPrice.", $newItemsInCart);
                            }
                        } else {
                            $totalPrice = $menuPrice * $quantity;
                            // Insert the new cart item
                            $stmt = self::set()->prepare('INSERT INTO cart (user_id, menu_id, menu_name, unit_price, quantity, total_price) VALUES (?, ?,?, ?,?, ?)');
                            $stmt->execute([$userId, $menuId, $menuName, $menuPrice, $quantity, $totalPrice]);
    
                            // if the id doesn't exist, insert a new array containing the price of the food from the menu table and set quantity equals to one
                            $itemsInCart = self::$items = array(
                                "menuId" => $menuId,
                                "menuName" => $menuName,
                                "menuPrice" => $menuPrice,
                                "quantity" => $quantity,
                                "totalPrice" => $totalPrice
                            );
    
                            $Utility->response(true, 'Item added to cart.', $itemsInCart);
                        }
                    } else {
                        // Calculate the total price for the new cart item
                        $Utility->response(false, "Unable to retrieve details", null);
                    }
                }
            } else {
    
                $Utility->response(false, "Food item not found", null);
            }
        }
      
        public static function removeItem(object $data) {
            $menuId = $data->menuId;
            $userId = $data->userId;
            $quantity = 1; // You can adjust this if needed
    
            $Utility = new Utility();
    
            $stmt = self::set()->prepare('SELECT * FROM cart WHERE user_id = :user_id AND menu_id = :menu_id');
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":menu_id", $menuId);
    
            if ($stmt->execute()) {
                $existingCartItem = $stmt->fetch();
    
                if ($existingCartItem) {
                    $currentQuantity = $existingCartItem["quantity"];
                    $menuPrice = $existingCartItem["unit_price"];
                    
                    if ($currentQuantity > $quantity) {
                        // Calculate the new total price based on the updated quantity
                        $newQuantity = $currentQuantity - $quantity;
                        $newTotalPrice = $menuPrice * $newQuantity;
    
                        // Update the cart item with the new quantity and total price
                        $stmt = self::set()->prepare('UPDATE cart SET quantity = :totalQuantity, total_price = :totalPrice WHERE user_id = :user_id AND menu_id = :menu_id');
                        
                        $stmt->bindParam(":user_id", $userId);
                        $stmt->bindParam(":menu_id", $menuId);
                        $stmt->bindParam(":totalPrice", $newTotalPrice);
                        $stmt->bindParam(":totalQuantity", $newQuantity);
    
                        if ($stmt->execute()) {
                            
                            $ItemsInCart=array(
                                "menuId"=>$menuId,  
                                "quantity"=>$newQuantity,
                                "totalPrice"=>$newTotalPrice
                            );

                            $Utility->response(true, "Item removed from cart.", $ItemsInCart);
                        } else {
                            $Utility->response(false, "Failed to update cart item.", null);
                        }
                    } elseif ($currentQuantity === $quantity) {
                        // Quantity is 1, so remove the item from the cart
                        $stmt = self::set()->prepare('DELETE FROM cart WHERE user_id = :user_id AND menu_id = :menu_id');
                        
                        $stmt->bindParam(":user_id", $userId);
                        $stmt->bindParam(":menu_id", $menuId);
    
                        if ($stmt->execute()) {

                            $Utility->response(true, "Item removed from cart.", null);
                        } else {
                            $Utility->response(false, "Failed to remove cart item.", null);
                        }
                    } else {
                        $Utility->response(false, "Quantity cannot be less than 1.", null);
                    }
                } else {
                    $Utility->response(false, "Cart item not found.", null);
                }
            } else {
                $Utility->response(false, "Unable to retrieve cart item.", null);
            }
        }
        

        public static function displayCart($userId)
        {
            $Utility = new Utility();
            $totalPrice = 0;
            $cartItemsArray = []; // Initialize an empty array to store cart items
        
            $stmt = self::set()->prepare('SELECT * FROM cart WHERE user_id = :user_id');
            $stmt->bindParam(":user_id", $userId);
        
            if ($stmt->execute()) {
                $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                if ($cartItems) {
                    foreach ($cartItems as $cartItem) {
                        $menuId = $cartItem['menu_id'];
                        $menuName = $cartItem['menu_name'];
                        $menuPrice = $cartItem['unit_price'];
                        $quantity = $cartItem['quantity'];
                        $itemTotalPrice = $cartItem['total_price'];
        
                        // Create an array for each cart item
                        $cartItemArray = [
                            'menuId' => $menuId,
                            'menuName' => $menuName,
                            'menuPrice' => $menuPrice,
                            'quantity' => $quantity,
                            'totalPrice' => $itemTotalPrice,
                        ];
        
                        $cartItemsArray[] = $cartItemArray; // Add the cart item array to the result array
        
                        $totalPrice += $itemTotalPrice;
                    }
        
                    // Create a result array containing cart items and total price
                    $result = [
                        'cartItems' => $cartItemsArray,
                        'totalPrice' => $totalPrice,
                    ];
        
                    return $result; // Convert the result array to JSON and echo it
                } else {
                    return ['cartItems' => [], 'totalPrice' => 0]; // Return an empty cart and total price if the cart is empty
                }
            } else {
                $Utility->response(false, "Unable to retrieve cart items.", null);
            }
        }

        public static function deliveryDetails(object $data) {
            $userId = $data->userId;
            $street_address = $data->street_address;
            $house_number = $data->house_number;
            $label = $data->label;
        
            if ($label === 'other') {
                $label = $data->customLabel ?? 'other'; // Use custom label if provided, else use "other"
            }
        
            try {
                $pdo = self::set(); // Assuming self::set() returns a PDO instance
        
                // Insert delivery information into the database using named placeholders
                $sql = "INSERT INTO delivery_info (user_id, street_address, house_number, label) VALUES (:userId, :street_address, :house_number, :label)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt->bindParam(':street_address', $street_address, PDO::PARAM_STR);
                $stmt->bindParam(':house_number', $house_number, PDO::PARAM_STR);
                $stmt->bindParam(':label', $label, PDO::PARAM_STR);
        
                if ($stmt->execute()) {
                    return true;
                } else {
                    echo "Error: " . $stmt->errorInfo()[2];
                    return false;
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
                return false;
            }
        }
        


        public static function calculateTotalFees(object $data)
        {
            $userId = $data->userId;
        
            // Retrieve cart data and subtotal
            $outcome = self::displayCart($userId);
            $subtotal = $outcome['totalPrice'];
            $foodItemsCount = count($outcome['cartItems']);
        
            // Define your rates (you can adjust these values as needed)
            $deliveryFeeRate = 5; // $5 delivery fee
            $vatRate = 0.1; // 10% VAT (0.1)
            $counterChargeRate = 2; // $2 counter charges
        
            // Check if 10 or more food items were purchased and apply a discount if applicable
            $discountRate = ($foodItemsCount >= 10) ? 1 : 0; // 1% discount if 10 or more food items
        
            // Calculate the food purchase discount (1% of subtotal)
            $foodPurchaseDiscount = $subtotal * ($discountRate / 100);
        
            // Apply the discount to the subtotal
            $subtotal -= $foodPurchaseDiscount;
        
            // Calculate the delivery fee
            $deliveryFee = $subtotal * ($deliveryFeeRate / 100);
        
            // Calculate the VAT
            $vat = $subtotal * $vatRate;
        
            // Calculate the counter charges
            $counterCharges = $counterChargeRate;
        
            // Calculate the total fees payable
            $totalFees = $deliveryFee + $vat + $counterCharges;
        
            return [
                'deliveryFee' => $deliveryFee,
                'vat' => $vat,
                'counterCharges' => $counterCharges,
                'foodPurchaseDiscount' => $foodPurchaseDiscount,
                'totalFees' => $totalFees,
            ];
        }
         

        public  static function  processPayment(object $data) {
            // Extract data from the input
            $reference = $data->reference;
            $userId = $data->userId;
            $Utility=new Utility();

            // Initialize Paystack API settings
            $paystackApiUrl = "https://api.paystack.co/transaction/verify/$reference";
    
            // Initialize cURL session
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $paystackApiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer sk_test_966e0a1be5975c1ecda1fbcebfb029858b6a654b ",
                    "Cache-Control: no-cache",
                ),
            ));
    
            // Execute the cURL request
            $response = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
    
            if ($response) {
                $result = json_decode($response);
    
                if ($result->status === true && $result->data->status === 'success') {
                    $amount = $result->data->amount / 100;
                    $status = $result->data->status;
                    $reference = $result->data->reference;
                    $fname = $result->data->customer->first_name;
                    $lname = $result->data->customer->last_name;
                    $fullname = $lname . " " . $fname;
                    $mail = $result->data->customer->email;
                    date_default_timezone_set('Africa/Lagos');
                    $paid_time = $result->data->paid_at;
                    $timestamp = time();
                    $dateString = date('Y-m-d H:i:s', $timestamp);
                    $trackingID=$Utility->randomPass(10);
    
                    $db = new Db();
                    $pdo = $db->connect();
                    $sql = "INSERT INTO `orders` (`user_id`, `status`, `amount_paid`, `reference`, `fullname`, `mail`, `db_record_time`, `paid_at`)
                            VALUES (:user_id, :status, :amount_paid, :reference, :fullname, :mail, :timestamp, :paid_time)";
    
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
                    $stmt->bindParam(':amount_paid', $amount, PDO::PARAM_INT);
                    $stmt->bindParam(':reference', $reference, PDO::PARAM_STR);
                    $stmt->bindParam(':fullname', $fullname, PDO::PARAM_STR);
                    $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
                    $stmt->bindParam(':timestamp', $dateString);
                    $stmt->bindParam(':paid_time', $paid_time);
    
                    if ($stmt->execute()) {
                        
                        return $trackingID;
        
                    } else {
                        $Utility->response(false, "There was a problem with the database query: " . implode(", ", $stmt->errorInfo()), null);
                    }
                } else {
                    $Utility->response(false, "Payment verification failed. Please try again.", null);
                }
            } else {
                $Utility->response(false, "Error communicating with Paystack API. Please try again later.", null);
            }
        }
    }
        

   



        
        







    


?>