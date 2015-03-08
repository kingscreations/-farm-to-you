<?php
$currentDir = dirname(__FILE__);
require_once ("../root-path.php");

session_start();

if(!@isset($_SESSION['profileId'])) {
	header('Location: ../sign-in/index.php');
}

session_abort();


require_once("../php/lib/header.php");
require_once("../php/classes/profile.php");
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");


$profileId = $_SESSION['profileId'];

mysqli_report(MYSQLI_REPORT_STRICT);
$configArray = readConfig("/etc/apache2/capstone-mysql/farmtoyou.ini");
$mysqli = new mysqli($configArray['hostname'], $configArray['username'], $configArray['password'], $configArray['database']);

//$profileFromUser = Profile::getProfileByUserId($mysqli,$_SESSION['user']['id']);
//$profileId = $profileFromUser->getProfileId();

$profile = Profile::getProfileByProfileId($mysqli, $profileId);

$profileFirstname = $profile->getFirstName();
$profileLastname = $profile->getLastName();
$profilePhone = $profile->getPhone();
$profileImage = $profile->getImagePath();
$profileType = $profile->getProfileType();


?>

<script src="../js/edit-profile.js"></script>
	<div class="container-fluid container-margin-sm transparent-form">
		<div class="row">
	<?php if($profileType === "m") { ?>

	<div id="multi-menu" class="col-md-3 hidden-sm hidden-xs transparent-menu">
			<ul class="nav nav-pills nav-stacked">
				<li class="active"><a href="../edit-profile/index.php">Edit Profile</a></li>
				<li><a href="../add-store/index.php">Manage Stores</a></li>
				<li><a href="../merchant-order-list/index.php">List of Orders</a></li>
				<li><a href="../bank-account/index.php">Bank Account</a></li>
			</ul>
		</div>
		<?php	} else { ?>

		<div id="multi-menu" class="col-md-3 hidden-sm hidden-xs transparent-menu">
			<ul class="nav nav-pills nav-stacked">
				<li class="active"><a href="../edit-profile/index.php">Edit Profile</a></li>
				<li><a href="../client-order-list/index.php">List of Orders</a></li>
				<li class="disabled"><a href="#">Account Settings</a></li>
			</ul>
		</div>
<?php } ?>


	<div class="dropdown hidden-lg hidden-md" style="position:relative">
		<a href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Menu<span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li class="active"><a href="../edit-profile/index.php">Edit Profile</a></li>
			<li><a href="../add-store/index.php">Manage Stores</a></li>
			<li><a href="../merchant-order-list/index.php">List of Orders</a></li>
			<li class="disabled"><a href="#">Account Settings</a></li>
		</ul>
	</div>

<!--Form to edit a profile-->
<!--<div class="container-fluid">-->

<div class="col-md-9">



	<form id="editProfile" class="form-inline transparent-form" method="post" action="../php/forms/edit-profile-controller.php" enctype="multipart/form-data">
		<h2>Edit Profile</h2>
		<?php echo generateInputTags(); ?>
		<div class="form-group">
			<label for="inputFirstname">First Name:</label>
			<input type="text" class="form-control" id="inputFirstname" name="inputFirstname" placeholder="<?php echo $profileFirstname ?>" value="<?php echo $profileFirstname ?>">
		</div>

		<br>

		<div class="form-group">
			<label for="inputLastname">Last Name:</label>
			<input type="text" class="form-control" id="inputLastname" name="inputLastname" placeholder="<?php echo $profileLastname ?>" value="<?php echo $profileLastname ?>">
		</div>

		<br>

		<div class="form-group">
			<label for="inputPhone">Phone Number:</label>
			<input type="tel" class="form-control" id="inputPhone" name="inputPhone" placeholder="<?php echo $profilePhone ?>" value="<?php echo $profilePhone ?>">
		</div>

		<br>

		<div class="form-group">
			<label for="inputImage">Profile Image</label>
			<input type="file" class="form-control" id="inputImage" name="inputImage" value="">
		</div>

		<br>

		<div class="form-group">
			<input type="submit" class="form-control" id="inputSubmit" name="inputSubmit" value="Submit">
		</div>

		<br>
		<div class="form-group edit-product">

			<?php

			$baseUrl             = CONTENT_ROOT_URL . 'images/profile/';
			$basePath            = CONTENT_ROOT_PATH . 'images/profile/';
			$imagePlaceholderSrc = SITE_ROOT_URL. 'images/placeholder.png';
			$imageSrc            = basename($profile->getImagePath());

			// show a placeholder if the product is not associated with an image
			if(is_file($profile->getImagePath())) {
				?>
				<img class="thumbnail img-responsive" src="<?php echo $baseUrl . $imageSrc; ?>" alt="<?php echo $profileFirstname . $profileLastname; ?>"/>
			<?php } else { ?>
				<img class="thumbnail img-responsive" src="<?php echo $imagePlaceholderSrc; ?>" alt="<?php echo $profileFirstname . $profileLastname; ?>"/>
			<?php } ?>

		</div>
		<br>
		<p id="outputArea" style=""></p>

	</form>

			</div>
		</div>
	</div>
</div>
<?php

if($profileId !== false) {
	$_SESSION["profileId"] = $profileId;
}
require_once("../php/lib/footer.php")
?>