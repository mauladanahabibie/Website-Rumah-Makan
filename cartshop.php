<?php
session_start();
include_once 'koneksi.php';

// Tambahkan produk ke keranjang
if (isset($_POST['action']) && $_POST['action'] == "add") {
    $id = intval($_POST['id']);
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity']++;
    } else {
        $sql_p = "SELECT * FROM products WHERE id={$id}";
        $query_p = mysqli_query($conn, $sql_p);
        if (mysqli_num_rows($query_p) != 0) {
            $row_p = mysqli_fetch_array($query_p);
            $_SESSION['cart'][$row_p['id']] = array("quantity" => 1, "price" => $row_p['price']);
        } else {
            $message = "Product ID is invalid";
        }
    }
    // Redirect setelah operasi POST selesai
    header("Location: cartshop.php");
    exit();
}

// Hapus produk dari keranjang
if (isset($_POST['action']) && $_POST['action'] == "remove") {
    $id = intval($_POST['id']);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
}

// Perbarui jumlah produk di keranjang
if (isset($_POST['submit'])) {
    foreach ($_POST['quantity'] as $key => $val) {
        if ($val == 0) {
            unset($_SESSION['cart'][$key]);
        } else {
            $_SESSION['cart'][$key]['quantity'] = $val;
        }
    }
    // Redirect setelah operasi POST selesai
    header("Location: cartshop.php");
    exit();
}

// Kirim notifikasi ke WhatsApp
if (isset($_POST['checkout'])) {
    $message = "Order Details:\n";
    $totalprice = 0;

    foreach ($_SESSION['cart'] as $id => $details) {
        $result = mysqli_query($conn, "SELECT name FROM products WHERE id = $id");
        $product = mysqli_fetch_assoc($result);
        $message .= "{$product['name']} (x{$details['quantity']}): $" . ($details['price'] * $details['quantity']) . "\n";
        $totalprice += $details['price'] * $details['quantity'];
    }
    $message .= "Total Price: $" . $totalprice;

    $whatsappAPIUrl = "https://wa.me/+6289509400110?text=" . urlencode($message);
    echo "<script>window.location.href='$whatsappAPIUrl';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart Shop</title>
    <link rel="stylesheet" href="css/cartshop.css">
</head>
<body>

<h1>Cart Shop</h1>

<div class="cart">
    <h2>Your Cart</h2>
    <form method="post" action="cartshop.php">
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
            <?php
            $totalprice = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $id => $details) {
                    $result = mysqli_query($conn, "SELECT name FROM products WHERE id = $id");
                    $product = mysqli_fetch_assoc($result);
                    $subtotal = $details['quantity'] * $details['price'];
                    $totalprice += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo "RP " . htmlspecialchars($details['price']); ?></td>
                <td><input type="number" name="quantity[<?php echo $id; ?>]" value="<?php echo htmlspecialchars($details['quantity']); ?>" min="1" /></td>
                <td><?php echo "RP " . htmlspecialchars($subtotal); ?></td>
                <td>
                    <form method="post" action="cartshop.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="action" value="remove">
                        <button type="submit">Remove</button>
                    </form>
                </td>
            </tr>
            <?php
                }
            ?>
            <tr>
                <td colspan="5"><strong>Total Price: <?php echo "RP " . htmlspecialchars($totalprice); ?></strong></td>
            </tr>
            <?php
            } else {
            ?>
            <tr>
                <td colspan="5">No products in your cart</td>
            </tr>
            <?php
            }
            ?>
        </table>
        <input type="submit" name="submit" value="Update Cart" />
        <input type="submit" name="checkout" value="Checkout and Notify via WhatsApp" />
    </form>
</div>

<div class="products">
    <h2>Products</h2>
    <?php
    $sql = "SELECT * FROM products";
    $query = mysqli_query($conn, $sql);
    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_array($query)) {
    ?>
    <div class="product">
        <p><?php echo htmlspecialchars($row['name']); ?></p>
        <p>Price: <?php echo "RP " . htmlspecialchars($row['price']); ?></p>
        <form method="post" action="cartshop.php">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="hidden" name="action" value="add">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
    <?php
        }
    }
    ?>
</div>

</body>
</html>