<?php
require 'header.php';
require 'includes/database.php';
$conn = getDB();

if (isset($_GET['query'])) {
    $search_query = strtolower(htmlspecialchars($_GET['query']));
    
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
       
        $exact_matches = [];
        $substring_matches = [];
        $fuzzy_matches = [];

        while ($row = $result->fetch_assoc()) {
            $product_name = strtolower($row['name']);
            $product_desc = strtolower($row['description']);

           
            if ($product_name === $search_query || $product_desc === $search_query) {
                $row['match_type'] = 'exact';
                $exact_matches[] = $row;
                continue; 
            }
            if (str_contains($product_name, $search_query) || str_contains($product_desc, $search_query)) {
                $row['match_type'] = 'substring';
                $substring_matches[] = $row;
                continue;
            }
            $distance_name = levenshtein($search_query, $product_name);
            $distance_desc = levenshtein($search_query, $product_desc);

            $levenshtein_threshold = 5; // higher means exact accuracy
            if ($distance_name <= $levenshtein_threshold || $distance_desc <= $levenshtein_threshold) {
                $row['match_type'] = 'fuzzy';
                $row['levenshtein_score'] = min($distance_name, $distance_desc);
                $fuzzy_matches[] = $row;
            }
        }

       
        usort($fuzzy_matches, function ($a, $b) {
            return $a['levenshtein_score'] <=> $b['levenshtein_score'];
        });

        $all_matches = array_merge($exact_matches, $substring_matches, $fuzzy_matches);

        if (!empty($all_matches)) {
            echo '<div class="container">';
            echo '<h1>Search Results for "' . htmlspecialchars($search_query) . '"</h1>';
            echo '<div class="product-list">';
            foreach ($all_matches as $product) {
                echo '<div class="product-item">';
                echo '<a href="productinfo.php?product_id=' . htmlspecialchars($product['product_id']) . '">';
                echo '<img src="img/' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['name']) . '">';
                echo '</a>';
                echo '<h2><a href="productinfo.php?product_id=' . htmlspecialchars($product['product_id']) . '">' . htmlspecialchars($product['name']) . '</a></h2>';
                echo '<p>' . htmlspecialchars($product['description']) . '</p>';
                echo '<p class="price">Price: Rs. ' . number_format($product['price'], 2) . '</p>';
                echo '</div>';
            }
            echo '</div></div>';
        } else {
            echo '<p>No products found for "' . htmlspecialchars($search_query) . '".</p>';
        }
    } else {
        echo '<p>No products found in the database.</p>';
    }
}
?>
  <html>
    <link rel="stylesheet" href="styles.css">

<style>

.product-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.product-item {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}



.product-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-item h2 {
    font-size: 18px;
    margin: 15px 10px 5px;
    color: #333;
}


.product-item p {
    margin: 10px;
    font-size: 14px;
    color: #666;
}

.product-item .price {
    font-weight: bold;
    color: #000;
    margin: 10px;
}
</style>

</html>
