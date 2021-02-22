<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

//we are going to use session variables so we need to enable sessions
session_start();


$emailError = $streetError = $streetnumberError = $cityError = $zipcodeError = $orderError = $noExpress = "";
$email = $street = $streetnumber = $city = $zipcode = $order = $express = "";

const max_number = 4;
const street_number = 5;

$cookieName = "saved-orders";
$expire = time() + (86400 * 30); // => 1 day * 30 = month


if (isset($_COOKIE["saved-orders"])){
    $totalValue = (float)$_COOKIE["saved-orders"];
}
else {
    $totalValue = 0;
    setcookie($cookieName,(string)$totalValue,$expire);
}


//your products with their price.

if (!isset($_GET["food"])) {

    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
} elseif ($_GET["food"] == 0) {

    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];
}
elseif ($_GET["food"] == 3) {

    $products = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5],
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3]
    ];
}
else {
    $products = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    if (empty($_POST["street"])) {
        $streetError = "* street name is required";
    } elseif (!empty($_POST["street"]) && is_numeric($_POST["street"])) {
        $streetError = "* text only";
    } else {
        $street = newInput($_POST["street"]);
        $_SESSION["street"] = $street;
    }

    if (!empty($_POST["streetnumber"]) && !is_numeric($_POST["streetnumber"])) {
        $streetnumberError = "* only numerics allowed";
    } elseif (empty($_POST["streetnumber"])) {
        $streetnumberError = "* street number is required";
    } elseif (mb_strlen($_POST["streetnumber"]) > street_number) {
        $streetnumberError = "* Street number is too long";
    } else {
        $streetnumber = newInput($_POST["streetnumber"]);
        $_SESSION["streetnumber"] = $streetnumber;
    }

    if (empty($_POST["city"])) {
        $cityError = "* city name is required";
    } elseif (!empty($_POST["street"]) && is_numeric($_POST["city"])) {
        $cityError = "* text only";
    } else {
        $city = newInput($_POST["city"]);
        $_SESSION["city"] = $city;
    }

    if (!empty($_POST["zipcode"]) && !is_numeric($_POST["zipcode"])) {
        $zipcodeError = "* only numerics allowed";
    } elseif (empty($_POST["zipcode"])) {
        $zipcodeError = "zipcode is required";
    } elseif (mb_strlen($_POST["zipcode"]) != max_number) {
        $zipcodeError = "* Needs to be 4 digits";
    } else {
        $zipcode = newInput($_POST["zipcode"]);
        $_SESSION["zipcode"] = $zipcode;
    }

    if (empty($_POST["email"])) {
        $emailError = "* email address is required";
    } elseif (!empty($_POST["email"])) {
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false) {
            $emailError = "* email address is invalid";
        } else {
            $email = newInput($_POST["email"]);
            $_SESSION["email"] = $email;
        }
    }
}

if (isset($_POST["orderButton"]) && ($email == "" || $street == "" || $streetnumber == "" || $city == "" || $zipcode == "")) {
    $orderError = "* please fill in the form completely!";
}
else {
    $order = "Your order has been taken, sit back and relax!";
}


if (isset($_POST["products"],$_POST["express_delivery"])) {
    $currentTimeExpress = time();
    $expressDelivery = 1;
    $expressSeconds = $expressDelivery * (45 * 60);
    $newTimeExpress = $currentTimeExpress + $expressSeconds;

    $express = "Thank you for choosing express delivery! Your order will arrive in" . " " . date("H:i", $newTimeExpress) . " " . "minutes";
} elseif (isset($_POST["products"])) {
    $currentTime = time();
    $deliveryHours = 2;
    $seconds = $deliveryHours * (60 * 60);
    $newTime = $currentTime + $seconds;

    $noExpress = "Your order will arrive at " . " " . date("H:i", $newTime) . " " . "Hours";
}


function newInput($input)
{
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}



function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}

// whatIsHappening()


if (isset($_POST["products"])) {
    foreach ($_POST["products"] as $i => $price) {
        $totalValue += $products[$i]["price"]*$_POST["products"][$i];
    }
    if
    (isset($_POST["express_delivery"])){
        $totalValue += $_POST["express_delivery"];
    }
    setcookie($cookieName,(string)$totalValue,$expire);
}

require 'form-view.php';