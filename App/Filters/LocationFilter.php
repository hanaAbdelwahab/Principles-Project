<!-- App/Filters/LocationFilter.php-->
<?php
require_once 'Filter.php';

class LocationFilter implements Filter {
    private $location;

    public function __construct($location) {
        $this->location = $location;
    }

    public function apply(string &$sql, array &$params): void {
        $sql .= " AND location = ?";
        $params[] = $this->location;
    }
}
?>