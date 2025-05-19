<!-- App/Filters/ColorFilter.php-->
<?php
require_once 'Filter.php';

class ColorFilter implements Filter {
    private $color;

    public function __construct($color) {
        $this->color = $color;
    }

    public function apply(string &$sql, array &$params): void {
        $sql .= " AND color = ?";
        $params[] = $this->color;
    }
}
?>