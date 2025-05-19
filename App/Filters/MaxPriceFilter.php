<!-- App/Filters/MaxPriceFilter.php-->
<?php
require_once 'Filter.php';

class MaxPriceFilter implements Filter {
    private $max_price;

    public function __construct($max_price) {
        $this->max_price = $max_price;
    }

    public function apply(string &$sql, array &$params): void {
        $sql .= " AND price_per_day >= ?";
        $params[] = $this->max_price;
    }
}
?>