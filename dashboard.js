// dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    // Fetch order data using AJAX or Fetch API
    // Example:
    fetch('/api/orders')
        .then(response => response.json())
        .then(data => {
            // Update the charts with fetched data
            createSalesChart(data.sales);
            createProductChart(data.products);
        });

    function createSalesChart(salesData) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.dates,
                datasets: [{
                    label: 'Sales',
                    data: salesData.amounts,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            }
        });
    }

    function createProductChart(productData) {
        const ctx = document.getElementById('productsChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: productData.names,
                datasets: [{
                    label: 'Products Sold',
                    data: productData.units,
                    backgroundColor: ['red', 'blue', 'yellow', 'green', 'purple']
                }]
            }
        });
    }
});
