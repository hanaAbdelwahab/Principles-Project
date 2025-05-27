<!-- App/Filters/BrandFilter.php-->
<?php
require_once 'Filter.php';

class BrandFilter implements Filter {
    private $brand;

    public function __construct($brand) {
        $this->brand = $brand;
    }

    public function apply(string &$sql, array &$params): void {
        $sql .= " AND name = ?";
        $params[] = $this->brand;
    }
}
?>