<?php


// dummy session
$currentDir = dirname(__FILE__);
require_once ("../root-path.php");

session_start();

if(!@isset($_SESSION['profileId'])) {
	header('Location: ../sign-in/index.php');
}

session_abort();


require_once("../php/lib/header.php");

// classes
require_once("../php/classes/order.php");
require_once("../php/classes/orderproduct.php");
require_once("../php/classes/checkout.php");
require_once("../php/classes/location.php");
require_once("../php/classes/product.php");

$profileId = $_SESSION['profileId'];

// credentials
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

try {
	// get the credentials information from the server and connect to the database
	mysqli_report(MYSQLI_REPORT_STRICT);
	$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
	$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

	// grab all stores by profile id in dummy session
	$orders = Order::getAllOrdersByProfileId($mysqli, $profileId);

	 echo '	<div class="container-fluid container-margin-sm transparent-form">
					<div class="row">
						<div id="multi-menu" class="col-md-3 hidden-sm hidden-xs transparent-menu">
							<ul class="nav nav-pills nav-stacked">
								<li><a href="../edit-profile/index.php">Edit Profile</a></li>
								<li class="active"><a href="../client-order-list/index.php">List of Orders</a></li>
								<li class="disabled"><a href="#">Account Settings</a></li>
							</ul>
						</div>';


	// create table of existing stores
	if($orders !== null) {
		sort($orders);
		foreach($orders as $order) {
			$orderId = $order->getOrderId();
			$orderProducts = OrderProduct::getAllOrderProductsByOrderId($mysqli, $orderId);
			$checkout = Checkout::getCheckoutByOrderId($mysqli, $orderId);
			$checkoutDate = $checkout->getCheckoutDate();
			$formattedDate = $checkoutDate->format("m/d/Y - H:i:s");
			$checkoutFinalPrice = number_format((float)$checkout->getFinalPrice(), 2, '.', '');

			echo '<table class="table table-responsive">';
			echo '<tr>';
			echo '<th>Order #'.$orderId .'</th>';
			echo '<th></th>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>Date</td>';
			echo '<td>'.$formattedDate .'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<td>Products</td>';
			echo '<td>' ;
			foreach($orderProducts as $orderProduct) {
				$productId = $orderProduct->getProductId();
				$orderProductQuantity = $orderProduct->getProductQuantity();
				$locationId = $orderProduct->getLocationId();
				$product = Product::getProductByProductId($mysqli, $productId);
				$productName = $product->getProductName();
				$productWeight = $product->getProductWeight();
				$productPrice = number_format((float)$product->getProductPrice(), 2, '.', '');
				$location = Location::getLocationByLocationId($mysqli, $locationId);
				$locationName = $location->getLocationName();

				echo "$orderProductQuantity order of $productWeight lbs. of $productName for  $$productPrice at $locationName location";
				echo '<br>';
			}
			echo '</td>' ;
			echo '</tr>';

			echo '<tr>';
			echo '<td>Final Price</td>';
			echo '<td>$'.$checkoutFinalPrice.'</td>';
			echo '</tr>';
			echo '</table>';
			echo '</div>';
			echo '</div>';
		}
//		echo '</table>';

	} else {
		echo '<h4>No orders found.</h4>';
		echo '</div>';
		echo '</div>';

	}

} catch(Exception $exception) {
	echo "<p class=\"alert alert-danger\">Exception: " . $exception->getMessage() . "</p>";
}

?>
