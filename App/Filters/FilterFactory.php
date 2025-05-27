<!--FilterFactory-->
<?php
require_once 'BrandFilter.php';
require_once 'ColorFilter.php';
require_once 'LocationFilter.php';
require_once 'MaxPriceFilter.php';

class FilterFactory {
    public function create(array $filters): array {
        $filterObjects = [];

        if (!empty($filters['brand'])) {
            $filterObjects[] = new BrandFilter($filters['brand']);
        }

        if (!empty($filters['location'])) {
            $filterObjects[] = new LocationFilter($filters['location']);
        }

        if (!empty($filters['color'])) {
            $filterObjects[] = new ColorFilter($filters['color']);
        }

        if (!empty($filters['max_price']) && $filters['max_price'] > 0) {
            $filterObjects[] = new MaxPriceFilter($filters['max_price']);
        }

        return $filterObjects;
    }
}
