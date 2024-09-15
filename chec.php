<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }
        a {
            text-decoration: none;
            color: #a0468f;
        }
        h1, h2 {
            color: #a0468f;
        }
        /* Navbar */
        .navbar {
            background-color: #fff;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }
        .navbar ul li {
            display: inline-block;
        }
        .navbar-icons {
            display: flex;
            gap: 20px;
        }
        /* Checkout Page */
        .checkout-container {
            display: flex;
            justify-content: space-between;
            padding: 30px 10%;
            gap: 40px;
        }
        .billing-details, .order-summary {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .billing-details {
            width: 60%;
        }
        .order-summary {
            width: 35%;
        }
        /* Form Fields */
        .billing-details input, .billing-details select, .billing-details textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .billing-details label {
            font-weight: bold;
        }
        /* Order Summary Table */
        .order-summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-summary table th, .order-summary table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .order-summary table th {
            text-align: left;
        }
        .order-summary .total {
            font-weight: bold;
            font-size: 18px;
        }
        /* Payment Section */
        .payment-methods {
            margin-top: 20px;
        }
        /* Footer */
        .footer {
            background-color: #333;
            color: #fff;
            padding: 40px 10%;
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        .footer-column {
            width: 23%;
        }
        .footer-column h3 {
            color: #fff;
            margin-bottom: 15px;
        }
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        .footer-column ul li {
            margin-bottom: 10px;
        }
        .footer-column ul li a {
            color: #ccc;
        }
        .footer-column ul li a:hover {
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-logo">
            <a href="#"><img src="img/cheddis.png" alt="Logo" width="150"></a>
        </div>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="products.php">Products</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="blog.php">Blog</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <div class="navbar-icons">
            <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
            <a href="login.php"><i class="fas fa-user-circle"></i></a>
        </div>
    </div>

    <!-- Checkout Page -->
    <div class="checkout-container">
        <!-- Billing Details -->
        <div class="billing-details">
            <h2>Billing details</h2>
            <form action="process_order.php" method="POST">
                <label for="first_name">First name <span>*</span></label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last name <span>*</span></label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="company_name">Company name (optional)</label>
                <input type="text" id="company_name" name="company_name">

                <label for="country">Country/Region <span>*</span></label>
                <select id="country" name="country" required>
                    <option value="">Select a country</option>
                    <option value="Nepal">Nepal</option>
                </select>

                <label for="address">Street address <span>*</span></label>
                <input type="text" id="address" name="address" required>

                <label for="city">Town / City <span>*</span></label>
                <input type="text" id="city" name="city" required>

                <label for="state">State / Zone <span>*</span></label>
                <input type="text" id="state" name="state" required>

                <label for="postcode">Postcode / ZIP</label>
                <input type="text" id="postcode" name="postcode">

                <label for="phone">Phone <span>*</span></label>
                <input type="text" id="phone" name="phone" required>

                <label for="email">Email address <span>*</span></label>
                <input type="email" id="email" name="email" required>

                <label for="notes">Order notes (optional)</label>
                <textarea id="notes" name="notes" placeholder="Notes about your order, e.g., special notes for delivery"></textarea>

                <button type="submit">Place order</button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Your order</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Jelly Fruit Lollipop × 1</td>
                        <td>₨100.00</td>
                    </tr>
                    <tr>
                        <td>Orange Ball × 1</td>
                        <td>₨100.00</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th>Subtotal</th>
                        <td>₨200.00</td>
                    </tr>
                    <tr>
                        <th>Shipping</th>
                        <td>Free shipping</td>
                    </tr>
                    <tr class="total">
                        <th>Total</th>
                        <td>₨200.00</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Payment Methods -->
            <div class="payment-methods">
                <h3>Payment</h3>
                <label>
                    <input type="radio" name="payment_method" value="credit_card" checked> Credit/Debit Card
                </label>
                <div>
                    <input type="text" placeholder="Card number">
                    <input type="text" placeholder="MM/YY">
                    <input type="text" placeholder="CVC">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-column">
            <h3>About Us</h3>
            <p>We are here to provide you with the best quality candies and remind you of sweet memories.</p>
        </div>
        <div class="footer-column">
            <h3
